<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
require_once __DIR__ . "/src/Html2Text.php";
require_once __DIR__ . "/src/Html2TextException.php";
function convert_html_to_text($html)
{
 return Html2Text\Html2Text::convert($html);
}
function fix_newlines($text)
{
 return Html2Text\Html2Text::fixNewlines($text);
}
