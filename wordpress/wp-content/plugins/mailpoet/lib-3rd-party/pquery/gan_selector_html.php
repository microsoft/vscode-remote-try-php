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
 * Tokenizes a css selector query
 */
class CSSQueryTokenizer extends TokenizerBase {

	/**
	 * Opening bracket token, used for "["
	 */
	const TOK_BRACKET_OPEN = 100;
	/**
	 * Closing bracket token, used for "]"
	 */
	const TOK_BRACKET_CLOSE = 101;
	/**
	 * Opening brace token, used for "("
	 */
	const TOK_BRACE_OPEN = 102;
	/**
	 * Closing brace token, used for ")"
	 */
	const TOK_BRACE_CLOSE = 103;
	/**
	 * String token
	 */
	const TOK_STRING = 104;
	/**
	 * Colon token, used for ":"
	 */
	const TOK_COLON = 105;
	/**
	 * Comma token, used for ","
	 */
	const TOK_COMMA = 106;
	/**
	 * "Not" token, used for "!"
	 */
	const TOK_NOT = 107;

	/**
	 * "All" token, used for "*" in query
	 */
	const TOK_ALL = 108;
	/**
	 * Pipe token, used for "|"
	 */
	const TOK_PIPE = 109;
	/**
	 * Plus token, used for "+"
	 */
	const TOK_PLUS = 110;
	/**
	 * "Sibling" token, used for "~" in query
	 */
	const TOK_SIBLING = 111;
	/**
	 * Class token, used for "." in query
	 */
	const TOK_CLASS = 112;
	/**
	 * ID token, used for "#" in query
	 */
	const TOK_ID = 113;
	/**
	 * Child token, used for ">" in query
	 */
	const TOK_CHILD = 114;

	/**
	 * Attribute compare prefix token, used for "|="
	 */
	const TOK_COMPARE_PREFIX = 115;
	/**
	 * Attribute contains token, used for "*="
	 */
	const TOK_COMPARE_CONTAINS = 116;
	/**
	 * Attribute contains word token, used for "~="
	 */
	const TOK_COMPARE_CONTAINS_WORD = 117;
	/**
	 * Attribute compare end token, used for "$="
	 */
	const TOK_COMPARE_ENDS = 118;
	/**
	 * Attribute equals token, used for "="
	 */
	const TOK_COMPARE_EQUALS = 119;
	/**
	 * Attribute not equal token, used for "!="
	 */
	const TOK_COMPARE_NOT_EQUAL = 120;
	/**
	 * Attribute compare bigger than token, used for ">="
	 */
	const TOK_COMPARE_BIGGER_THAN = 121;
	/**
	 * Attribute compare smaller than token, used for "<="
	 */
	const TOK_COMPARE_SMALLER_THAN = 122;
	/**
	 * Attribute compare with regex, used for "%="
	 */
	const TOK_COMPARE_REGEX = 123;
	/**
	 * Attribute compare start token, used for "^="
	 */
	const TOK_COMPARE_STARTS = 124;

	/**
	 * Sets query identifiers
	 * @see TokenizerBase::$identifiers
	 * @access private
	 */
	var $identifiers = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890_-?';

	/**
	 * Map characters to match their tokens
	 * @see TokenizerBase::$custom_char_map
	 * @access private
	 */
	var $custom_char_map = array(
		'.' => self::TOK_CLASS,
		'#' => self::TOK_ID,
		',' => self::TOK_COMMA,
		'>' => 'parse_gt',//self::TOK_CHILD,

		'+' => self::TOK_PLUS,
		'~' => 'parse_sibling',

		'|' => 'parse_pipe',
		'*' => 'parse_star',
		'$' => 'parse_compare',
		'=' => self::TOK_COMPARE_EQUALS,
		'!' => 'parse_not',
		'%' => 'parse_compare',
		'^' => 'parse_compare',
		'<' => 'parse_compare',

		'"' => 'parse_string',
		"'" => 'parse_string',
		'(' => self::TOK_BRACE_OPEN,
		')' => self::TOK_BRACE_CLOSE,
		'[' => self::TOK_BRACKET_OPEN,
		']' => self::TOK_BRACKET_CLOSE,
		':' => self::TOK_COLON
	);

