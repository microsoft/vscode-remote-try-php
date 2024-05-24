<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Language package factory
 *
 * @since  1.3.0
 */
class LanguageFactory
{
    /**
     * Application's default language
     *
     * @var    string
     * @since  1.3.0
     */
    private $defaultLanguage = 'en-GB';

    /**
     * Path to the directory containing the application's language folder
     *
     * @var    string
     * @since  2.0.0-alpha
     */
    private $languageDirectory = '';

    /**
     * Get the application's default language.
     *
     * @return  string
     *
     * @since   1.3.0
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Creates a new Language instance based on the given parameters.
     *
     * @param   string   $lang   The language to use.
     * @param   string   $path   The base path to the language folder.  This is required if creating a new instance.
     * @param   boolean  $debug  The debug mode.
     *
     * @return  Language
     *
     * @since   1.3.0
     */
    public function getLanguage($lang = '', $path = '', $debug = false)
    {
        $path = $path ?: $this->getLanguageDirectory();
        $lang = $lang ?: $this->getDefaultLanguage();

        $loaderRegistry = new ParserRegistry();
        $loaderRegistry->add(new Parser\IniParser());

        return new Language($loaderRegistry, $path, $lang, $debug);
    }

    /**
     * Get the path to the directory containing the application's language folder.
     *
     * @return  string
     *
     * @since   1.3.0
     */
    public function getLanguageDirectory()
    {
        return $this->languageDirectory;
    }

    /**
     * Creates a new LocaliseInterface instance for the language.
     *
     * @param   string  $lang      Language to check.
     * @param   string  $basePath  Base path to the language folder.
     *
     * @return  LocaliseInterface
     *
     * @since   2.0.0-alpha
     */
    public function getLocalise(string $lang, string $basePath = ''): LocaliseInterface
    {
        /*
         * Look for a language specific localise class
         *
         * LocaliseInterface classes are searched for in the global namespace and are named based
         * on the language code, replacing hyphens with underscores (i.e. en-GB looks for En_GBLocalise)
         */
        $class = str_replace('-', '_', $lang . 'Localise');

        // If this class already exists, no need to try and find it
        if (class_exists($class)) {
            return new $class();
        }

        $paths = [];

        $basePath = $basePath ?: $this->getLanguageDirectory();

        // Get the LanguageHelper to set the proper language directory
        $basePath = (new LanguageHelper())->getLanguagePath($basePath);

        // Explicitly set the keys to define the lookup order
        $paths[0] = $basePath . "/overrides/$lang.localise.php";
        $paths[1] = $basePath . "/$lang/$lang.localise.php";

        ksort($paths);
        $path = reset($paths);

        while (!class_exists($class) && $path) {
            if (file_exists($path)) {
                require_once $path;
            }

            $path = next($paths);
        }

        // If we have found a match initialise it and return it
        if (class_exists($class)) {
            return new $class();
        }

        // Return the en_GB class if no specific instance is found
        return new Localise\En_GBLocalise();
    }

    /**
     * Creates a new StemmerInterface instance for the requested adapter.
     *
     * @param   string  $adapter  The type of stemmer to load.
     *
     * @return  StemmerInterface
     *
     * @since   1.3.0
     * @throws  \RuntimeException on invalid stemmer
     */
    public function getStemmer($adapter)
    {
        // Setup the adapter for the stemmer.
        $class = __NAMESPACE__ . '\\Stemmer\\' . ucfirst(trim($adapter));

        // Check if a stemmer exists for the adapter.
        if (!class_exists($class)) {
            // Throw invalid adapter exception.
            throw new \RuntimeException(sprintf('Invalid stemmer type %s', $class));
        }

        return new $class();
    }

    /**
     * Retrieves a new Text object for a Language instance
     *
     * @param   Language|null  $language  An optional Language object to inject, otherwise the default object is loaded
     *
     * @return  Text
     *
     * @since   2.0.0-alpha
     */
    public function getText(?Language $language = null): Text
    {
        $language = $language ?: $this->getLanguage();

        return new Text($language);
    }

    /**
     * Set the application's default language
     *
     * @param   string  $language  Language code for the application's default language
     *
     * @return  $this
     *
     * @since   1.3.0
     */
    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;

        return $this;
    }

    /**
     * Set the path to the directory containing the application's language folder
     *
     * @param   string  $directory  Path to the application's language folder
     *
     * @return  $this
     *
     * @since   2.0.0-alpha
     */
    public function setLanguageDirectory(string $directory): self
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot set language directory to "%s" since the directory does not exist.',
                    $directory
                )
            );
        }

        $this->languageDirectory = $directory;

        return $this;
    }
}
