<?php
namespace MailPoetVendor\Twig\Error;
if (!defined('ABSPATH')) exit;
class SyntaxError extends Error
{
 public function addSuggestions(string $name, array $items) : void
 {
 $alternatives = [];
 foreach ($items as $item) {
 $lev = \levenshtein($name, $item);
 if ($lev <= \strlen($name) / 3 || \false !== \strpos($item, $name)) {
 $alternatives[$item] = $lev;
 }
 }
 if (!$alternatives) {
 return;
 }
 \asort($alternatives);
 $this->appendMessage(\sprintf(' Did you mean "%s"?', \implode('", "', \array_keys($alternatives))));
 }
}