	/**
	 * Parse ">" character
	 * @internal Could be {@link TOK_CHILD} or {@link TOK_COMPARE_BIGGER_THAN}
	 * @return int
	 */
	protected function parse_gt() {
		if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
			++$this->pos;
			return ($this->token = self::TOK_COMPARE_BIGGER_THAN);
		} else {
			return ($this->token = self::TOK_CHILD);
		}
	}

	/**
	 * Parse "~" character
	 * @internal Could be {@link TOK_SIBLING} or {@link TOK_COMPARE_CONTAINS_WORD}
	 * @return int
	 */
	protected function parse_sibling() {
		if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
			++$this->pos;
			return ($this->token = self::TOK_COMPARE_CONTAINS_WORD);
		} else {
			return ($this->token = self::TOK_SIBLING);
		}
	}

	/**
	 * Parse "|" character
	 * @internal Could be {@link TOK_PIPE} or {@link TOK_COMPARE_PREFIX}
	 * @return int
	 */
	protected function parse_pipe() {
		if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
			++$this->pos;
			return ($this->token = self::TOK_COMPARE_PREFIX);
		} else {
			return ($this->token = self::TOK_PIPE);
		}
	}

	/**
	 * Parse "*" character
	 * @internal Could be {@link TOK_ALL} or {@link TOK_COMPARE_CONTAINS}
	 * @return int
	 */
	protected function parse_star() {
		if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
			++$this->pos;
			return ($this->token = self::TOK_COMPARE_CONTAINS);
		} else {
			return ($this->token = self::TOK_ALL);
		}
	}

	/**
	 * Parse "!" character
	 * @internal Could be {@link TOK_NOT} or {@link TOK_COMPARE_NOT_EQUAL}
	 * @return int
	 */
	protected function parse_not() {
		if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
			++$this->pos;
			return ($this->token = self::TOK_COMPARE_NOT_EQUAL);
		} else {
			return ($this->token = self::TOK_NOT);
		}
	}

	/**
	 * Parse several compare characters
	 * @return int
	 */
	protected function parse_compare() {
		if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
			switch($this->doc[$this->pos++]) {
				case '$':
					return ($this->token = self::TOK_COMPARE_ENDS);
				case '%':
					return ($this->token = self::TOK_COMPARE_REGEX);
				case '^':
					return ($this->token = self::TOK_COMPARE_STARTS);
				case '<':
					return ($this->token = self::TOK_COMPARE_SMALLER_THAN);
			}
		}
		return false;
	}

	/**
	 * Parse strings (" and ')
	 * @return int
	 */
	protected function parse_string() {
		$char = $this->doc[$this->pos];

		while (true) {
			if ($this->next_search($char.'\\', false) !== self::TOK_NULL) {
				if($this->doc[$this->pos] === $char) {
					break;
				} else {
					++$this->pos;
				}
			} else {
				$this->pos = $this->size - 1;
				break;
			}
		}

		return ($this->token = self::TOK_STRING);
	}

}

/**
 * Performs a css select query on HTML nodes
 */
class HtmlSelector {

	/**
	 * Parser object
	 * @internal If string, then it will create a new instance as parser
	 * @var CSSQueryTokenizer
	 */
	var $parser = 'MailPoetVendor\\pQuery\\CSSQueryTokenizer';

	/**
	 * Target of queries
	 * @var DomNode
	 */
	var $root = null;

	/**
	 * Last performed query, result in {@link $result}
	 * @var string
	 */
	var $query = '';

	/**
	 * Array of matching nodes
	 * @var array
	 */
	var $result = array();

	/**
	 * Include root in search, if false the only child nodes are evaluated
	 * @var bool
	 */
	var $search_root = false;

	/**
	 * Search recursively
	 * @var bool
	 */
	var $search_recursive = true;

	/**
	 * Extra function map for custom filters
	 * @var array
	 * @internal array('root' => 'filter_root') will cause the
	 * selector to call $this->filter_root at :root
	 * @see DomNode::$filter_map
	 */
	var $custom_filter_map = array();

	/**
	 * Class constructor
	 * @param DomNode $root {@link $root}
	 * @param string $query
	 * @param bool $search_root {@link $search_root}
	 * @param bool $search_recursive {@link $search_recursive}
	 * @param CSSQueryTokenizer $parser If null, then default class will be used
	 */
	function __construct($root, $query = '*', $search_root = false, $search_recursive = true, $parser = null) {
		if ($parser === null) {
			$parser = new $this->parser();
		}
		$this->parser = $parser;
		$this->root =& $root;

		$this->search_root = $search_root;
		$this->search_recursive = $search_recursive;

		$this->select($query);
	}

