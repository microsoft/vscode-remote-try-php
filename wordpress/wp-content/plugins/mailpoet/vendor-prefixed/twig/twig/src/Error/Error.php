<?php
namespace MailPoetVendor\Twig\Error;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;
class Error extends \Exception
{
 private $lineno;
 private $name;
 private $rawMessage;
 private $sourcePath;
 private $sourceCode;
 public function __construct(string $message, int $lineno = -1, ?Source $source = null, ?\Throwable $previous = null)
 {
 parent::__construct('', 0, $previous);
 if (null === $source) {
 $name = null;
 } else {
 $name = $source->getName();
 $this->sourceCode = $source->getCode();
 $this->sourcePath = $source->getPath();
 }
 $this->lineno = $lineno;
 $this->name = $name;
 $this->rawMessage = $message;
 $this->updateRepr();
 }
 public function getRawMessage() : string
 {
 return $this->rawMessage;
 }
 public function getTemplateLine() : int
 {
 return $this->lineno;
 }
 public function setTemplateLine(int $lineno) : void
 {
 $this->lineno = $lineno;
 $this->updateRepr();
 }
 public function getSourceContext() : ?Source
 {
 return $this->name ? new Source($this->sourceCode, $this->name, $this->sourcePath) : null;
 }
 public function setSourceContext(?Source $source = null) : void
 {
 if (null === $source) {
 $this->sourceCode = $this->name = $this->sourcePath = null;
 } else {
 $this->sourceCode = $source->getCode();
 $this->name = $source->getName();
 $this->sourcePath = $source->getPath();
 }
 $this->updateRepr();
 }
 public function guess() : void
 {
 $this->guessTemplateInfo();
 $this->updateRepr();
 }
 public function appendMessage($rawMessage) : void
 {
 $this->rawMessage .= $rawMessage;
 $this->updateRepr();
 }
 private function updateRepr() : void
 {
 $this->message = $this->rawMessage;
 if ($this->sourcePath && $this->lineno > 0) {
 $this->file = $this->sourcePath;
 $this->line = $this->lineno;
 return;
 }
 $dot = \false;
 if (\str_ends_with($this->message, '.')) {
 $this->message = \substr($this->message, 0, -1);
 $dot = \true;
 }
 $questionMark = \false;
 if (\str_ends_with($this->message, '?')) {
 $this->message = \substr($this->message, 0, -1);
 $questionMark = \true;
 }
 if ($this->name) {
 if (\is_string($this->name) || \is_object($this->name) && \method_exists($this->name, '__toString')) {
 $name = \sprintf('"%s"', $this->name);
 } else {
 $name = \json_encode($this->name);
 }
 $this->message .= \sprintf(' in %s', $name);
 }
 if ($this->lineno && $this->lineno >= 0) {
 $this->message .= \sprintf(' at line %d', $this->lineno);
 }
 if ($dot) {
 $this->message .= '.';
 }
 if ($questionMark) {
 $this->message .= '?';
 }
 }
 private function guessTemplateInfo() : void
 {
 $template = null;
 $templateClass = null;
 $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS | \DEBUG_BACKTRACE_PROVIDE_OBJECT);
 foreach ($backtrace as $trace) {
 if (isset($trace['object']) && $trace['object'] instanceof Template) {
 $currentClass = \get_class($trace['object']);
 $isEmbedContainer = null === $templateClass ? \false : \str_starts_with($templateClass, $currentClass);
 if (null === $this->name || $this->name == $trace['object']->getTemplateName() && !$isEmbedContainer) {
 $template = $trace['object'];
 $templateClass = \get_class($trace['object']);
 }
 }
 }
 // update template name
 if (null !== $template && null === $this->name) {
 $this->name = $template->getTemplateName();
 }
 // update template path if any
 if (null !== $template && null === $this->sourcePath) {
 $src = $template->getSourceContext();
 $this->sourceCode = $src->getCode();
 $this->sourcePath = $src->getPath();
 }
 if (null === $template || $this->lineno > -1) {
 return;
 }
 $r = new \ReflectionObject($template);
 $file = $r->getFileName();
 $exceptions = [$e = $this];
 while ($e = $e->getPrevious()) {
 $exceptions[] = $e;
 }
 while ($e = \array_pop($exceptions)) {
 $traces = $e->getTrace();
 \array_unshift($traces, ['file' => $e->getFile(), 'line' => $e->getLine()]);
 while ($trace = \array_shift($traces)) {
 if (!isset($trace['file']) || !isset($trace['line']) || $file != $trace['file']) {
 continue;
 }
 foreach ($template->getDebugInfo() as $codeLine => $templateLine) {
 if ($codeLine <= $trace['line']) {
 // update template line
 $this->lineno = $templateLine;
 return;
 }
 }
 }
 }
 }
}
