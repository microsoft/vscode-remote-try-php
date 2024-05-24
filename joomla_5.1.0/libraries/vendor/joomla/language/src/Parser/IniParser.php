<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Parser;

use Joomla\Language\DebugParserInterface;

/**
 * Language file parser for INI files
 *
 * @since  2.0.0-alpha
 */
class IniParser implements DebugParserInterface
{
    /**
     * Parse a file and check its contents for valid structure
     *
     * @param   string  $filename  The name of the file.
     *
     * @return  string[]  Array containing a list of errors
     *
     * @since   2.0.0-alpha
     */
    public function debugFile(string $filename): array
    {
        // Initialise variables for manually parsing the file for common errors.
        $blacklist = ['YES', 'NO', 'NULL', 'FALSE', 'ON', 'OFF', 'NONE', 'TRUE'];
        $errors    = [];

        // Open the file as a stream.
        foreach (new \SplFileObject($filename) as $lineNumber => $line) {
            // Avoid BOM error as BOM is OK when using parse_ini.
            if ($lineNumber == 0) {
                $line = str_replace("\xEF\xBB\xBF", '', $line);
            }

            $line = trim($line);

            // Ignore comment lines.
            if (!\strlen($line) || $line[0] == ';') {
                continue;
            }

            // Ignore grouping tag lines, like: [group]
            if (preg_match('#^\[[^\]]*\](\s*;.*)?$#', $line)) {
                continue;
            }

            $realNumber = $lineNumber + 1;

            // Check for any incorrect uses of _QQ_.
            if (strpos($line, '_QQ_') !== false) {
                $errors[] = 'The deprecated constant `_QQ_` is in use on line ' . $realNumber;

                continue;
            }

            // Check for odd number of double quotes.
            if (substr_count($line, '"') % 2 != 0) {
                $errors[] = 'There are an odd number of quotes on line ' . $realNumber;

                continue;
            }

            // Check that the line passes the necessary format.
            if (!preg_match('#^[A-Z][A-Z0-9_\*\-\.]*\s*=\s*".*"(\s*;.*)?$#', $line)) {
                $errors[] = 'The language key does not meet the required format on line ' . $realNumber;

                continue;
            }

            // Check that the key is not in the blacklist.
            $key = strtoupper(trim(substr($line, 0, strpos($line, '='))));

            if (\in_array($key, $blacklist)) {
                $errors[] = 'The language key "' . $key . '" is a blacklisted key on line ' . $realNumber;

                $errors[] = $realNumber;
            }
        }

        return $errors;
    }

    /**
     * Get the type of parser
     *
     * @return  string
     *
     * @since   2.0.0-alpha
     */
    public function getType(): string
    {
        return 'ini';
    }

    /**
     * Load the strings from a file
     *
     * @param   string  $filename  The name of the file.
     *
     * @return  string[]
     *
     * @since   2.0.0-alpha
     * @throws  \RuntimeException on a load/parse error
     */
    public function loadFile(string $filename): array
    {
        $result = @parse_ini_file($filename);

        if ($result === false) {
            $lastError = error_get_last();

            $errorMessage = $lastError['message'] ?? 'Unknown Error';

            throw new \RuntimeException(
                sprintf('Could not process file `%s`: %s', $errorMessage, $filename)
            );
        }

        return $result;
    }
}