	#php4 PHP4 class constructor compatibility
	#function HtmlSelector($root, $query = '*', $search_root = false, $search_recursive = true, $parser = null) {return $this->__construct($root, $query, $search_root, $search_recursive, $parser);}
	#php4e

	/**
	 * toString method, returns {@link $query}
	 * @return string
	 * @access private
	 */
	function __toString() {
		return $this->query;
	}

	/**
	 * Class magic invoke method, performs {@link select()}
	 * @return array
	 * @access private
	 */
	function __invoke($query = '*') {
		return $this->select($query);
	}

	/**
	 * Perform query
	 * @param string $query
	 * @return array False on failure
	 */
	function select($query = '*') {
		$this->parser->setDoc($query);
		$this->query = $query;
		return (($this->parse()) ? $this->result : false);
	}

	/**
	 * Trigger error
	 * @param string $error
	 * @internal %pos% and %tok% will be replace in string with position and token(string)
	 * @access private
	 */
	protected function error($error) {
		$error = htmlentities(str_replace(
			array('%tok%', '%pos%'),
			array($this->parser->getTokenString(), (int) $this->parser->getPos()),
			$error
		));

		trigger_error(esc_html($error));
	}

	/**
	 * Get identifier (parse identifier or string)
	 * @param bool $do_error Error on failure
	 * @return string False on failure
	 * @access private
	 */
	protected function parse_getIdentifier($do_error = true) {
		$p =& $this->parser;
		$tok = $p->token;

		if ($tok === CSSQueryTokenizer::TOK_IDENTIFIER) {
			return $p->getTokenString();
		} elseif($tok === CSSQueryTokenizer::TOK_STRING) {
			return str_replace(array('\\\'', '\\"', '\\\\'), array('\'', '"', '\\'), $p->getTokenString(1, -1));
		} elseif ($do_error) {
			$this->error('Expected identifier at %pos%!');
		}
		return false;
	}

