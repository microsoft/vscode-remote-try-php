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
 * Converts a document into tokens
 *
 * Can convert any string into tokens. The base class only supports
 * identifier/whitespace tokens. For more tokens, the class can be
 * easily extended.
 *
 * Use like:
 * <code>
 * <?php
 *  $a = new TokenizerBase('hello word');
 *  while ($a->next() !== $a::TOK_NULL) {
 *    echo $a->token, ': ',$a->getTokenString(), "<br>\n";
 *  }
 * ?>
 * </code>
 *
 * @internal The tokenizer works with a character map that connects a certain
 * character to a certain function/token. This class is build with speed in mind.
 */
class TokenizerBase {

	/**
	 * NULL Token, used at end of document (parsing should stop after this token)
	 */
	const TOK_NULL = 0;
	/**
	 * Unknown token, used at unidentified character
	 */
	const TOK_UNKNOWN = 1;
	/**
	 * Whitespace token, used with whitespace
	 */
	const TOK_WHITESPACE = 2;
	/**
	 * Identifier token, used with identifiers
	 */
	const TOK_IDENTIFIER = 3;

	/**
	 * The document that is being tokenized
	 * @var string
	 * @internal Public for faster access!
	 * @see setDoc()
	 * @see getDoc()
	 * @access private
	 */
	var $doc = '';

	/**
	 * The size of the document (length of string)
	 * @var int
	 * @internal Public for faster access!
	 * @see $doc
	 * @access private
	 */
	var $size = 0;

	/**
	 * Current (character) position in the document
	 * @var int
	 * @internal Public for faster access!
	 * @see setPos()
	 * @see getPos()
	 * @access private
	 */
	var $pos = 0;

	/**
	 * Current (Line/Column) position in document
	 * @var array (Current_Line, Line_Starting_Pos)
	 * @internal Public for faster access!
	 * @see getLinePos()
	 * @access private
	 */
	var $line_pos = array(0, 0);

	/**
	 * Current token
	 * @var int
	 * @internal Public for faster access!
	 * @see getToken()
	 * @access private
	 */
	var $token = self::TOK_NULL;

	/**
	 * Start position of token. If NULL, then current position is used.
	 * @var int
	 * @internal Public for faster access!
	 * @see getTokenString()
	 * @access private
	 */
	var $token_start = null;

	/**
	 * List with all the character that can be considered as whitespace
	 * @var array|string
	 * @internal Variable is public + associated array for faster access!
	 * @internal array(' ' => true) will recognize space (' ') as whitespace
	 * @internal String will be converted to array in constructor
	 * @internal Result token will be {@link self::TOK_WHITESPACE};
	 * @see setWhitespace()
	 * @see getWhitespace()
	 * @access private
	 */
	var $whitespace = " \t\n\r\0\x0B";

	/**
	 * List with all the character that can be considered as identifier
	 * @var array|string
	 * @internal Variable is public + associated array for faster access!
	 * @internal array('a' => true) will recognize 'a' as identifier
	 * @internal String will be converted to array in constructor
	 * @internal Result token will be {@link self::TOK_IDENTIFIER};
	 * @see setIdentifiers()
	 * @see getIdentifiers()
	 * @access private
	 */
	var $identifiers = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890_';

	/**
	 * All characters that should be mapped to a token/function that cannot be considered as whitespace or identifier
	 * @var array
	 * @internal Variable is public + associated array for faster access!
	 * @internal array('a' => 'parse_a') will call $this->parse_a() if it matches the character 'a'
	 * @internal array('a' => self::TOK_A) will set token to TOK_A if it matches the character 'a'
	 * @see mapChar()
	 * @see unmapChar()
	 * @access private
	 */
	var $custom_char_map = array();

	/**
	 * Automatically built character map. Built using {@link $identifiers}, {@link $whitespace} and {@link $custom_char_map}
	 * @var array
	 * @internal Public for faster access!
	 * @access private
	 */
	var $char_map = array();

	/**
	 * All errors found while parsing the document
	 * @var array
	 * @see addError()
	 */
	var $errors = array();

