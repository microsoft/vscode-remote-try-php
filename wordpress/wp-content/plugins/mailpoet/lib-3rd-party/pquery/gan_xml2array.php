<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing
/**
 * @author Niels A.D.
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2010 Niels A.D., 2014 Todd Burry
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL-2.1
 * @package pQuery
 */

namespace MailPoetVendor\pQuery;

if (!defined('ABSPATH')) exit;


/**
 * Converts a XML document to an array
 */
class XML2ArrayParser extends HtmlParserBase {

	/**
	 * Holds the document structure
	 * @var array array('name' => 'tag', 'attrs' => array('attr' => 'val'), 'childen' => array())
	 */
	var $root = array(
		'name' => '',
		'attrs' => array(),
		'children' => array()
	);

	/**
	 * Current parsing hierarchy
	 * @var array
	 * @access private
	 */
	var $hierarchy = array();

	protected function parse_hierarchy($self_close) {
		if ($this->status['closing_tag']) {
			$found = false;
			for ($count = count($this->hierarchy), $i = $count - 1; $i >= 0; $i--) {
				if (strcasecmp($this->hierarchy[$i]['name'], $this->status['tag_name']) === 0) {

					for($ii = ($count - $i - 1); $ii >= 0; $ii--) {
						$e = array_pop($this->hierarchy);
						if ($ii > 0) {
							$this->addError('Closing tag "'.$this->status['tag_name'].'" while "'.$e['name'].'" is not closed yet');
						}
					}

					$found = true;
					break;
				}
			}

			if (!$found) {
				$this->addError('Closing tag "'.$this->status['tag_name'].'" which is not open');
			}
		} else {
			$tag = array(
				'name' => $this->status['tag_name'],
				'attrs' => $this->status['attributes'],
				'children' => array()
			);
			if ($this->hierarchy) {
				$current =& $this->hierarchy[count($this->hierarchy) - 1];
				$current['children'][] = $tag;
				$tag =& $current['children'][count($current['children']) - 1];
				unset($current['tagData']);
			} else {
				$this->root = $tag;
				$tag =& $this->root;
				$self_close = false;
			}
			if (!$self_close) {
				$this->hierarchy[] =& $tag;
			}
		}
	}

	function parse_tag_default() {
		if (!parent::parse_tag_default()) {return false;}

		if ($this->status['tag_name'][0] !== '?') {
			$this->parse_hierarchy(($this->status['self_close']) ? true : null);
		}
		return true;
	}

	function parse_text() {
		parent::parse_text();
		if (($this->status['text'] !== '') && $this->hierarchy) {
			$current =& $this->hierarchy[count($this->hierarchy) - 1];
			if (!$current['children']) {
				$current['tagData'] = $this->status['text'];
			}
		}
	}

	function parse_all() {
		return ((parent::parse_all()) ? $this->root : false);
	}
}