	/**
	 * Get query conditions (tag, attribute and filter conditions)
	 * @return array False on failure
	 * @see DomNode::match()
	 * @access private
	 */
	protected function parse_conditions() {
		$p =& $this->parser;
		$tok = $p->token;

		if ($tok === CSSQueryTokenizer::TOK_NULL) {
			$this->error('Invalid search pattern(1): Empty string!');
			return false;
		}
		$conditions_all = array();

		//Tags
		while ($tok !== CSSQueryTokenizer::TOK_NULL) {
			$conditions = array('tags' => array(), 'attributes' => array());

			if ($tok === CSSQueryTokenizer::TOK_ALL) {
				$tok = $p->next();
				if (($tok === CSSQueryTokenizer::TOK_PIPE) && ($tok = $p->next()) && ($tok !== CSSQueryTokenizer::TOK_ALL)) {
					if (($tag = $this->parse_getIdentifier()) === false) {
						return false;
					}
					$conditions['tags'][] = array(
						'tag' => $tag,
						'compare' => 'name'
					);
					$tok = $p->next_no_whitespace();
				} else {
					$conditions['tags'][''] = array(
						'tag' => '',
						'match' => false
					);
					if ($tok === CSSQueryTokenizer::TOK_ALL) {
						$tok = $p->next_no_whitespace();
					}
				}
			} elseif ($tok === CSSQueryTokenizer::TOK_PIPE) {
				$tok = $p->next();
				if ($tok === CSSQueryTokenizer::TOK_ALL) {
					$conditions['tags'][] = array(
						'tag' => '',
						'compare' => 'namespace',
					);
				} elseif (($tag = $this->parse_getIdentifier()) !== false) {
					$conditions['tags'][] = array(
						'tag' => $tag,
						'compare' => 'total',
					);
				} else {
					return false;
				}
				$tok = $p->next_no_whitespace();
			} elseif ($tok === CSSQueryTokenizer::TOK_BRACE_OPEN) {
				$tok = $p->next_no_whitespace();
				$last_mode = 'or';

				while (true) {
					$match = true;
					$compare = 'total';

					if ($tok === CSSQueryTokenizer::TOK_NOT) {
						$match = false;
						$tok = $p->next_no_whitespace();
					}

					if ($tok === CSSQueryTokenizer::TOK_ALL) {
						$tok = $p->next();
						if ($tok === CSSQueryTokenizer::TOK_PIPE) {
							$this->next();
							$compare = 'name';
							if (($tag = $this->parse_getIdentifier()) === false) {
								return false;
							}
						}
					} elseif ($tok === CSSQueryTokenizer::TOK_PIPE) {
						$tok = $p->next();
						if ($tok === CSSQueryTokenizer::TOK_ALL) {
							$tag = '';
							$compare = 'namespace';
						} elseif (($tag = $this->parse_getIdentifier()) === false) {
							return false;
						}
						$tok = $p->next_no_whitespace();
					} else {
						if (($tag = $this->parse_getIdentifier()) === false) {
							return false;
						}
						$tok = $p->next();
						if ($tok === CSSQueryTokenizer::TOK_PIPE) {
							$tok = $p->next();

							if ($tok === CSSQueryTokenizer::TOK_ALL) {
								$compare = 'namespace';
							} elseif (($tag_name = $this->parse_getIdentifier()) !== false) {
								$tag = $tag.':'.$tag_name;
							} else {
								return false;
							}

							$tok = $p->next_no_whitespace();
						}
					}
					if ($tok === CSSQueryTokenizer::TOK_WHITESPACE) {
						$tok = $p->next_no_whitespace();
					}

					$conditions['tags'][] = array(
						'tag' => $tag,
						'match' => $match,
						'operator' => $last_mode,
						'compare' => $compare
					);
					switch($tok) {
						case CSSQueryTokenizer::TOK_COMMA:
							$tok = $p->next_no_whitespace();
							$last_mode = 'or';
							continue 2;
						case CSSQueryTokenizer::TOK_PLUS:
							$tok = $p->next_no_whitespace();
							$last_mode = 'and';
							continue 2;
						case CSSQueryTokenizer::TOK_BRACE_CLOSE:
							$tok = $p->next();
							break 2;
						default:
							$this->error('Expected closing brace or comma at pos %pos%!');
							return false;
					}
				}
			} elseif (($tag = $this->parse_getIdentifier(false)) !== false) {
				$tok = $p->next();
				if ($tok === CSSQueryTokenizer::TOK_PIPE) {
					$tok = $p->next();

					if ($tok === CSSQueryTokenizer::TOK_ALL) {
						$conditions['tags'][] = array(
							'tag' => $tag,
							'compare' => 'namespace'
						);
					} elseif (($tag_name = $this->parse_getIdentifier()) !== false) {
						$tag = $tag.':'.$tag_name;
						$conditions['tags'][] = array(
							'tag' => $tag,
							'match' => true
						);
					} else {
						return false;
					}

					$tok = $p->next();
                } elseif ($tag === 'text' && $tok === CSSQueryTokenizer::TOK_BRACE_OPEN) {
                    $pos = $p->getPos();
                    $tok = $p->next();
                    if ($tok === CSSQueryTokenizer::TOK_BRACE_CLOSE) {
                        $conditions['tags'][] = array(
                            'tag' => '~text~',
                            'match' => true
                        );
                        $p->next();
                    } else {
                        $p->setPos($pos);
                    }
				} else {
					$conditions['tags'][] = array(
						'tag' => $tag,
						'match' => true
					);
				}
			} else {
				unset($conditions['tags']);
			}

			//Class
			$last_mode = 'or';
			if ($tok === CSSQueryTokenizer::TOK_CLASS) {
				$p->next();
				if (($class = $this->parse_getIdentifier()) === false) {
					return false;
				}

				$conditions['attributes'][] = array(
					'attribute' => 'class',
					'operator_value' => 'contains_word',
					'value' => $class,
					'operator_result' => $last_mode
				);
				$last_mode = 'and';
				$tok = $p->next();
			}

			//ID
			if ($tok === CSSQueryTokenizer::TOK_ID) {
				$p->next();
				if (($id = $this->parse_getIdentifier()) === false) {
					return false;
				}

				$conditions['attributes'][] = array(
					'attribute' => 'id',
					'operator_value' => 'equals',
					'value' => $id,
					'operator_result' => $last_mode
				);
				$last_mode = 'and';
				$tok = $p->next();
			}

			//Attributes
			if ($tok === CSSQueryTokenizer::TOK_BRACKET_OPEN) {
				$tok = $p->next_no_whitespace();

				while (true) {
					$match = true;
					$compare = 'total';
					if ($tok === CSSQueryTokenizer::TOK_NOT) {
						$match = false;
						$tok = $p->next_no_whitespace();
					}

					if ($tok === CSSQueryTokenizer::TOK_ALL) {
						$tok = $p->next();
						if ($tok === CSSQueryTokenizer::TOK_PIPE) {
							$tok = $p->next();
							if (($attribute = $this->parse_getIdentifier()) === false) {
								return false;
							}
							$compare = 'name';
							$tok = $p->next();
						} else {
							$this->error('Expected pipe at pos %pos%!');
							return false;
						}
					} elseif ($tok === CSSQueryTokenizer::TOK_PIPE) {
						$tok = $p->next();
						if (($tag = $this->parse_getIdentifier()) === false) {
							return false;
						}
						$tok = $p->next_no_whitespace();
					} elseif (($attribute = $this->parse_getIdentifier()) !== false) {
						$tok = $p->next();
						if ($tok === CSSQueryTokenizer::TOK_PIPE) {
							$tok = $p->next();

							if (($attribute_name = $this->parse_getIdentifier()) !== false) {
								$attribute = $attribute.':'.$attribute_name;
							} else {
								return false;
							}

							$tok = $p->next();
						}
					} else {
						return false;
					}
					if ($tok === CSSQueryTokenizer::TOK_WHITESPACE) {
						$tok = $p->next_no_whitespace();
					}

					$operator_value = '';
					$val = '';
					switch($tok) {
						case CSSQueryTokenizer::TOK_COMPARE_PREFIX:
						case CSSQueryTokenizer::TOK_COMPARE_CONTAINS:
						case CSSQueryTokenizer::TOK_COMPARE_CONTAINS_WORD:
						case CSSQueryTokenizer::TOK_COMPARE_ENDS:
						case CSSQueryTokenizer::TOK_COMPARE_EQUALS:
						case CSSQueryTokenizer::TOK_COMPARE_NOT_EQUAL:
						case CSSQueryTokenizer::TOK_COMPARE_REGEX:
						case CSSQueryTokenizer::TOK_COMPARE_STARTS:
						case CSSQueryTokenizer::TOK_COMPARE_BIGGER_THAN:
						case CSSQueryTokenizer::TOK_COMPARE_SMALLER_THAN:
							$operator_value = $p->getTokenString(($tok === CSSQueryTokenizer::TOK_COMPARE_EQUALS) ? 0 : -1);
							$p->next_no_whitespace();

							if (($val = $this->parse_getIdentifier()) === false) {
								return false;
							}

							$tok = $p->next_no_whitespace();
							break;
					}

					if ($operator_value && $val) {
						$conditions['attributes'][] = array(
							'attribute' => $attribute,
							'operator_value' => $operator_value,
							'value' => $val,
							'match' => $match,
							'operator_result' => $last_mode,
							'compare' => $compare
						);
					} else {
						$conditions['attributes'][] = array(
							'attribute' => $attribute,
							'value' => $match,
							'operator_result' => $last_mode,
							'compare' => $compare
						);
					}

					switch($tok) {
						case CSSQueryTokenizer::TOK_COMMA:
							$tok = $p->next_no_whitespace();
							$last_mode = 'or';
							continue 2;
						case CSSQueryTokenizer::TOK_PLUS:
							$tok = $p->next_no_whitespace();
							$last_mode = 'and';
							continue 2;
						case CSSQueryTokenizer::TOK_BRACKET_CLOSE:
							$tok = $p->next();
							break 2;
						default:
							$this->error('Expected closing bracket or comma at pos %pos%!');
							return false;
					}
				}
			}

			if (count($conditions['attributes']) < 1) {
				unset($conditions['attributes']);
			}

			while($tok === CSSQueryTokenizer::TOK_COLON) {
				if (count($conditions) < 1) {
					$conditions['tags'] = array(array(
						'tag' => '',
						'match' => false
					));
				}

				$tok = $p->next();
				if (($filter = $this->parse_getIdentifier()) === false) {
					return false;
				}

				if (($tok = $p->next()) === CSSQueryTokenizer::TOK_BRACE_OPEN) {
					$start = $p->pos;
					$count = 1;
					while ((($tok = $p->next()) !== CSSQueryTokenizer::TOK_NULL) && !(($tok === CSSQueryTokenizer::TOK_BRACE_CLOSE) && (--$count === 0))) {
						if ($tok === CSSQueryTokenizer::TOK_BRACE_OPEN) {
							++$count;
						}
					}


					if ($tok !== CSSQueryTokenizer::TOK_BRACE_CLOSE) {
						$this->error('Expected closing brace at pos %pos%!');
						return false;
					}
					$len = $p->pos - 1 - $start;
					$params = (($len > 0) ? substr($p->doc, $start + 1, $len) : '');
					$tok = $p->next();
				} else {
					$params = '';
				}

				$conditions['filters'][] = array('filter' => $filter, 'params' => $params);
			}
			if (count($conditions) < 1) {
				$this->error('Invalid search pattern(2): No conditions found!');
				return false;
			}
			$conditions_all[] = $conditions;

			if ($tok === CSSQueryTokenizer::TOK_WHITESPACE) {
				$tok = $p->next_no_whitespace();
			}

			if ($tok === CSSQueryTokenizer::TOK_COMMA) {
				$tok = $p->next_no_whitespace();
				continue;
			} else {
				break;
			}
		}

		return $conditions_all;
	}


