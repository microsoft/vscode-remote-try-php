<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Languages/translation handler class
 *
 * @since  1.0
 */
class Language
{
    /**
     * Debug language, If true, highlights if string isn't found.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $debug = false;

    /**
     * The default language, used when a language file in the requested language does not exist.
     *
     * @var    string
     * @since  1.0
     */
    protected $default = 'en-GB';

    /**
     * An array of orphaned text.
     *
     * @var    array
     * @since  1.0
     */
    protected $orphans = [];

    /**
     * Array holding the language metadata.
     *
     * @var    array
     * @since  1.0
     */
    protected $metadata;

    /**
     * Array holding the language locale or boolean null if none.
     *
     * @var    array|boolean
     * @since  1.0
     */
    protected $locale;

    /**
     * The language to load.
     *
     * @var    string
     * @since  1.0
     */
    protected $lang;

    /**
     * A nested array of language files that have been loaded
     *
     * @var    array
     * @since  1.0
     */
    protected $paths = [];

    /**
     * List of language files that are in error state
     *
     * @var    array
     * @since  1.0
     */
    protected $errorfiles = [];

    /**
     * An array of used text, used during debugging.
     *
     * @var    array
     * @since  1.0
     */
    protected $used = [];

    /**
     * Counter for number of loads.
     *
     * @var    integer
     * @since  1.0
     */
    protected $counter = 0;

    /**
     * An array used to store overrides.
     *
     * @var    array
     * @since  1.0
     */
    protected $override = [];

    /**
     * The localisation object.
     *
     * @var    LocaliseInterface
     * @since  2.0.0-alpha
     */
    protected $localise;

    /**
     * LanguageHelper object
     *
     * @var    LanguageHelper
     * @since  2.0.0-alpha
     */
    protected $helper;

    /**
     * The base path to the language folder
     *
     * @var    string
     * @since  2.0.0-alpha
     */
    protected $basePath;

    /**
     * MessageCatalogue object
     *
     * @var    MessageCatalogue
     * @since  2.0.0-alpha
     */
    protected $catalogue;

    /**
     * Language parser registry
     *
     * @var    ParserRegistry
     * @since  2.0.0-alpha
     */
    protected $parserRegistry;

    /**
     * Constructor activating the default information of the language.
     *
     * @param   ParserRegistry  $parserRegistry  A registry containing the supported file parsers
     * @param   string          $path            The base path to the language folder
     * @param   string          $lang            The language
     * @param   boolean         $debug           Indicates if language debugging is enabled
     *
     * @since   1.0
     */
    public function __construct(ParserRegistry $parserRegistry, string $path, string $lang = '', bool $debug = false)
    {
        if (empty($path)) {
            throw new \InvalidArgumentException(
                'The $path variable cannot be empty when creating a new Language object'
            );
        }

        $this->basePath = $path;
        $this->helper   = new LanguageHelper();

        $this->lang = $lang ?: $this->default;

        $this->metadata = $this->helper->getMetadata($this->lang, $this->basePath);
        $this->setDebug($debug);

        $this->parserRegistry = $parserRegistry;

        $basePath = $this->helper->getLanguagePath($this->basePath);

        $filename = $basePath . "/overrides/$lang.override.ini";

        if (file_exists($filename) && $contents = $this->parse($filename)) {
            if (\is_array($contents)) {
                // Sort the underlying heap by key values to optimize merging
                ksort($contents, SORT_STRING);
                $this->override = $contents;
            }

            unset($contents);
        }

        // Grab a localisation file
        $this->localise = (new LanguageFactory())->getLocalise($lang, $path);

        $this->catalogue = new MessageCatalogue($this->lang);

        $this->load();
    }

