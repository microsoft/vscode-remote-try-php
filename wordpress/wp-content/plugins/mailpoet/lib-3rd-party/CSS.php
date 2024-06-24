<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoetVendor;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;
use MailPoet\Util\pQuery\DomNode;
use MailPoet\Util\pQuery\pQuery;

/*
  Copyright 2013-2014, François-Marie de Jouvencel

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
* A class to inline CSS.
*
* It honours !important attributes and doesn't choke on complex styles.
*
*
*/

class CSS {
  /**
   * @param string $contents
   * @return DomNode
   */
  function inlineCSS($contents) {
    $html = pQuery::parseStr($contents);

    if (!$html instanceof DomNode) {
      throw new \InvalidArgumentException('Error parsing contents.');
    }

    $css_blocks = '';

    // Find all <style> blocks and cut styles from them (leaving media queries)
    foreach ($html->query('style') as $style) {
      list($_css_to_parse, $_css_to_keep) = $this->splitMediaQueries($style->getInnerText());
      $css_blocks .= $_css_to_parse;
      if (!empty($_css_to_keep)) {
        $style->setInnerText($_css_to_keep);
      } else {
        $style->setOuterText('');
      }
    }

    $raw_css = '';
    if (!empty($css_blocks)) {
      $raw_css .= $css_blocks;
    }

    // Get the CSS rules by decreasing specificity (the most specific rule first).
    // This is an array with, amongst other things, the keys 'properties', which hold the CSS properties
    // and the 'selector', which holds the CSS selector
    $rules = $this->parseCSS($raw_css);
    $nodes_map = [];

    // We loop over each rule by increasing order of specificity, find the nodes matching the selector
    // and apply the CSS properties
    foreach ($rules as $rule) {
      if (!isset($nodes_map[$rule['selector']])) {
        $nodes_map[$rule['selector']] = $html->query($rule['selector']);
      }
      foreach ($nodes_map[$rule['selector']] as $node) {
        // I'm leaving this for debug purposes, it has proved useful.
        /*
        if ($node->already_styled === 'yes')
        {
          echo "<PRE>";
          echo "Rule:\n";
          print_r($rule);
          echo "\n\nOld style:\n";
          echo $node->style."\n";
          print_r($this->styleToArray($node->style));
          echo "\n\nNew style:\n";
          print_r(array_merge($this->styleToArray($node->style), $rule['properties']));
          echo "</PRE>";
          die();
        }//*/

        // Unserialize the style array, merge the rule's CSS into it...
        $nodeStyles = $this->styleToArray($node->style);
        $style = array_merge($rule['properties'], $nodeStyles);

        // And put the CSS back as a string!
        $node->style = $this->arrayToStyle($style);

        // I'm leaving this for debug purposes, it has proved useful.
        /*
        if ($rule['selector'] === 'table.table-recap td')
        {
          $node->already_styled = 'yes';
        }//*/
      }
    }

    // Now a tricky part: do a second pass with only stuff marked !important
    // because !important properties do not care about specificity, except when fighting
    // against another !important property
    // We need to start with a rule with lowest specificity
    $rules = array_reverse($rules);
    foreach ($rules as $rule) {
      foreach ($rule['properties'] as $key => $value) {
        if (strpos($value, '!important') === false) {
          continue;
        }
        foreach ($nodes_map[$rule['selector']] as $node) {
          $style = $this->styleToArray($node->style);
          $style[$key] = $value;
          $node->style = $this->arrayToStyle($style);
          // remove all !important tags (inlined styles take precedent over others anyway)
          $node->style = str_replace("!important", "", $node->style);
        }
      }
    }

    return $html;
  }

