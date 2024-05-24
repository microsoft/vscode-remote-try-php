<?php

namespace Algo26\IdnaConvert\Punycode;

use Algo26\IdnaConvert\Exception\InvalidCharacterException;
use OutOfBoundsException;

class FromPunycode extends AbstractPunycode implements PunycodeInterface
{
    public function __construct(?string $idnVersion = null)
    {
        parent::__construct();
    }

    /**
     * The actual decoding algorithm
     *
     * @param string
     *
     * @return mixed
     * @throws InvalidCharacterException
     */
    public function convert($encoded)
    {
        if (!$this->validate($encoded)) {
            return false;
        }

        $decoded = [];
        // Find last occurrence of the delimiter
        $delimiterPosition = strrpos($encoded, '-');
        if ($delimiterPosition > self::byteLength(self::punycodePrefix)) {
            for ($k = $this->byteLength(self::punycodePrefix); $k < $delimiterPosition; ++$k) {
                $decoded[] = ord($encoded[$k]);
            }
        }
        $decodedLength = count($decoded);
        $encodedLength = $this->byteLength($encoded);

        // Walking through the strings; init
        $isFirst = true;
        $bias = self::initialBias;
        $currentIndex = 0;
        $char = self::initialN;

        $startOfLoop = ($delimiterPosition) ? ($delimiterPosition + 1) : 0;
        for ($encodedIndex = $startOfLoop; $encodedIndex < $encodedLength; ++$decodedLength) {
            for ($oldIndex = $currentIndex, $w = 1, $k = self::base; 1; $k += self::base) {
                if ($encodedIndex + 1 > $encodedLength) {
                    throw new InvalidCharacterException('trying to read beyond input length');
                }
                $digit = $this->decodeDigit($encoded[$encodedIndex++]);

                if ($digit >= self::base) {
                    throw new InvalidCharacterException(
                        sprintf(
                            'encountered invalid digit at #%d',
                            $encodedIndex - 1
                        )
                    );
                }

                if ($digit > floor((PHP_INT_MAX - $currentIndex) / $w)) {
                    throw new OutOfBoundsException(
                        sprintf(
                            'overflow at #%d',
                            $encodedIndex - 1
                        )
                    );
                }

                $currentIndex += $digit * $w;
                $t = ($k <= $bias)
                    ? self::tMin
                    : (
                        ($k >= $bias + self::tMax)
                            ? self::tMax
                            : ($k - $bias)
                    );
                if ($digit < $t) {
                    break;
                }
                $w = (int) ($w * (self::base - $t));
            }
            $bias = $this->adapt($currentIndex - $oldIndex, $decodedLength + 1, $isFirst);
            $isFirst = false;
            $char += (int) ($currentIndex / ($decodedLength + 1));
            $currentIndex %= ($decodedLength + 1);
            if ($decodedLength > 0) {
                // Make room for the decoded char
                for ($i = $decodedLength; $i > $currentIndex; $i--) {
                    $decoded[$i] = $decoded[($i - 1)];
                }
            }
            $decoded[$currentIndex++] = $char;
        }

        return $this->unicodeTransCoder->convert(
            $decoded,
            $this->unicodeTransCoder::FORMAT_UCS4_ARRAY,
            $this->unicodeTransCoder::FORMAT_UTF8
        );
    }


    /**
     * Checks, whether the provided string is a valid punycode string
     * @param string $encoded
     * @return boolean
     */
    private function validate($encoded): bool
    {
        // Check for existence of the prefix
        if (strpos($encoded, self::punycodePrefix) !== 0) {
            return false;
        }

        // If nothing is left after the prefix, it is hopeless
        if (strlen(trim($encoded)) <= strlen(self::punycodePrefix)) {
            return false;
        }

        return true;
    }

    private function decodeDigit(string $codePoint): int
    {
        $codeAsInt = ord($codePoint);

        if ($codeAsInt - 48 < 10) {
            return $codeAsInt - 22;
        }

        if ($codeAsInt - 65 < 26) {
            return $codeAsInt - 65;
        }

        if ($codeAsInt - 97 < 26) {
            return $codeAsInt - 97;
        }

        return self::base;
    }
}
