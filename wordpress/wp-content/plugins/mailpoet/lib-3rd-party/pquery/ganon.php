<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

if (!defined('ABSPATH')) exit;

/**
 * @author Niels A.D.
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2010 Niels A.D., 2014 Todd Burry
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL-2.1
 * @package pQuery
 */

use MailPoetVendor\pQuery\Html5Parser;
use MailPoetVendor\pQuery\HtmlFormatter;

/**
 * Returns HTML DOM from string
 * @param string $str
 * @param bool $return_root Return root node or return parser object
 * @return Html5Parser|DomNode
 */
function str_get_dom($str, $return_root = true) {
	$a = new Html5Parser($str);
	return (($return_root) ? $a->root : $a);
}

/**
 * Returns HTML DOM from file/website
 * @param string $str
 * @param bool $return_root Return root node or return parser object
 * @param bool $use_include_path Use include path search in file_get_contents
 * @param resource $context Context resource used in file_get_contents (PHP >= 5.0.0)
 * @return Html5Parser|DomNode
 */
function file_get_dom($file, $return_root = true, $use_include_path = false, $context = null) {
	if (version_compare(PHP_VERSION, '5.0.0', '>='))
		$f = file_get_contents($file, $use_include_path, $context);
	else {
		if ($context !== null)
			trigger_error('Context parameter not supported in this PHP version');
		$f = file_get_contents($file, $use_include_path);
	}

	return (($f === false) ? false : str_get_dom($f, $return_root));
}

/**
 * Format/beautify DOM
 * @param DomNode $root
 * @param array $options Extra formatting options {@link Formatter::$options}
 * @return bool
 */
function dom_format(&$root, $options = array()) {
	$formatter = new HtmlFormatter($options);
	return $formatter->format($root);
}

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	/**
	 * PHP alternative to str_split, for backwards compatibility
	 * @param string $string
	 * @return string
	 */
	function str_split($string) {
		$res = array();
		$size = strlen($string);
		for ($i = 0; $i < $size; $i++) {
			$res[] = $string[$i];
		}

		return $res;
	}
}

if (version_compare(PHP_VERSION, '5.2.0', '<')) {
	/**
	 * PHP alternative to array_fill_keys, for backwards compatibility
	 * @param array $keys
	 * @param mixed $value
	 * @return array
	 */
	function array_fill_keys($keys, $value) {
		$res = array();
		foreach($keys as $k) {
			$res[$k] = $value;
		}

		return $res;
	}
}

#!! <- Ignore when converting to single file
if (!defined('GANON_NO_INCLUDES')) {
	define('GANON_NO_INCLUDES', true);
    include_once('IQuery.php');
	include_once('gan_tokenizer.php');
	include_once('gan_parser_html.php');
	include_once('gan_node_html.php');
	include_once('gan_selector_html.php');
	include_once('gan_formatter.php');
}
#!
