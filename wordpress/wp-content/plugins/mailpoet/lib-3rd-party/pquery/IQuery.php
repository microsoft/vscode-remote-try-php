<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoetVendor\pQuery;

if (!defined('ABSPATH')) exit;


interface IQuery extends \Countable {
   /// Methods ///

   /**
    * Adds the specified class(es) to each of the set of matched elements.
    * @param string $classname The name of the class to add. You can add multiple classes by separating them with spaces.
    * @return IQuery
    */
   function addClass($classname);

   /**
    * Insert content, specified by the parameter, after each element in the set of matched elements.
    * @param string $content The content to add.
    * @return IQuery
    */
   function after($content);

   /**
    * Insert content, specified by the parameter, to the end of each element in the set of matched elements.
    * @param string $content The content to append.
    * @return IQuery
    */
   function append($content);

   /**
    * Get the value of an attribute for the first element in the set of matched elements or set one
    * or more attributes for every matched element.
    * @param string $name The name of the attribute.
    * @param null|string $value The value to set or null to get the current attribute value.
    * @return string|IQuery
    */
   function attr($name, $value = null);

   /**
    * Insert content, specified by the parameter, before each element in the set of matched elements.
    * @param string $content The content to add.
    * @return IQuery
    */
   function before($content);

   /**
    * Remove all child nodes of the set of matched elements from the DOM.
    * @return IQuery;
    */
   function clear();

   /**
    * Get the value of a style property for the first element in the set of matched elements or
    * set one or more CSS properties for every matched element.
    */
//   function css($name, $value = null);

   /**
    * Determine whether any of the matched elements are assigned the given class.
    * @param string $classname The name of the class to check.
    */
   function hasClass($classname);

   /**
    * Get the HTML contents of the first element in the set of matched elements
    * or set the HTML contents of every matched element.
    * @param string|null $value The value to set.
    */
   function html($value = null);

   /**
    * Insert content, specified by the parameter, to the beginning of each element in the set of matched elements.
    * @param string $content The content to add.
    */
   function prepend($content);

   /**
    * Get the value of a property for the first element in the set of matched elements
    * or set one or more properties for every matched element.
    * @param string $name The name of the property.
    * The currently supported properties are `tagname`, `selected`, and `checked`.
    * @param null|string $value The value to set or null to get the current property value.
    */
   function prop($name, $value = null);

   /**
    * Remove the set of matched elements from the DOM.
    * @param null|string $selector A css query to filter the set of removed nodes.
    */
   function remove($selector = null);

   /**
    * Remove an attribute from each element in the set of matched elements.
    * @param string $name The name of the attribute to remove.
    */
   function removeAttr($name);

   /**
    * Remove a single class, multiple classes, or all classes from each element in the set of matched elements.
    * @param string $classname The name of the class to remove.
    */
   function removeClass($classname);

   /**
    * Replace each element in the set of matched elements with the provided new content and return the set of elements that was removed.
    * @param string $content The content that will replace the nodes.
    */
   function replaceWith($content);

   /**
    * Returns the name of the element.
    * @param null|string $tagName A new tag name or null to return the current tag name.
    */
   function tagName($value = null);

   /**
    * Get the combined text contents of each element in the set of matched elements, including their descendants, or set the text contents of the matched elements.
    * @param null|string $value A string to set the text or null to return the current text.
    */
   function text($value = null);

   /**
    * Add or remove one or more classes from each element in the set of matched elements,
    * depending on either the class’s presence or the value of the switch argument.
    * @param string $classname
    * @param bool|null
    */
   function toggleClass($classname, $switch = null);

   /**
    * Remove the parents of the set of matched elements from the DOM, leaving the matched elements in their place.
    */
   function unwrap();

   /**
    * Get the current value of the first element in the set of matched elements or set the value of every matched element.
    * @param string|null $value The new value of the element or null to return the current value.
    */
   function val($value = null);

   /**
    * Wrap an HTML structure around each element in the set of matched elements.
    * @param string A tag name or html string specifying the structure to wrap around the matched elements.
    */
   function wrap($wrapping_element);

   /**
    * Wrap an HTML structure around the content of each element in the set of matched elements.
    * @param string A tag name or html string specifying the structure to wrap around the content of the matched elements.
    */
   function wrapInner($wrapping_element);
}