	/**
	 * Class constructor
	 * @param string $doc Document to be tokenized
	 * @param int $pos Position to start parsing
	 * @see setDoc()
	 * @see setPos()
	 */
	function __construct($doc = '', $pos = 0) {
		$this->setWhitespace($this->whitespace);
		$this->setIdentifiers($this->identifiers);

		$this->setDoc($doc, $pos);
	}

	#php4 PHP4 class constructor compatibility
	#function TokenizerBase($doc = '', $pos = 0) {return $this->__construct($doc, $pos);}
	#php4e

	/**
	 * Sets target document
	 * @param string $doc Document to be tokenized
	 * @param int $pos Position to start parsing
	 * @see getDoc()
	 * @see setPos()
	 */
	function setDoc($doc, $pos = 0) {
		$this->doc = $doc;
		$this->size = strlen($doc);
		$this->setPos($pos);
	}

	/**
	 * Returns target document
	 * @return string
	 * @see setDoc()
	 */
	function getDoc() {
		return $this->doc;
	}

	/**
	 * Sets position in document
	 * @param int $pos
	 * @see getPos()
	 */
	function setPos($pos = 0) {
		$this->pos = $pos - 1;
		$this->line_pos = array(0, 0);
		$this->next();
	}

	/**
	 * Returns current position in document (Index)
	 * @return int
	 * @see setPos()
	 */
	function getPos() {
		return $this->pos;
	}

	/**
	 * Returns current position in document (Line/Char)
	 * @return array array(Line, Column)
	 */
	function getLinePos() {
		return array($this->line_pos[0], $this->pos - $this->line_pos[1]);
	}

	/**
	 * Returns current token
	 * @return int
	 * @see $token
	 */
	function getToken() {
		return $this->token;
	}

	/**
	 * Returns current token as string
	 * @param int $start_offset Offset from token start
	 * @param int $end_offset Offset from token end
	 * @return string
	 */
	function getTokenString($start_offset = 0, $end_offset = 0) {
		$token_start = ((is_int($this->token_start)) ? $this->token_start : $this->pos) + $start_offset;
		$len = $this->pos - $token_start + 1 + $end_offset;
		return (($len > 0) ? substr($this->doc, $token_start, $len) : '');
	}

	/**
	 * Sets characters to be recognized as whitespace
	 *
	 * Used like: setWhitespace('ab') or setWhitespace(array('a' => true, 'b', 'c'));
	 * @param string|array $ws
	 * @see getWhitespace();
	 */
	function setWhitespace($ws) {
		if (is_array($ws)) {
			$this->whitespace = array_fill_keys(array_values($ws), true);
			$this->buildCharMap();
		} else {
			$this->setWhiteSpace(str_split($ws));
		}
	}

	/**
	 * Returns whitespace characters as string/array
	 * @param bool $as_string Should the result be a string or an array?
	 * @return string|array
	 * @see setWhitespace()
	 */
	function getWhitespace($as_string = true) {
		$ws = array_keys($this->whitespace);
		return (($as_string) ? implode('', $ws) : $ws);
	}

	/**
	 * Sets characters to be recognized as identifier
	 *
	 * Used like: setIdentifiers('ab') or setIdentifiers(array('a' => true, 'b', 'c'));
	 * @param string|array $ident
	 * @see getIdentifiers();
	 */
	function setIdentifiers($ident) {
		if (is_array($ident)) {
			$this->identifiers = array_fill_keys(array_values($ident), true);
			$this->buildCharMap();
		} else {
			$this->setIdentifiers(str_split($ident));
		}
	}

	/**
	 * Returns identifier characters as string/array
	 * @param bool $as_string Should the result be a string or an array?
	 * @return string|array
	 * @see setIdentifiers()
	 */
	function getIdentifiers($as_string = true) {
		$ident = array_keys($this->identifiers);
		return (($as_string) ? implode('', $ident) : $ident);
	}

	/**
	 * Maps a custom character to a token/function
	 *
	 * Used like: mapChar('a', self::{@link TOK_IDENTIFIER}) or mapChar('a', 'parse_identifier');
	 * @param string $char Character that should be mapped. If set, it will be overridden
	 * @param int|string $map If function name, then $this->function will be called, otherwise token is set to $map
	 * @see unmapChar()
	 */
	function mapChar($char, $map) {
		$this->custom_char_map[$char] = $map;
		$this->buildCharMap();
	}