	/**
	 * Evaluate root node using custom callback
	 * @param array $conditions {@link parse_conditions()}
	 * @param bool|int $recursive
	 * @param bool $check_root
	 * @return array
	 * @access private
	 */
	protected function parse_callback($conditions, $recursive = true, $check_root = false) {
		return ($this->result = $this->root->getChildrenByMatch(
			$conditions,
			$recursive,
			$check_root,
			$this->custom_filter_map
		));
	}

	/**
	 * Parse first bit of query, only root node has to be evaluated now
	 * @param bool|int $recursive
	 * @return bool
	 * @internal Result of query is set in {@link $result}
	 * @access private
	 */
	protected function parse_single($recursive = true) {
		if (($c = $this->parse_conditions()) === false) {
			return false;
		}

		$this->parse_callback($c, $recursive, $this->search_root);
		return true;
	}

	/**
	 * Evaluate sibling nodes
	 * @return bool
	 * @internal Result of query is set in {@link $result}
	 * @access private
	 */
	protected function parse_adjacent() {
		$tmp = $this->result;
		$this->result = array();
		if (($c = $this->parse_conditions()) === false) {
			return false;
		}

		foreach($tmp as $t) {
			if (($sibling = $t->getNextSibling()) !== false) {
				if ($sibling->match($c, true, $this->custom_filter_map)) {
					$this->result[] = $sibling;
				}
			}
		}

		return true;
	}

