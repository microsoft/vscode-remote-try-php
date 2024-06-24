<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
\define('AT_START', 1);
\define('AT_END', 2);
\define('SEL_START', 3);
\define('SEL_END', 4);
\define('PROPERTY', 5);
\define('VALUE', 6);
\define('COMMENT', 7);
\define('IMPORTANT_COMMENT', 8);
\define('DEFAULT_AT', 41);
require __DIR__ . \DIRECTORY_SEPARATOR . 'class.csstidy_print.php';
require __DIR__ . \DIRECTORY_SEPARATOR . 'class.csstidy_optimise.php';
class csstidy
{
 public $css = array();
 public $tokens = array();
 public $print;
 public $optimise;
 public $charset = '';
 public $import = array();
 public $namespace = '';
 public $version = '2.0.3';
 public $settings = array();
 public $status = 'is';
 public $at = '';
 public $next_selector_at = '';
 public $selector = '';
 public $property = '';
 public $sel_separate = array();
 public $value = '';
 public $sub_value = '';
 public $sub_value_arr = array();
 public $str_char = array();
 public $cur_string = array();
 public $from = array();
 public $invalid_at = \false;
 public $added = \false;
 public $log = array();
 public $line = 1;
 public $quoted_string = array();
 public $tokens_list = "";
 public $data = array();
 public $template;
 public function __construct()
 {
 $data = array();
 include __DIR__ . \DIRECTORY_SEPARATOR . 'data.inc.php';
 $this->data = $data;
 $this->settings['remove_bslash'] = \true;
 $this->settings['compress_colors'] = \true;
 $this->settings['compress_font-weight'] = \true;
 $this->settings['lowercase_s'] = \false;
 $this->settings['optimise_shorthands'] = 1;
 $this->settings['remove_last_;'] = \true;
 $this->settings['space_before_important'] = \false;
 $this->settings['case_properties'] = 1;
 $this->settings['sort_properties'] = \false;
 $this->settings['sort_selectors'] = 0;
 $this->settings['merge_selectors'] = 0;
 $this->settings['reverse_left_and_right'] = 0;
 $this->settings['discard_invalid_selectors'] = \false;
 $this->settings['discard_invalid_properties'] = \false;
 $this->settings['css_level'] = 'CSS3.0';
 $this->settings['preserve_css'] = \false;
 $this->settings['timestamp'] = \false;
 $this->settings['template'] = '';
 // say that propertie exist
 $this->set_cfg('template', 'default');
 // call load_template
 $this->optimise = new csstidy_optimise($this);
 $this->tokens_list =& $this->data['csstidy']['tokens'];
 }
 public function get_cfg($setting)
 {
 if (isset($this->settings[$setting])) {
 return $this->settings[$setting];
 }
 return \false;
 }
 public function _load_template($template)
 {
 switch ($template) {
 case 'default':
 $this->load_template('default');
 break;
 case 'highest':
 $this->load_template('highest_compression');
 break;
 case 'high':
 $this->load_template('high_compression');
 break;
 case 'low':
 $this->load_template('low_compression');
 break;
 default:
 $this->load_template($template);
 break;
 }
 }
 public function set_cfg($setting, $value = null)
 {
 if (\is_array($setting) && $value === null) {
 foreach ($setting as $setprop => $setval) {
 $this->settings[$setprop] = $setval;
 }
 if (\array_key_exists('template', $setting)) {
 $this->_load_template($this->settings['template']);
 }
 return \true;
 } elseif (isset($this->settings[$setting]) && $value !== '') {
 $this->settings[$setting] = $value;
 if ($setting === 'template') {
 $this->_load_template($this->settings['template']);
 }
 return \true;
 }
 return \false;
 }
 public function _add_token($type, $data, $do = \false)
 {
 if ($this->get_cfg('preserve_css') || $do) {
 // nested @... : if opening a new part we just closed, remove the previous closing instead of adding opening
 if ($type === \AT_START and \count($this->tokens) and $last = \end($this->tokens) and $last[0] === \AT_END and $last[1] === \trim($data)) {
 \array_pop($this->tokens);
 } else {
 $this->tokens[] = array($type, ($type == \COMMENT or $type == \IMPORTANT_COMMENT) ? $data : \trim($data));
 }
 }
 }
 public function log($message, $type, $line = -1)
 {
 if ($line === -1) {
 $line = $this->line;
 }
 $line = \intval($line);
 $add = array('m' => $message, 't' => $type);
 if (!isset($this->log[$line]) || !\in_array($add, $this->log[$line])) {
 $this->log[$line][] = $add;
 }
 }
 public function _unicode(&$string, &$i)
 {
 ++$i;
 $add = '';
 $replaced = \false;
 while ($i < \strlen($string) && (\ctype_xdigit($string[$i]) || \ctype_space($string[$i])) && \strlen($add) < 6) {
 $add .= $string[$i];
 if (\ctype_space($string[$i])) {
 break;
 }
 $i++;
 }
 if (\hexdec($add) > 47 && \hexdec($add) < 58 || \hexdec($add) > 64 && \hexdec($add) < 91 || \hexdec($add) > 96 && \hexdec($add) < 123) {
 $this->log('Replaced unicode notation: Changed \\' . $add . ' to ' . \chr(\hexdec($add)), 'Information');
 $add = \chr(\hexdec($add));
 $replaced = \true;
 } else {
 $add = \trim('\\' . $add);
 }
 if (@\ctype_xdigit($string[$i + 1]) && \ctype_space($string[$i]) && !$replaced || !\ctype_space($string[$i])) {
 $i--;
 }
 if ($add !== '\\' || !$this->get_cfg('remove_bslash') || \strpos($this->tokens_list, $string[$i + 1]) !== \false) {
 return $add;
 }
 if ($add === '\\') {
 $this->log('Removed unnecessary backslash', 'Information');
 }
 return '';
 }
 public function write_page($filename, $doctype = 'xhtml1.1', $externalcss = \true, $title = '', $lang = 'en')
 {
 $this->write($filename, \true);
 }
 public function write($filename, $formatted = \false, $doctype = 'xhtml1.1', $externalcss = \true, $title = '', $lang = 'en', $pre_code = \true)
 {
 $filename .= $formatted ? '.xhtml' : '.css';
 if (!\is_dir('temp')) {
 $madedir = \mkdir('temp');
 if (!$madedir) {
 print 'Could not make directory "temp" in ' . \dirname(__FILE__);
 exit;
 }
 }
 $handle = \fopen('temp/' . $filename, 'w');
 if ($handle) {
 if (!$formatted) {
 \fwrite($handle, $this->print->plain());
 } else {
 \fwrite($handle, $this->print->formatted_page($doctype, $externalcss, $title, $lang, $pre_code));
 }
 }
 \fclose($handle);
 }
 public function load_template($content, $from_file = \true)
 {
 $predefined_templates =& $this->data['csstidy']['predefined_templates'];
 if ($content === 'high_compression' || $content === 'default' || $content === 'highest_compression' || $content === 'low_compression') {
 $this->template = $predefined_templates[$content];
 return;
 }
 if ($from_file) {
 $content = \strip_tags(\file_get_contents($content), '<span>');
 }
 $content = \str_replace("\r\n", "\n", $content);
 // Unify newlines (because the output also only uses \n)
 $template = \explode('|', $content);
 for ($i = 0; $i < \count($template); $i++) {
 $this->template[$i] = $template[$i];
 }
 }
 public function parse_from_url($url)
 {
 return $this->parse(@\file_get_contents($url));
 }
 public function is_token(&$string, $i)
 {
 return \strpos($this->tokens_list, $string[$i]) !== \false && !$this->escaped($string, $i);
 }
 public function parse($string)
 {
 // Temporarily set locale to en_US in order to handle floats properly
 $old = @\setlocale(\LC_ALL, 0);
 @\setlocale(\LC_ALL, 'C');
 // PHP bug? Settings need to be refreshed in PHP4
 $this->print = new csstidy_print($this);
 $this->optimise = new csstidy_optimise($this);
 $all_properties =& $this->data['csstidy']['all_properties'];
 $at_rules =& $this->data['csstidy']['at_rules'];
 $quoted_string_properties =& $this->data['csstidy']['quoted_string_properties'];
 $this->css = array();
 $this->print->input_css = $string;
 $string = \str_replace("\r\n", "\n", $string) . ' ';
 $cur_comment = '';
 $cur_at = '';
 for ($i = 0, $size = \strlen($string); $i < $size; $i++) {
 if ($string[$i] === "\n" || $string[$i] === "\r") {
 ++$this->line;
 }
 switch ($this->status) {
 case 'at':
 if ($this->is_token($string, $i)) {
 if ($string[$i] === '/' && @$string[$i + 1] === '*') {
 $this->status = 'ic';
 ++$i;
 $this->from[] = 'at';
 } elseif ($string[$i] === '{') {
 $this->status = 'is';
 $this->at = $this->css_new_media_section($this->at, $cur_at);
 $this->_add_token(\AT_START, $this->at);
 } elseif ($string[$i] === ',') {
 $cur_at = \trim($cur_at) . ',';
 } elseif ($string[$i] === '\\') {
 $cur_at .= $this->_unicode($string, $i);
 } elseif (\in_array($string[$i], array('(', ')', ':', '.', '/'))) {
 $cur_at .= $string[$i];
 }
 } else {
 $lastpos = \strlen($cur_at) - 1;
 if (!((\ctype_space($cur_at[$lastpos]) || $this->is_token($cur_at, $lastpos) && $cur_at[$lastpos] === ',') && \ctype_space($string[$i]))) {
 $cur_at .= $string[$i];
 }
 }
 break;
 case 'is':
 if ($this->is_token($string, $i)) {
 if ($string[$i] === '/' && @$string[$i + 1] === '*' && \trim($this->selector) == '') {
 $this->status = 'ic';
 ++$i;
 $this->from[] = 'is';
 } elseif ($string[$i] === '@' && \trim($this->selector) == '') {
 // Check for at-rule
 $this->invalid_at = \true;
 foreach ($at_rules as $name => $type) {
 if (!\strcasecmp(\substr($string, $i + 1, \strlen($name)), $name)) {
 $type === 'at' ? $cur_at = '@' . $name : ($this->selector = '@' . $name);
 if ($type === 'atis') {
 $this->next_selector_at = $this->next_selector_at ? $this->next_selector_at : ($this->at ? $this->at : \DEFAULT_AT);
 $this->at = $this->css_new_media_section($this->at, ' ', \true);
 $type = 'is';
 }
 $this->status = $type;
 $i += \strlen($name);
 $this->invalid_at = \false;
 break;
 }
 }
 if ($this->invalid_at) {
 $this->selector = '@';
 $invalid_at_name = '';
 for ($j = $i + 1; $j < $size; ++$j) {
 if (!\ctype_alpha($string[$j])) {
 break;
 }
 $invalid_at_name .= $string[$j];
 }
 $this->log('Invalid @-rule: ' . $invalid_at_name . ' (removed)', 'Warning');
 }
 } elseif ($string[$i] === '"' || $string[$i] === "'") {
 $this->cur_string[] = $string[$i];
 $this->status = 'instr';
 $this->str_char[] = $string[$i];
 $this->from[] = 'is';
 $this->quoted_string[] = $string[$i - 1] === '=';
 } elseif ($this->invalid_at && $string[$i] === ';') {
 $this->invalid_at = \false;
 $this->status = 'is';
 if ($this->next_selector_at) {
 $this->at = $this->css_close_media_section($this->at);
 $this->at = $this->css_new_media_section($this->at, $this->next_selector_at);
 $this->next_selector_at = '';
 }
 } elseif ($string[$i] === '{') {
 $this->status = 'ip';
 if ($this->at == '') {
 $this->at = $this->css_new_media_section($this->at, \DEFAULT_AT);
 }
 $this->selector = $this->css_new_selector($this->at, $this->selector);
 $this->_add_token(\SEL_START, $this->selector);
 $this->added = \false;
 } elseif ($string[$i] === '}') {
 $this->_add_token(\AT_END, $this->at);
 $this->at = $this->css_close_media_section($this->at);
 $this->selector = '';
 $this->sel_separate = array();
 } elseif ($string[$i] === ',') {
 $this->selector = \trim($this->selector) . ',';
 $this->sel_separate[] = \strlen($this->selector);
 } elseif ($string[$i] === '\\') {
 $this->selector .= $this->_unicode($string, $i);
 } elseif ($string[$i] === '*' && @\in_array($string[$i + 1], array('.', '#', '[', ':')) && ($i == 0 or $string[$i - 1] !== '/')) {
 // remove unnecessary universal selector, FS#147, but not comment in selector
 } else {
 $this->selector .= $string[$i];
 }
 } else {
 $lastpos = \strlen($this->selector) - 1;
 if ($lastpos == -1 || !((\ctype_space($this->selector[$lastpos]) || $this->is_token($this->selector, $lastpos) && $this->selector[$lastpos] === ',') && \ctype_space($string[$i]))) {
 $this->selector .= $string[$i];
 }
 }
 break;
 case 'ip':
 if ($this->is_token($string, $i)) {
 if (($string[$i] === ':' || $string[$i] === '=') && $this->property != '') {
 $this->status = 'iv';
 if (!$this->get_cfg('discard_invalid_properties') || $this->property_is_valid($this->property)) {
 $this->property = $this->css_new_property($this->at, $this->selector, $this->property);
 $this->_add_token(\PROPERTY, $this->property);
 }
 } elseif ($string[$i] === '/' && @$string[$i + 1] === '*' && $this->property == '') {
 $this->status = 'ic';
 ++$i;
 $this->from[] = 'ip';
 } elseif ($string[$i] === '}') {
 $this->explode_selectors();
 $this->status = 'is';
 $this->invalid_at = \false;
 $this->_add_token(\SEL_END, $this->selector);
 $this->selector = '';
 $this->property = '';
 if ($this->next_selector_at) {
 $this->at = $this->css_close_media_section($this->at);
 $this->at = $this->css_new_media_section($this->at, $this->next_selector_at);
 $this->next_selector_at = '';
 }
 } elseif ($string[$i] === ';') {
 $this->property = '';
 } elseif ($string[$i] === '\\') {
 $this->property .= $this->_unicode($string, $i);
 } elseif ($this->property === '' && !\ctype_space($string[$i]) || ($this->property === '/' || $string[$i] === '/')) {
 $this->property .= $string[$i];
 }
 } elseif (!\ctype_space($string[$i])) {
 $this->property .= $string[$i];
 }
 break;
 case 'iv':
 $pn = ($string[$i] === "\n" || $string[$i] === "\r") && $this->property_is_next($string, $i + 1) || $i == \strlen($string) - 1;
 if ($this->is_token($string, $i) || $pn) {
 if ($string[$i] === '/' && @$string[$i + 1] === '*') {
 $this->status = 'ic';
 ++$i;
 $this->from[] = 'iv';
 } elseif ($string[$i] === '"' || $string[$i] === "'" || $string[$i] === '(') {
 $this->cur_string[] = $string[$i];
 $this->str_char[] = $string[$i] === '(' ? ')' : $string[$i];
 $this->status = 'instr';
 $this->from[] = 'iv';
 $this->quoted_string[] = \in_array(\strtolower($this->property), $quoted_string_properties);
 } elseif ($string[$i] === ',') {
 $this->sub_value = \trim($this->sub_value) . ',';
 } elseif ($string[$i] === '\\') {
 $this->sub_value .= $this->_unicode($string, $i);
 } elseif ($string[$i] === ';' || $pn) {
 if ($this->selector[0] === '@' && isset($at_rules[\substr($this->selector, 1)]) && $at_rules[\substr($this->selector, 1)] === 'iv') {
 $this->sub_value_arr[] = \trim($this->sub_value);
 $this->status = 'is';
 switch ($this->selector) {
 case '@charset':
 $this->charset = '"' . $this->sub_value_arr[0] . '"';
 break;
 case '@namespace':
 $this->namespace = \implode(' ', $this->sub_value_arr);
 break;
 case '@import':
 $this->import[] = \implode(' ', $this->sub_value_arr);
 break;
 }
 $this->sub_value_arr = array();
 $this->sub_value = '';
 $this->selector = '';
 $this->sel_separate = array();
 } else {
 $this->status = 'ip';
 }
 } elseif ($string[$i] !== '}') {
 $this->sub_value .= $string[$i];
 }
 if (($string[$i] === '}' || $string[$i] === ';' || $pn) && !empty($this->selector)) {
 if ($this->at == '') {
 $this->at = $this->css_new_media_section($this->at, \DEFAULT_AT);
 }
 // case settings
 if ($this->get_cfg('lowercase_s')) {
 $this->selector = \strtolower($this->selector);
 }
 $this->property = \strtolower($this->property);
 $this->optimise->subvalue();
 if ($this->sub_value != '') {
 $this->sub_value_arr[] = $this->sub_value;
 $this->sub_value = '';
 }
 $this->value = '';
 while (\count($this->sub_value_arr)) {
 $sub = \array_shift($this->sub_value_arr);
 if (\strstr($this->selector, 'font-face')) {
 $sub = $this->quote_font_format($sub);
 }
 if ($sub != '') {
 $this->value .= (!\strlen($this->value) || \substr($this->value, -1, 1) === ',' ? '' : ' ') . $sub;
 }
 }
 $this->optimise->value();
 $valid = $this->property_is_valid($this->property);
 if ((!$this->invalid_at || $this->get_cfg('preserve_css')) && (!$this->get_cfg('discard_invalid_properties') || $valid)) {
 $this->css_add_property($this->at, $this->selector, $this->property, $this->value);
 $this->_add_token(\VALUE, $this->value);
 $this->optimise->shorthands();
 }
 if (!$valid) {
 if ($this->get_cfg('discard_invalid_properties')) {
 $this->log('Removed invalid property: ' . $this->property, 'Warning');
 } else {
 $this->log('Invalid property in ' . \strtoupper($this->get_cfg('css_level')) . ': ' . $this->property, 'Warning');
 }
 }
 $this->property = '';
 $this->sub_value_arr = array();
 $this->value = '';
 }
 if ($string[$i] === '}') {
 $this->explode_selectors();
 $this->_add_token(\SEL_END, $this->selector);
 $this->status = 'is';
 $this->invalid_at = \false;
 $this->selector = '';
 if ($this->next_selector_at) {
 $this->at = $this->css_close_media_section($this->at);
 $this->at = $this->css_new_media_section($this->at, $this->next_selector_at);
 $this->next_selector_at = '';
 }
 }
 } elseif (!$pn) {
 $this->sub_value .= $string[$i];
 if (\ctype_space($string[$i])) {
 $this->optimise->subvalue();
 if ($this->sub_value != '') {
 $this->sub_value_arr[] = $this->sub_value;
 $this->sub_value = '';
 }
 }
 }
 break;
 case 'instr':
 $_str_char = $this->str_char[\count($this->str_char) - 1];
 $_cur_string = $this->cur_string[\count($this->cur_string) - 1];
 $_quoted_string = $this->quoted_string[\count($this->quoted_string) - 1];
 $temp_add = $string[$i];
 // Add another string to the stack. Strings can't be nested inside of quotes, only parentheses, but
 // parentheticals can be nested more than once.
 if ($_str_char === ")" && ($string[$i] === "(" || $string[$i] === '"' || $string[$i] === '\'') && !$this->escaped($string, $i)) {
 $this->cur_string[] = $string[$i];
 $this->str_char[] = $string[$i] === '(' ? ')' : $string[$i];
 $this->from[] = 'instr';
 $this->quoted_string[] = $_str_char === ')' && $string[$i] !== '(' && \trim($_cur_string) === '(' ? $_quoted_string : !($string[$i] === '(');
 continue 2;
 }
 if ($_str_char !== ")" && ($string[$i] === "\n" || $string[$i] === "\r") && !($string[$i - 1] === '\\' && !$this->escaped($string, $i - 1))) {
 $temp_add = "\\A";
 $this->log('Fixed incorrect newline in string', 'Warning');
 }
 $_cur_string .= $temp_add;
 if ($string[$i] === $_str_char && !$this->escaped($string, $i)) {
 $this->status = \array_pop($this->from);
 if (!\preg_match('|[' . \implode('', $this->data['csstidy']['whitespace']) . ']|uis', $_cur_string) && $this->property !== 'content') {
 if (!$_quoted_string) {
 if ($_str_char !== ')') {
 // Convert properties like
 // font-family: 'Arial';
 // to
 // font-family: Arial;
 // or
 // url("abc")
 // to
 // url(abc)
 $_cur_string = \substr($_cur_string, 1, -1);
 }
 } else {
 $_quoted_string = \false;
 }
 }
 \array_pop($this->cur_string);
 \array_pop($this->quoted_string);
 \array_pop($this->str_char);
 if ($_str_char === ')') {
 $_cur_string = '(' . \trim(\substr($_cur_string, 1, -1)) . ')';
 }
 if ($this->status === 'iv') {
 if (!$_quoted_string) {
 if (\strpos($_cur_string, ',') !== \false) {
 // we can on only remove space next to ','
 $_cur_string = \implode(',', \array_map('trim', \explode(',', $_cur_string)));
 }
 // and multiple spaces (too expensive)
 if (\strpos($_cur_string, ' ') !== \false) {
 $_cur_string = \preg_replace(",\\s+,", ' ', $_cur_string);
 }
 }
 $this->sub_value .= $_cur_string;
 } elseif ($this->status === 'is') {
 $this->selector .= $_cur_string;
 } elseif ($this->status === 'instr') {
 $this->cur_string[\count($this->cur_string) - 1] .= $_cur_string;
 }
 } else {
 $this->cur_string[\count($this->cur_string) - 1] = $_cur_string;
 }
 break;
 case 'ic':
 if ($string[$i] === '*' && $string[$i + 1] === '/') {
 $this->status = \array_pop($this->from);
 $i++;
 if (\strlen($cur_comment) > 1 and \strncmp($cur_comment, '!', 1) === 0) {
 $this->_add_token(\IMPORTANT_COMMENT, $cur_comment);
 $this->css_add_important_comment($cur_comment);
 } else {
 $this->_add_token(\COMMENT, $cur_comment);
 }
 $cur_comment = '';
 } else {
 $cur_comment .= $string[$i];
 }
 break;
 }
 }
 $this->optimise->postparse();
 $this->print->_reset();
 @\setlocale(\LC_ALL, $old);
 // Set locale back to original setting
 return !(empty($this->css) && empty($this->import) && empty($this->charset) && empty($this->tokens) && empty($this->namespace));
 }
 public function quote_font_format($value)
 {
 if (\strncmp($value, 'format', 6) == 0) {
 $p = \strpos($value, ')', 7);
 $end = \substr($value, $p);
 $format_strings = $this->parse_string_list(\substr($value, 7, $p - 7));
 if (!$format_strings) {
 $value = '';
 } else {
 $value = 'format(';
 foreach ($format_strings as $format_string) {
 $value .= '"' . \str_replace('"', '\\"', $format_string) . '",';
 }
 $value = \substr($value, 0, -1) . $end;
 }
 }
 return $value;
 }
 public function explode_selectors()
 {
 // Explode multiple selectors
 if ($this->get_cfg('merge_selectors') === 1) {
 $new_sels = array();
 $lastpos = 0;
 $this->sel_separate[] = \strlen($this->selector);
 foreach ($this->sel_separate as $num => $pos) {
 if ($num == \count($this->sel_separate) - 1) {
 $pos += 1;
 }
 $new_sels[] = \substr($this->selector, $lastpos, $pos - $lastpos - 1);
 $lastpos = $pos;
 }
 if (\count($new_sels) > 1) {
 foreach ($new_sels as $selector) {
 if (isset($this->css[$this->at][$this->selector])) {
 $this->merge_css_blocks($this->at, $selector, $this->css[$this->at][$this->selector]);
 }
 }
 unset($this->css[$this->at][$this->selector]);
 }
 }
 $this->sel_separate = array();
 }
 static function escaped(&$string, $pos)
 {
 return !(@($string[$pos - 1] !== '\\') || csstidy::escaped($string, $pos - 1));
 }
 public function css_add_important_comment($comment)
 {
 if ($this->get_cfg('preserve_css') || \trim($comment) == '') {
 return;
 }
 if (!isset($this->css['!'])) {
 $this->css['!'] = '';
 } else {
 $this->css['!'] .= "\n";
 }
 $this->css['!'] .= $comment;
 }
 public function css_add_property($media, $selector, $property, $new_val)
 {
 if ($this->get_cfg('preserve_css') || \trim($new_val) == '') {
 return;
 }
 $this->added = \true;
 if (isset($this->css[$media][$selector][$property])) {
 if ($this->is_important($this->css[$media][$selector][$property]) && $this->is_important($new_val) || !$this->is_important($this->css[$media][$selector][$property])) {
 $this->css[$media][$selector][$property] = \trim($new_val);
 }
 } else {
 $this->css[$media][$selector][$property] = \trim($new_val);
 }
 }
 public function css_check_last_media_section_or_inc($media)
 {
 // are we starting?
 if (!$this->css || !\is_array($this->css) || empty($this->css)) {
 return $media;
 }
 // if the last @media is the same as this
 // keep it
 \end($this->css);
 $at = \key($this->css);
 if ($at == $media) {
 return $media;
 }
 // else inc the section in the array
 while (isset($this->css[$media])) {
 if (\is_numeric($media)) {
 $media++;
 } else {
 $media .= ' ';
 }
 }
 return $media;
 }
 public function css_new_media_section($current_media, $new_media, $at_root = \false)
 {
 if ($this->get_cfg('preserve_css')) {
 return $new_media;
 }
 // if we already are in a media and CSS level is 3, manage nested medias
 if ($current_media && !$at_root && !\is_numeric($current_media) && \strncmp($this->get_cfg('css_level'), 'CSS3', 4) == 0) {
 $new_media = \rtrim($current_media) . "{" . \rtrim($new_media);
 }
 return $this->css_check_last_media_section_or_inc($new_media);
 }
 public function css_close_media_section($current_media)
 {
 if ($this->get_cfg('preserve_css')) {
 return '';
 }
 if (\strpos($current_media, '{') !== \false) {
 $current_media = \explode('{', $current_media);
 \array_pop($current_media);
 $current_media = \implode('{', $current_media);
 return $current_media;
 }
 return '';
 }
 public function css_new_selector($media, $selector)
 {
 if ($this->get_cfg('preserve_css')) {
 return $selector;
 }
 $selector = \trim($selector);
 if (\strncmp($selector, '@font-face', 10) != 0) {
 if ($this->settings['merge_selectors'] != \false) {
 return $selector;
 }
 if (!$this->css || !isset($this->css[$media]) || !$this->css[$media]) {
 return $selector;
 }
 // if last is the same, keep it
 \end($this->css[$media]);
 $sel = \key($this->css[$media]);
 if ($sel == $selector) {
 return $selector;
 }
 }
 while (isset($this->css[$media][$selector])) {
 $selector .= ' ';
 }
 return $selector;
 }
 public function css_new_property($media, $selector, $property)
 {
 if ($this->get_cfg('preserve_css')) {
 return $property;
 }
 if (!$this->css || !isset($this->css[$media][$selector]) || !$this->css[$media][$selector]) {
 return $property;
 }
 while (isset($this->css[$media][$selector][$property])) {
 $property .= ' ';
 }
 return $property;
 }
 public function merge_css_blocks($media, $selector, $css_add)
 {
 foreach ($css_add as $property => $value) {
 $this->css_add_property($media, $selector, $property, $value, \false);
 }
 }
 public function is_important(&$value)
 {
 return \strpos($value, '!') !== \false and !\strcasecmp(\substr(\str_replace($this->data['csstidy']['whitespace'], '', $value), -10, 10), '!important');
 }
 public function gvw_important($value)
 {
 if ($this->is_important($value)) {
 $value = \trim($value);
 $value = \substr($value, 0, -9);
 $value = \trim($value);
 $value = \substr($value, 0, -1);
 $value = \trim($value);
 return $value;
 }
 return $value;
 }
 public function property_is_next($istring, $pos)
 {
 $all_properties =& $this->data['csstidy']['all_properties'];
 $istring = \substr($istring, $pos, \strlen($istring) - $pos);
 $pos = \strpos($istring, ':');
 if ($pos === \false) {
 return \false;
 }
 $istring = \strtolower(\trim(\substr($istring, 0, $pos)));
 if (isset($all_properties[$istring])) {
 $this->log('Added semicolon to the end of declaration', 'Warning');
 return \true;
 }
 return \false;
 }
 public function property_is_valid($property)
 {
 if (\strpos($property, '--') === 0) {
 $property = "--custom";
 } elseif (\in_array(\trim($property), $this->data['csstidy']['multiple_properties'])) {
 $property = \trim($property);
 }
 $all_properties =& $this->data['csstidy']['all_properties'];
 return isset($all_properties[$property]) && \strpos($all_properties[$property], \strtoupper($this->get_cfg('css_level'))) !== \false;
 }
 public function parse_string_list($value)
 {
 $value = \trim($value);
 // Case: empty
 if (!$value) {
 return array();
 }
 $strings = array();
 $in_str = \false;
 $current_string = '';
 for ($i = 0, $_len = \strlen($value); $i < $_len; $i++) {
 if (($value[$i] === ',' || $value[$i] === ' ') && $in_str === \true) {
 $in_str = \false;
 $strings[] = $current_string;
 $current_string = '';
 } elseif ($value[$i] === '"' || $value[$i] === "'") {
 if ($in_str === $value[$i]) {
 $strings[] = $current_string;
 $in_str = \false;
 $current_string = '';
 continue;
 } elseif (!$in_str) {
 $in_str = $value[$i];
 }
 } else {
 if ($in_str) {
 $current_string .= $value[$i];
 } else {
 if (!\preg_match("/[\\s,]/", $value[$i])) {
 $in_str = \true;
 $current_string = $value[$i];
 }
 }
 }
 }
 if ($current_string) {
 $strings[] = $current_string;
 }
 return $strings;
 }
}
