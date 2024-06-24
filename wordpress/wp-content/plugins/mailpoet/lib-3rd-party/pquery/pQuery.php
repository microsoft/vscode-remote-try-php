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
 * A jQuery-like object for php.
 */
class pQuery implements \ArrayAccess, \IteratorAggregate, IQuery {
    /// Properties ///

    /**
     * @var IQuery[]
     */
    protected $nodes = array();

    /// Methods ///

    public function __construct($nodes = array()) {
        $this->nodes = $nodes;
    }

    public function addClass($classname) {
        foreach ($this->nodes as $node) {
            $node->addClass($classname);
        }
        return $this;
    }

    public function after($content) {
        foreach ($this->nodes as $node) {
            $node->after($content);
        }
        return $this;
    }

    public function append($content) {
        foreach ($this->nodes as $node) {
            $node->append($content);
        }
        return $this;
    }

    public function attr($name, $value = null) {
        if (empty($this->nodes) && $value === null)
            return '';

        foreach ($this->nodes as $node) {
            if ($value === null)
                return $node->attr($name);
            $node->attr($name, $value);
        }
        return $this;
    }

    public function before($content) {
        foreach ($this->nodes as $node) {
            $node->before($content);
        }
        return $this;
    }

    public function clear() {
        foreach ($this->nodes as $node) {
            $node->clear();
        }
        return $this;
    }

    /**
     * Get the count of matched elements.
     *
     * @return int Returns the count of matched elements.
     */
    public function count(): int {
        return count($this->nodes);
    }

    /**
     * Format/beautify a DOM.
     *
     * @param DomNode $dom The dom to format.
     * @param array $options Extra formatting options. See {@link HtmlFormatter::$options}.
     * @return bool Returns `true` on sucess and `false` on failure.
     */
//    public static function format($dom, $options = array()) {
//        $formatter = new pQuery\HtmlFormatter($options);
//        return $formatter->format($dom);
//    }

    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->nodes);
    }

    public function hasClass($classname) {
        foreach ($this->nodes as $node) {
            if ($node->hasClass($classname))
                return true;
        }
        return false;
    }

    public function html($value = null) {
        if (empty($this->nodes) && $value === null)
            return '';

        foreach ($this->nodes as $node) {
            if ($value === null)
                return $node->html();
            $node->html($value);
        }
        return $this;
    }

    public function offsetExists($offset): bool {
        return isset($this->nodes[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        return isset($this->nodes[$offset]) ? $this->nodes[$offset] : null;
    }

    public function offsetSet($offset, $value): void {

        if (is_null($offset) || !isset($this->nodes[$offset])) {
            throw new \BadMethodCallException("You are not allowed to add new nodes to the pQuery object.");
        } else {
            $this->nodes[$offset]->replaceWith($value);
        }
    }

    public function offsetUnset($offset): void {
        if (isset($this->nodes[$offset])) {
            $this->nodes[$offset]->remove();
            unset($this->nodes[$offset]);
        }
    }

    /**
     * Query a file or url.
     *
     * @param string $path The path to the url.
     * @param resource $context A context suitable to be passed into {@link file_get_contents}
     * @return DomNode Returns the root dom node for the html file.
     */
    public static function parseFile($path, $context = null) {
        $html_str = file_get_contents($path, false, $context);
        return static::parseStr($html_str);
    }

    /**
     * Query a string of html.
     *
     * @param string $html
     * @return DomNode Returns the root dom node for the html string.
     */
    public static function parseStr($html) {
        $parser = new Html5Parser($html);
        return $parser->root;
    }

    public function prepend($content = null) {
        foreach ($this->nodes as $node) {
            $node->prepend($content);
        }
        return $this;
    }

    public function prop($name, $value = null) {
        if (empty($this->nodes) && $value === null)
            return '';

        foreach ($this->nodes as $node) {
            if ($value === null)
                return $node->prop($name);
            $node->prop($name, $value);
        }
        return $this;
    }

    public function remove($selector = null) {
        foreach ($this->nodes as $node) {
            $node->remove($selector);
        }
        if ($selector === null)
            $this->nodes = array();

        return $this;
    }

    public function removeAttr($name) {
        foreach ($this->nodes as $node) {
            $node->removeAttr($name);
        }
        return $this;
    }

    public function removeClass($classname) {
        foreach ($this->nodes as $node) {
            $node->removeClass($classname);
        }
        return $this;
    }

    public function replaceWith($content) {
        foreach ($this->nodes as &$node) {
            $node = $node->replaceWith($content);
        }
        return $this;
    }

    public function tagName($value = null) {
        foreach ($this->nodes as $node) {
            if ($value === null)
                return $node->tagName();
            $node->tagName($value);
        }
        return $this;
    }

    public function text($value = null) {
        if (empty($this->nodes) && $value === null)
            return '';

        foreach ($this->nodes as $node) {
            if ($value === null)
                return $node->text();
            $node->text($value);
        }
        return $this;
    }

    public function toggleClass($classname, $switch = null) {
        foreach ($this->nodes as $node) {
            $node->toggleClass($classname, $switch);
        }

        return $this;
    }

    public function unwrap() {
        foreach ($this->nodes as $node) {
            $node->unwrap();
        }
        return $this;
    }

    public function val($value = null) {
        if (empty($this->nodes) && $value === null)
            return '';

        foreach ($this->nodes as $node) {
            if ($value === null)
                return $node->val();
            $node->val($value);
        }
        return $this;
    }

    public function wrap($wrapping_element) {
        foreach ($this->nodes as $node) {
            $node->wrap($wrapping_element);
        }
        return $this;
    }

    public function wrapInner($wrapping_element) {
        foreach ($this->nodes as $node) {
            $node->wrapInner($wrapping_element);
        }
        return $this;
    }
}