    /**
     * Translate function, mimics the php gettext (alias _) function.
     *
     * The function checks if $jsSafe is true, then if $interpretBackslashes is true.
     *
     * @param   string   $string                The string to translate
     * @param   boolean  $jsSafe                Make the result JavaScript safe
     * @param   boolean  $interpretBackSlashes  Interpret \t and \n
     *
     * @return  string  The translation of the string
     *
     * @see     Language::translate()
     * @since   1.0
     * @deprecated  3.0  Use translate instead
     */
    public function _($string, $jsSafe = false, $interpretBackSlashes = true)
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::translate() instead.',
            __METHOD__,
            self::class
        );

        return $this->translate((string) $string, (bool) $jsSafe, (bool) $interpretBackSlashes);
    }

    /**
     * Translate function, mimics the php gettext (alias _) function.
     *
     * The function checks if $jsSafe is true, then if $interpretBackslashes is true.
     *
     * @param   string   $string                The string to translate
     * @param   boolean  $jsSafe                Make the result JavaScript safe
     * @param   boolean  $interpretBackSlashes  Interpret \t and \n
     *
     * @return  string  The translation of the string
     *
     * @since   2.0.0-alpha
     */
    public function translate(string $string, bool $jsSafe = false, bool $interpretBackSlashes = true): string
    {
        // Detect empty string
        if ($string == '') {
            return '';
        }

        $key = strtoupper($string);

        if ($this->catalogue->hasMessage($key)) {
            $string = $this->debug ? '**' . $this->catalogue->getMessage($key) . '**' : $this->catalogue->getMessage($key);

            // Store debug information
            if ($this->debug) {
                $caller = $this->getCallerInfo();

                if (!array_key_exists($key, $this->used)) {
                    $this->used[$key] = [];
                }

                $this->used[$key][] = $caller;
            }
        } else {
            if ($this->debug) {
                $caller           = $this->getCallerInfo();
                $caller['string'] = $string;

                if (!array_key_exists($key, $this->orphans)) {
                    $this->orphans[$key] = [];
                }

                $this->orphans[$key][] = $caller;

                $string = '??' . $string . '??';
            }
        }

        if ($jsSafe) {
            // JavaScript filter
            $string = addslashes($string);
        } elseif ($interpretBackSlashes) {
            if (strpos($string, '\\') !== false) {
                // Interpret \n and \t characters
                $string = str_replace(['\\\\', '\t', '\n'], ['\\', "\t", "\n"], $string);
            }
        }

        return $string;
    }

    /**
     * Transliterate function
     *
     * This method processes a string and replaces all accented UTF-8 characters by unaccented ASCII-7 "equivalents".
     *
     * @param   string  $string  The string to transliterate.
     *
     * @return  string  The transliteration of the string.
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function transliterate($string)
    {
        $string = $this->localise->transliterate($string);

        // The transliterate method can return false if there isn't a fully valid UTF-8 string entered
        if ($string === false) {
            throw new \RuntimeException('Invalid UTF-8 was detected in the string "%s"', $string);
        }

        return $string;
    }

    /**
     * Returns an array of suffixes for plural rules.
     *
     * @param   integer  $count  The count number the rule is for.
     *
     * @return  string[]  The array of suffixes.
     *
     * @since   1.0
     */
    public function getPluralSuffixes($count)
    {
        return $this->localise->getPluralSuffixes($count);
    }

    /**
     * Checks if a language exists.
     *
     * This is a simple, quick check for the directory that should contain language files for the given user.
     *
     * @param   string  $lang      Language to check.
     * @param   string  $basePath  Optional path to check.
     *
     * @return  boolean  True if the language exists.
     *
     * @see     LanguageHelper::exists()
     * @since   1.0
     * @deprecated  3.0  Use LanguageHelper::exists() instead
     */
    public static function exists($lang, $basePath = '')
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::exists() instead.',
            __METHOD__,
            LanguageHelper::class
        );

        return (new LanguageHelper())->exists($lang, $basePath);
    }

    /**
     * Loads a single language file and appends the results to the existing strings
     *
     * @param   string   $extension  The extension for which a language file should be loaded.
     * @param   string   $basePath   The basepath to use.
     * @param   string   $lang       The language to load, default null for the current language.
     * @param   boolean  $reload     Flag that will force a language to be reloaded if set to true.
     *
     * @return  boolean  True if the file has successfully loaded.
     *
     * @since   1.0
     */
    public function load($extension = 'joomla', $basePath = '', $lang = null, $reload = false)
    {
        $lang     = $lang ?: $this->lang;
        $basePath = $basePath ?: $this->basePath;

        $path = $this->helper->getLanguagePath($basePath, $lang);

        $internal = $extension == 'joomla' || $extension == '';
        $filename = $internal ? $lang : $lang . '.' . $extension;
        $filename = "$path/$filename.ini";

        if (isset($this->paths[$extension][$filename]) && !$reload) {
            // This file has already been tested for loading.
            return $this->paths[$extension][$filename];
        }

        // Load the language file
        return $this->loadLanguage($filename, $extension);
    }

    /**
     * Loads a language file.
     *
     * This method will not note the successful loading of a file - use load() instead.
     *
     * @param   string  $filename   The name of the file.
     * @param   string  $extension  The name of the extension.
     *
     * @return  boolean  True if new strings have been added to the language
     *
     * @see     Language::load()
     * @since   1.0
     */
    protected function loadLanguage($filename, $extension = 'unknown')
    {
        $this->counter++;

        $result  = false;
        $strings = false;

        if (file_exists($filename)) {
            $strings = $this->parse($filename);
        }

        if ($strings) {
            if (\is_array($strings) && \count($strings)) {
                $this->catalogue->addMessages(array_replace($strings, $this->override));
                $result = true;
            }
        }

        // Record the result of loading the extension's file.
        if (!isset($this->paths[$extension])) {
            $this->paths[$extension] = [];
        }

        $this->paths[$extension][$filename] = $result;

        return $result;
    }

    /**
     * Parses a language file.
     *
     * @param   string  $filename  The name of the file.
     *
     * @return  array  The array of parsed strings.
     *
     * @since   1.0
     */
    protected function parse($filename)
    {
        // Capture hidden PHP errors from the parsing.
        if ($this->debug) {
            // See https://www.php.net/manual/en/reserved.variables.phperrormsg.php
            $php_errormsg = null;
            $trackErrors  = ini_get('track_errors');
            ini_set('track_errors', true);
        }

        try {
            $strings = $this->parserRegistry->get(pathinfo($filename, PATHINFO_EXTENSION))->loadFile($filename);
        } catch (\RuntimeException $exception) {
            // TODO - This shouldn't be absorbed
            $strings = [];
        }

        if ($this->debug) {
            // Restore error tracking to what it was before.
            ini_set('track_errors', $trackErrors);

            $this->debugFile($filename);
        }

        return \is_array($strings) ? $strings : [];
    }

    /**
     * Debugs a language file
     *
     * @param   string  $filename  Absolute path to the file to debug
     *
     * @return  integer  A count of the number of parsing errors
     *
     * @since   2.0.0-alpha
     */
    public function debugFile(string $filename): int
    {
        // Make sure our file actually exists
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException(
                sprintf('Unable to locate file "%s" for debugging', $filename)
            );
        }

        // Initialise variables for manually parsing the file for common errors.
        $debug        = $this->setDebug(false);
        $php_errormsg = null;

        $parser = $this->parserRegistry->get(pathinfo($filename, PATHINFO_EXTENSION));

        if (!($parser instanceof DebugParserInterface)) {
            return 0;
        }

        $errors = $parser->debugFile($filename);

        // Check if we encountered any errors.
        if (\count($errors)) {
            $this->errorfiles[$filename] = $filename . ' - error(s) ' . implode(', ', $errors);
        } elseif ($php_errormsg) {
            // We didn't find any errors but there's probably a parse notice.
            $this->errorfiles['PHP' . $filename] = 'PHP parser errors -' . $php_errormsg;
        }

        $this->setDebug($debug);

        return \count($errors);
    }

    /**
     * Get a metadata language property.
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $default   The default value.
     *
     * @return  mixed  The value of the property.
     *
     * @since   1.0
     */
    public function get($property, $default = null)
    {
        return $this->metadata[$property] ?? $default;
    }

    /**
     * Get the base path for the instance.
     *
     * @return  string
     *
     * @since   2.0.0-alpha
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Determine who called Language or Text.
     *
     * @return  mixed  Caller information or null if unavailable
     *
     * @since   1.0
     */
    protected function getCallerInfo()
    {
        // Try to determine the source if none was provided
        if (!\function_exists('debug_backtrace')) {
            return;
        }

        $backtrace = debug_backtrace();
        $info      = [];

        // Search through the backtrace to our caller
        $continue = true;

        while ($continue && next($backtrace)) {
            $step  = current($backtrace);
            $class = @ $step['class'];

            // We're looking for something outside of language.php
            if ($class != __CLASS__ && $class != Text::class) {
                $info['function'] = @ $step['function'];
                $info['class']    = $class;
                $info['step']     = prev($backtrace);

                // Determine the file and name of the file
                $info['file'] = @ $step['file'];
                $info['line'] = @ $step['line'];

                $continue = false;
            }
        }

        return $info;
    }

    /**
     * Getter for Name.
     *
     * @return  string  Official name element of the language.
     *
     * @since   1.0
     */
    public function getName()
    {
        return $this->metadata['name'];
    }

    /**
     * Get a list of language files that have been loaded.
     *
     * @param   string  $extension  An optional extension name.
     *
     * @return  array
     *
     * @since   1.0
     */
    public function getPaths($extension = null)
    {
        if (isset($extension)) {
            return $this->paths[$extension] ?? null;
        }

        return $this->paths;
    }

    /**
     * Get a list of language files that are in error state.
     *
     * @return  array
     *
     * @since   1.0
     */
    public function getErrorFiles()
    {
        return $this->errorfiles;
    }

    /**
     * Getter for the language tag (as defined in RFC 3066)
     *
     * @return  string  The language tag.
     *
     * @since   1.0
     */
    public function getTag()
    {
        return $this->metadata['tag'];
    }

    /**
     * Get the RTL property.
     *
     * @return  boolean  True is it an RTL language.
     *
     * @since   1.0
     */
    public function isRtl()
    {
        return (bool) $this->metadata['rtl'];
    }

    /**
     * Set the Debug property.
     *
     * @param   boolean  $debug  The debug setting.
     *
     * @return  boolean  Previous value.
     *
     * @since   1.0
     */
    public function setDebug($debug)
    {
        $previous    = $this->debug;
        $this->debug = (bool) $debug;

        return $previous;
    }

    /**
     * Get the Debug property.
     *
     * @return  boolean  True is in debug mode.
     *
     * @since   1.0
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Get the default language code.
     *
     * @return  string  Language code.
     *
     * @since   1.0
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the default language code.
     *
     * @param   string  $lang  The language code.
     *
     * @return  string  Previous value.
     *
     * @since   1.0
     */
    public function setDefault($lang)
    {
        $previous      = $this->default;
        $this->default = $lang;

        return $previous;
    }

    /**
     * Get the list of orphaned strings if being tracked.
     *
     * @return  array  Orphaned text.
     *
     * @since   1.0
     */
    public function getOrphans()
    {
        return $this->orphans;
    }

    /**
     * Get the list of used strings.
     *
     * Used strings are those strings requested and found either as a string or a constant.
     *
     * @return  array  Used strings.
     *
     * @since   1.0
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Determines is a key exists.
     *
     * @param   string  $string  The key to check.
     *
     * @return  boolean  True, if the key exists.
     *
     * @since   1.0
     */
    public function hasKey($string)
    {
        return $this->catalogue->hasMessage($string);
    }

    /**
     * Returns a associative array holding the metadata.
     *
     * @param   string  $lang      The name of the language.
     * @param   string  $basePath  The filepath to the language folder.
     *
     * @return  mixed  If $lang exists return key/value pair with the language metadata, otherwise return NULL.
     *
     * @see     LanguageHelper::getMetadata()
     * @since   1.0
     * @deprecated  3.0  Use LanguageHelper::getMetadata() instead
     */
    public static function getMetadata($lang, $basePath)
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::getMetadata() instead.',
            __METHOD__,
            LanguageHelper::class
        );

        return (new LanguageHelper())->getMetadata($lang, $basePath);
    }

    /**
     * Returns a list of known languages for an area
     *
     * @param   string  $basePath  The basepath to use
     *
     * @return  array  key/value pair with the language file and real name.
     *
     * @see     LanguageHelper::getKnownLanguages()
     * @since   1.0
     * @deprecated  3.0  Use LanguageHelper::getKnownLanguages() instead
     */
    public static function getKnownLanguages($basePath = '')
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::getKnownLanguages() instead.',
            __METHOD__,
            LanguageHelper::class
        );

        return (new LanguageHelper())->getKnownLanguages($basePath);
    }

    /**
     * Get the path to a language
     *
     * @param   string  $basePath  The basepath to use.
     * @param   string  $language  The language tag.
     *
     * @return  string  language related path or null.
     *
     * @see     LanguageHelper::getLanguagePath()
     * @since   1.0
     * @deprecated  3.0  Use LanguageHelper::getLanguagePath() instead
     */
    public static function getLanguagePath($basePath = '', $language = '')
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::getLanguagePath() instead.',
            __METHOD__,
            LanguageHelper::class
        );

        return (new LanguageHelper())->getLanguagePath($basePath, $language);
    }

    /**
     * Get the current language code.
     *
     * @return  string  The language code
     *
     * @since   1.0
     */
    public function getLanguage()
    {
        return $this->lang;
    }

    /**
     * Get the message catalogue for the language.
     *
     * @return  MessageCatalogue
     *
     * @since   2.0.0-alpha
     */
    public function getCatalogue(): MessageCatalogue
    {
        return $this->catalogue;
    }

    /**
     * Set the message catalogue for the language.
     *
     * @param   MessageCatalogue  $catalogue  The message catalogue to use.
     *
     * @return  void
     *
     * @since   2.0.0-alpha
     */
    public function setCatalogue(MessageCatalogue $catalogue): void
    {
        $this->catalogue = $catalogue;
    }

    /**
     * Get the language locale based on current language.
     *
     * @return  array  The locale according to the language.
     *
     * @since   1.0
     */
    public function getLocale()
    {
        if (!isset($this->locale)) {
            $locale = str_replace(' ', '', $this->metadata['locale'] ?? '');

            $this->locale = $locale ? explode(',', $locale) : false;
        }

        return $this->locale;
    }

    /**
     * Get the first day of the week for this language.
     *
     * @return  integer  The first day of the week according to the language
     *
     * @since   1.0
     */
    public function getFirstDay()
    {
        return (int) ($this->metadata['firstDay'] ?? 0);
    }

    /**
     * Get the weekends days for this language.
     *
     * @return  string  The weekend days of the week separated by a comma according to the language
     *
     * @since   2.0.0-alpha
     */
    public function getWeekEnd(): string
    {
        return $this->metadata['weekEnd'] ?? '0,6';
    }

    /**
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     *
     * @return  array  Array holding the found languages as filename => real name pairs.
     *
     * @see     LanguageHelper::parseLanguageFiles()
     * @since   1.0
     * @deprecated  3.0  Use LanguageHelper::parseLanguageFiles() instead
     */
    public static function parseLanguageFiles($dir = null)
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::parseLanguageFiles() instead.',
            __METHOD__,
            LanguageHelper::class
        );

        return (new LanguageHelper())->parseLanguageFiles($dir);
    }

    /**
     * Parse XML file for language information.
     *
     * @param   string  $path  Path to the XML files.
     *
     * @return  mixed  Array holding the found metadata as a key => value pair or null on an invalid XML file
     *
     * @see     LanguageHelper::parseXMLLanguageFile()
     * @since   1.0
     * @deprecated  3.0  Use LanguageHelper::parseXMLLanguageFile() instead
     */
    public static function parseXmlLanguageFile($path)
    {
        trigger_deprecation(
            'joomla/language',
            '2.0.0',
            '%s() is deprecated and will be removed in 3.0, use %s::parseXmlLanguageFile() instead.',
            __METHOD__,
            LanguageHelper::class
        );

        return (new LanguageHelper())->parseXMLLanguageFile($path);
    }
}