	/**
	 * Evaluate {@link $result}
	 * @param bool $parent Evaluate parent nodes
	 * @param bool|int $recursive
	 * @return bool
	 * @internal Result of query is set in {@link $result}
	 * @access private
	 */
	protected function parse_result($parent = false, $recursive = true) {
		$tmp = $this->result;
		$tmp_res = array();
		if (($c = $this->parse_conditions()) === false) {
			return false;
		}

		foreach(array_keys($tmp) as $t) {
			$this->root = (($parent) ? $tmp[$t]->parent : $tmp[$t]);
			$this->parse_callback($c, $recursive);
			foreach(array_keys($this->result) as $r) {
				if (!in_array($this->result[$r], $tmp_res, true)) {
					$tmp_res[] = $this->result[$r];
				}
			}
		}
		$this->result = $tmp_res;
		return true;
	}

	/**
	 * Parse full query
	 * @return bool
	 * @internal Result of query is set in {@link $result}
	 * @access private
	 */
	protected function parse() {
		$p =& $this->parser;
		$p->setPos(0);
		$this->result = array();

		if (!$this->parse_single()) {
			return false;
		}

		while (count($this->result) > 0) {
			switch($p->token) {
				case CSSQueryTokenizer::TOK_CHILD:
					$this->parser->next_no_whitespace();
					if (!$this->parse_result(false, 1)) {
						return false;
					}
					break;

				case CSSQueryTokenizer::TOK_SIBLING:
					$this->parser->next_no_whitespace();
					if (!$this->parse_result(true, 1)) {
						return false;
					}
					break;

				case CSSQueryTokenizer::TOK_PLUS:
					$this->parser->next_no_whitespace();
					if (!$this->parse_adjacent()) {
						return false;
					}
					break;

				case CSSQueryTokenizer::TOK_ALL:
				case CSSQueryTokenizer::TOK_IDENTIFIER:
				case CSSQueryTokenizer::TOK_STRING:
				case CSSQueryTokenizer::TOK_BRACE_OPEN:
				case CSSQueryTokenizer::TOK_BRACKET_OPEN:
				case CSSQueryTokenizer::TOK_ID:
				case CSSQueryTokenizer::TOK_CLASS:
				case CSSQueryTokenizer::TOK_COLON:
					if (!$this->parse_result()) {
						return false;
					}
					break;

				case CSSQueryTokenizer::TOK_NULL:
					break 2;

				default:
					$this->error('Invalid search pattern(3): No result modifier found!');
					return false;
			}
		}

		return true;
	}
}
