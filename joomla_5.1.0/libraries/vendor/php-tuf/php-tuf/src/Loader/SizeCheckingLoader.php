<?php

namespace Tuf\Loader;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use Tuf\Exception\DownloadSizeException;

/**
 * A data loader that enforces a size limit on the output of another loader.
 */
class SizeCheckingLoader implements LoaderInterface
{
    public function __construct(private LoaderInterface $decorated)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $locator, int $maxBytes, bool $exact = false): PromiseInterface
    {
        $checkSize = function (StreamInterface $data) use ($locator, $maxBytes, $exact) {
            $size = $this->getSize($data, $maxBytes);
            // If we're doing an exact size check, the stream MUST be exactly $maxBytes in size.
            if ($exact) {
                if ($size !== $maxBytes) {
                    throw new DownloadSizeException("Expected $locator to be $maxBytes bytes.");
                }
            } elseif ($size > $maxBytes) {
                throw new DownloadSizeException("$locator exceeded $maxBytes bytes");
            }
            return $data;
        };
        return $this->decorated->load($locator, $maxBytes)->then($checkSize);
    }

    /**
     * Tries to determine the size of a stream, up to $maxBytes.
     *
     * This method has intentional side effects. If the stream isn't seekable
     * and its size is unknown, up to $maxBytes of the incoming data will be
     * copied into a new stream, which will be referenced by the $stream
     * parameter. Data that was already in the original stream will be lost!
     *
     * @param \Psr\Http\Message\StreamInterface $stream
     *   A data stream.
     * @param int $maxBytes
     *   The maximum number of bytes that will be read from the stream if its
     *   size is not known, and it's not seekable.
     *
     * @return int
     *   The estimated size of the stream.
     */
    private function getSize(StreamInterface &$stream, int $maxBytes): int
    {
        // If the stream knows how big it is, believe it.
        $size = $stream->getSize();
        if (isset($size)) {
            return $size;
        }

        // If the stream is seekable, skip to the end and tell where we are.
        if ($stream->isSeekable()) {
            // Remember our position, so we can go back there before returning.
            $originalPosition = $stream->tell();
            $stream->seek(0, SEEK_END);
            $size = $stream->tell();
            $stream->seek($originalPosition);
            return $size;
        }

        // If we get here, we truly don't know how much data we're dealing with.
        // So, treat everything that has already come through the stream as
        // untrusted, and read up to $maxBytes into a new stream whose size we
        // do trust.
        $buffer = Utils::tryFopen('php://temp', 'a');
        $replacementStream = Utils::streamFor($buffer);
        // Read $maxBytes + 1 to detect if there is any data in the stream
        // beyond $maxBytes (there shouldn't be).
        $size = $replacementStream->write($stream->read($maxBytes + 1));
        $stream = $replacementStream;
        $stream->rewind();
        return $size;
    }
}
