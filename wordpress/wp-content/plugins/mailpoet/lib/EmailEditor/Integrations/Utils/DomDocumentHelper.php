<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Utils;

if (!defined('ABSPATH')) exit;


/**
 * This class should guarantee that our work with the DOMDocument is unified and safe.
 */
class DomDocumentHelper {
  private \DOMDocument $dom;

  public function __construct(
    string $htmlContent
  ) {
    $this->loadHtml($htmlContent);
  }

  private function loadHtml(string $htmlContent): void {
    libxml_use_internal_errors(true);
    $this->dom = new \DOMDocument();
    if (!empty($htmlContent)) {
      // prefixing the content with the XML declaration to force the input encoding to UTF-8
      $this->dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }
    libxml_clear_errors();
  }

  public function findElement(string $tagName): ?\DOMElement {
    $elements = $this->dom->getElementsByTagName($tagName);
    return $elements->item(0) ?: null;
  }

  public function getAttributeValue(\DOMElement $element, string $attribute): string {
    return $element->hasAttribute($attribute) ? $element->getAttribute($attribute) : '';
  }

  /**
   * Searches for the first appearance of the given tag name and returns the value of specified attribute.
   */
  public function getAttributeValueByTagName(string $tagName, string $attribute): ?string {
    $element = $this->findElement($tagName);
    if (!$element) {
      return null;
    }
    return $this->getAttributeValue($element, $attribute);
  }

  public function getOuterHtml(\DOMElement $element): string {
    return (string)$this->dom->saveHTML($element);
  }

  public function getElementInnerHTML(\DOMElement $element): string {
    $innerHTML = '';
    $children = $element->childNodes;
    foreach ($children as $child) {
      if (!$child instanceof \DOMNode) continue;
      $innerHTML .= $this->dom->saveHTML($child);
    }
    return $innerHTML;
  }
}