	/**
	 * Removes a char mapped with {@link mapChar()}
	 * @param string $char Character that should be unmapped
	 * @see mapChar()
	 */
	function unmapChar($char) {
		unset($this->custom_char_map[$char]);
		$this->buildCharMap();
	}

	/**
	 * Builds the {@link $map_char} array
	 * @internal Builds single array that maps all characters. Gets called if {@link $whitespace}, {@link $identifiers} or {@link $custom_char_map} get modified
	 */
	protected function buildCharMap() {
		$this->char_map = $this->custom_char_map;
		if (is_array($this->whitespace)) {
			foreach($this->whitespace as $w => $v) {
				$this->char_map[$w] = 'parse_whitespace';
			}
		}
		if (is_array($this->identifiers)) {
			foreach($this->identifiers as $i => $v) {
				$this->char_map[$i] = 'parse_identifier';
			}
		}
	}

	/**
	 * Add error to the array and appends current position
	 * @param string $error
	 */
	function addError($error) {
		$this->errors[] = htmlentities($error.' at '.($this->line_pos[0] + 1).', '.($this->pos - $this->line_pos[1] + 1).'!');
	}

	/**
	 * Parse line breaks and increase line number
	 * @internal Gets called to process line breaks
	 */
	protected function parse_linebreak() {
		if($this->doc[$this->pos] === "\r") {
			++$this->line_pos[0];
			if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === "\n")) {
				++$this->pos;
			}
			$this->line_pos[1] = $this->pos;
		} elseif($this->doc[$this->pos] === "\n") {
			++$this->line_pos[0];
			$this->line_pos[1] = $this->pos;
		}
	}

	/**
	 * Parse whitespace
	 * @return int Token
	 * @internal Gets called with {@link $whitespace} characters
	 */
	protected function parse_whitespace() {
		$this->token_start = $this->pos;

		while(++$this->pos < $this->size) {
			if (!isset($this->whitespace[$this->doc[$this->pos]])) {
				break;
			} else {
				$this->parse_linebreak();
			}
		}

		--$this->pos;
		return self::TOK_WHITESPACE;
	}

	/**
	 * Parse identifiers
	 * @return int Token
	 * @internal Gets called with {@link $identifiers} characters
	 */
	protected function parse_identifier() {
		$this->token_start = $this->pos;

		while((++$this->pos < $this->size) && isset($this->identifiers[$this->doc[$this->pos]])) {}

		--$this->pos;
		return self::TOK_IDENTIFIER;
	}

	/**
	 * Continues to the next token
	 * @return int Next token ({@link TOK_NULL} if none)
	 */
	function next() {
		$this->token_start = null;

		if (++$this->pos < $this->size) {
			if (isset($this->char_map[$this->doc[$this->pos]])) {
				if (is_string($this->char_map[$this->doc[$this->pos]])) {
					return ($this->token = $this->{$this->char_map[$this->doc[$this->pos]]}());
				} else {
					return ($this->token = $this->char_map[$this->doc[$this->pos]]);
				}
			} else {
				return ($this->token = self::TOK_UNKNOWN);
			}
		} else {
			return ($this->token = self::TOK_NULL);
		}
	}

	/**
	 * Finds the next token, but skips whitespace
	 * @return int Next token ({@link TOK_NULL} if none)
	 */
	function next_no_whitespace() {
		$this->token_start = null;

		while (++$this->pos < $this->size) {
			if (!isset($this->whitespace[$this->doc[$this->pos]])) {
				if (isset($this->char_map[$this->doc[$this->pos]])) {
					if (is_string($this->char_map[$this->doc[$this->pos]])) {
						return ($this->token = $this->{$this->char_map[$this->doc[$this->pos]]}());
					} else {
						return ($this->token = $this->char_map[$this->doc[$this->pos]]);
					}
				} else {
					return ($this->token = self::TOK_UNKNOWN);
				}
			} else {
				$this->parse_linebreak();
			}
		}

		return ($this->token = self::TOK_NULL);
	}

	/**
	 * Finds the next token using stop characters.
	 *
	 * Used like: next_search('abc') or next_search(array('a' => true, 'b' => true, 'c' => true));
	 * @param string|array $characters Characters to search for
	 * @param bool $callback Should the function check the charmap after finding a character?
	 * @return int Next token ({@link TOK_NULL} if none)
	 */
	function next_search($characters, $callback = true) {
		$this->token_start = $this->pos;
		if (!is_array($characters)) {
			$characters = array_fill_keys(str_split($characters), true);
		}

		while(++$this->pos < $this->size) {
			if (isset($characters[$this->doc[$this->pos]])) {
				if ($callback && isset($this->char_map[$this->doc[$this->pos]])) {
					if (is_string($this->char_map[$this->doc[$this->pos]])) {
						return ($this->token = $this->{$this->char_map[$this->doc[$this->pos]]}());
					} else {
						return ($this->token = $this->char_map[$this->doc[$this->pos]]);
					}
				} else {
					return ($this->token = self::TOK_UNKNOWN);
				}
			} else {
				$this->parse_linebreak();
			}
		}

		return ($this->token = self::TOK_NULL);
	}

	/**
	 * Finds the next token by searching for a string
	 * @param string $needle The needle that's being searched for
	 * @param bool $callback Should the function check the charmap after finding the needle?
	 * @return int Next token ({@link TOK_NULL} if none)
	 */
	function next_pos($needle, $callback = true) {
		$this->token_start = $this->pos;
		if (($this->pos < $this->size) && (($p = stripos($this->doc, $needle, $this->pos + 1)) !== false)) {

			$len = $p - $this->pos - 1;
			if ($len > 0) {
				$str = substr($this->doc, $this->pos + 1, $len);

				if (($l = strrpos($str, "\n")) !== false) {
					++$this->line_pos[0];
					$this->line_pos[1] = $l + $this->pos + 1;

					$len -= $l;
					if ($len > 0) {
						$str = substr($str, 0, -$len);
						$this->line_pos[0] += substr_count($str, "\n");
					}
				}
			}

			$this->pos = $p;
			if ($callback && isset($this->char_map[$this->doc[$this->pos]])) {
				if (is_string($this->char_map[$this->doc[$this->pos]])) {
					return ($this->token = $this->{$this->char_map[$this->doc[$this->pos]]}());
				} else {
					return ($this->token = $this->char_map[$this->doc[$this->pos]]);
				}
			} else {
				return ($this->token = self::TOK_UNKNOWN);
			}
		} else {
			$this->pos = $this->size;
			return ($this->token = self::TOK_NULL);
		}
	}

	/**
	 * Expect a specific token or character. Adds error if token doesn't match.
	 * @param string|int $token Character or token to expect
	 * @param bool|int $do_next Go to next character before evaluating. 1 for next char, true to ignore whitespace
	 * @param bool|int $try_next Try next character if current doesn't match. 1 for next char, true to ignore whitespace
	 * @param bool|int $next_on_match Go to next character after evaluating. 1 for next char, true to ignore whitespace
	 * @return bool
	 */
	protected function expect($token, $do_next = true, $try_next = false, $next_on_match = 1) {
		if ($do_next) {
			if ($do_next === 1) {
				$this->next();
			} else {
				$this->next_no_whitespace();
			}
		}

		if (is_int($token)) {
			if (($this->token !== $token) && ((!$try_next) || ((($try_next === 1) && ($this->next() !== $token)) || (($try_next === true) && ($this->next_no_whitespace() !== $token))))) {
				$this->addError('Unexpected "'.$this->getTokenString().'"');
				return false;
			}
		} else {
			if (($this->doc[$this->pos] !== $token) && ((!$try_next) || (((($try_next === 1) && ($this->next() !== self::TOK_NULL)) || (($try_next === true) && ($this->next_no_whitespace() !== self::TOK_NULL))) && ($this->doc[$this->pos] !== $token)))) {
				$this->addError('Expected "'.$token.'", but found "'.$this->getTokenString().'"');
				return false;
			}
		}

		if ($next_on_match) {
			if ($next_on_match === 1) {
				$this->next();
			} else {
				$this->next_no_whitespace();
			}
		}
		return true;
	}
}
