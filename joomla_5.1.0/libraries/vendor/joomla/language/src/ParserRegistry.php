<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Registry of file parsers
 *
 * @since  2.0.0-alpha
 */
class ParserRegistry
{
    /**
     * A map of the registered parsers
     *
     * @var    ParserInterface[]
     * @since  2.0.0-alpha
     */
    private $parserMap = [];

    /**
     * Register a parser, overridding a previously registered parser for the given type
     *
     * @param   ParserInterface  $parser  The parser to registery
     *
     * @return  void
     *
     * @since   2.0.0-alpha
     */
    public function add(ParserInterface $parser): void
    {
        $this->parserMap[$parser->getType()] = $parser;
    }

    /**
     * Get the parser for a given type
     *
     * @param   string  $type  The parser type to retrieve
     *
     * @return  ParserInterface
     *
     * @since   2.0.0-alpha
     */
    public function get(string $type): ParserInterface
    {
        if (!$this->has($type)) {
            throw new \InvalidArgumentException(sprintf('There is not a parser registered for the `%s` type.', $type));
        }

        return $this->parserMap[$type];
    }

    /**
     * Check if a parser is registered for the given type
     *
     * @param   string  $type  The parser type to check (typically the file extension)
     *
     * @return  boolean
     *
     * @since   2.0.0-alpha
     */
    public function has(string $type): bool
    {
        return isset($this->parserMap[$type]);
    }
}