  function parseCSS($text) {
    $css = new csstidy();
    $css->settings['compress_colors'] = false;
    // Disable shorthand optimisation--this breaks `padding: calc() calc()` style rules.
    $css->settings['optimise_shorthands'] = 0;
    $css->parse($text);

    $rules = [];
    $position = 0;

    foreach ($css->css as $declarations) {
      foreach ($declarations as $selectors => $properties) {
        foreach (explode(",", $selectors) as $selector) {
          $rules[] = [
            'position' => $position,
            'specificity' => $this->calculateCSSSpecifity($selector),
            'selector' => $selector,
            'properties' => $properties,
          ];
        }

        $position += 1;
      }
    }

    usort($rules, function($a, $b) {
      if ($a['specificity'] > $b['specificity']) {
        return -1;
      } else if ($a['specificity'] < $b['specificity']) {
        return 1;
      } else {
        if ($a['position'] > $b['position']) {
          return -1;
        } else {
          return 1;
        }
      }
    });

    return $rules;
  }

  /*
  * Merges two CSS inline styles strings into one.
  * If both styles defines same property the property from second styles will be used.
  */
  function mergeInlineStyles($styles_1, $styles_2) {
    $merged_styles = array_merge($this->styleToArray($styles_1), $this->styleToArray($styles_2));
    return $this->arrayToStyle($merged_styles);
  }

  private function splitMediaQueries($css) {
    $start = 0;
    $queries = '';

    while (($start = strpos($css, "@media", $start)) !== false) {
      // stack to manage brackets
      $s = [];

      // get the first opening bracket
      $i = strpos($css, "{", $start);

      // if $i is false, then there is probably a css syntax error
      if ($i !== false) {
        // push bracket onto stack
        array_push($s, $css[$i]);

        // move past first bracket
        $i++;

        while (!empty($s)) {
          // if the character is an opening bracket, push it onto the stack, otherwise pop the stack
          if ($css[$i] == "{") {
            array_push($s, "{");
          } else if ($css[$i] == "}") {
            array_pop($s);
          }

          $i++;
        }

        $queries .= substr($css, $start - 1, $i + 1 - $start) . "\n";
        $css = substr($css, 0, $start - 1) . substr($css, $i);
        $i = $start;
      }
    }

    return [$css, $queries];
  }

  /**
   * The following function fomes from CssToInlineStyles.php - here is the original licence FOR THIS FUNCTION
   *
   * CSS to Inline Styles class
   *
   * @author    Tijs Verkoyen <php-css-to-inline-styles@verkoyen.eu>
   * @version   1.2.1
   * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
   * @license   BSD License
   */

  private function calculateCSSSpecifity($selector) {
      // cleanup selector
    $selector = str_replace(['>', '+'], [' > ', ' + '], $selector);

      // init var
    $specifity = 0;

      // split the selector into chunks based on spaces
    $chunks = explode(' ', $selector);

      // loop chunks
    foreach ($chunks as $chunk) {
          // an ID is important, so give it a high specifity
      if (strstr($chunk, '#') !== false) $specifity += 100;

          // classes are more important than a tag, but less important then an ID
      elseif (strstr($chunk, '.')) $specifity += 10;

          // anything else isn't that important
      else $specifity += 1;
    }

      // return
    return $specifity;
  }

  /*
  * Turns a CSS style string (like: "border: 1px solid black; color:red")
  * into an array of properties (like: array("border" => "1px solid black", "color" => "red"))
  */
  private function styleToArray($str) {
    $str = EHelper::unescapeHtmlStyleAttr($str);
    $array = [];

    if (trim($str) === '') return $array;

    foreach (explode(';', $str) as $kv) {
      $line = trim($kv);
      if ($line === '') {
        continue;
      }
      list($selector, $rule) = explode(':', $line, 2);
      $array[trim($selector)] = trim($rule);
    }

    return $array;
  }

  /*
  * Reverses what styleToArray does, see above.
  * array("border" => "1px solid black", "color" => "red") yields "border: 1px solid black; color:red"
  */
  private function arrayToStyle($array) {
    $parts = [];
    foreach ($array as $k => $v) {
      $parts[] = "$k:$v";
    }
    return EHelper::escapeHtmlStyleAttr(implode(';', $parts));
  }
}
