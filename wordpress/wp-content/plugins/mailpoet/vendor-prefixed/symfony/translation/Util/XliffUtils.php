<?php
namespace MailPoetVendor\Symfony\Component\Translation\Util;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidResourceException;
class XliffUtils
{
 public static function getVersionNumber(\DOMDocument $dom) : string
 {
 foreach ($dom->getElementsByTagName('xliff') as $xliff) {
 $version = $xliff->attributes->getNamedItem('version');
 if ($version) {
 return $version->nodeValue;
 }
 $namespace = $xliff->attributes->getNamedItem('xmlns');
 if ($namespace) {
 if (0 !== \substr_compare('urn:oasis:names:tc:xliff:document:', $namespace->nodeValue, 0, 34)) {
 throw new InvalidArgumentException(\sprintf('Not a valid XLIFF namespace "%s".', $namespace));
 }
 return \substr($namespace, 34);
 }
 }
 // Falls back to v1.2
 return '1.2';
 }
 public static function validateSchema(\DOMDocument $dom) : array
 {
 $xliffVersion = static::getVersionNumber($dom);
 $internalErrors = \libxml_use_internal_errors(\true);
 if ($shouldEnable = self::shouldEnableEntityLoader()) {
 $disableEntities = \libxml_disable_entity_loader(\false);
 }
 try {
 $isValid = @$dom->schemaValidateSource(self::getSchema($xliffVersion));
 if (!$isValid) {
 return self::getXmlErrors($internalErrors);
 }
 } finally {
 if ($shouldEnable) {
 \libxml_disable_entity_loader($disableEntities);
 }
 }
 $dom->normalizeDocument();
 \libxml_clear_errors();
 \libxml_use_internal_errors($internalErrors);
 return [];
 }
 private static function shouldEnableEntityLoader() : bool
 {
 // Version prior to 8.0 can be enabled without deprecation
 if (\PHP_VERSION_ID < 80000) {
 return \true;
 }
 static $dom, $schema;
 if (null === $dom) {
 $dom = new \DOMDocument();
 $dom->loadXML('<?xml version="1.0"?><test/>');
 $tmpfile = \tempnam(\sys_get_temp_dir(), 'symfony');
 \register_shutdown_function(static function () use($tmpfile) {
 @\unlink($tmpfile);
 });
 $schema = '<?xml version="1.0" encoding="utf-8"?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
 <xsd:include schemaLocation="file:///' . \str_replace('\\', '/', $tmpfile) . '" />
</xsd:schema>';
 \file_put_contents($tmpfile, '<?xml version="1.0" encoding="utf-8"?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
 <xsd:element name="test" type="testType" />
 <xsd:complexType name="testType"/>
</xsd:schema>');
 }
 return !@$dom->schemaValidateSource($schema);
 }
 public static function getErrorsAsString(array $xmlErrors) : string
 {
 $errorsAsString = '';
 foreach ($xmlErrors as $error) {
 $errorsAsString .= \sprintf("[%s %s] %s (in %s - line %d, column %d)\n", \LIBXML_ERR_WARNING === $error['level'] ? 'WARNING' : 'ERROR', $error['code'], $error['message'], $error['file'], $error['line'], $error['column']);
 }
 return $errorsAsString;
 }
 private static function getSchema(string $xliffVersion) : string
 {
 if ('1.2' === $xliffVersion) {
 $schemaSource = \file_get_contents(__DIR__ . '/../Resources/schemas/xliff-core-1.2-strict.xsd');
 $xmlUri = 'http://www.w3.org/2001/xml.xsd';
 } elseif ('2.0' === $xliffVersion) {
 $schemaSource = \file_get_contents(__DIR__ . '/../Resources/schemas/xliff-core-2.0.xsd');
 $xmlUri = 'informativeCopiesOf3rdPartySchemas/w3c/xml.xsd';
 } else {
 throw new InvalidArgumentException(\sprintf('No support implemented for loading XLIFF version "%s".', $xliffVersion));
 }
 return self::fixXmlLocation($schemaSource, $xmlUri);
 }
 private static function fixXmlLocation(string $schemaSource, string $xmlUri) : string
 {
 $newPath = \str_replace('\\', '/', __DIR__) . '/../Resources/schemas/xml.xsd';
 $parts = \explode('/', $newPath);
 $locationstart = 'file:///';
 if (0 === \stripos($newPath, 'phar://')) {
 $tmpfile = \tempnam(\sys_get_temp_dir(), 'symfony');
 if ($tmpfile) {
 \copy($newPath, $tmpfile);
 $parts = \explode('/', \str_replace('\\', '/', $tmpfile));
 } else {
 \array_shift($parts);
 $locationstart = 'phar:///';
 }
 }
 $drive = '\\' === \DIRECTORY_SEPARATOR ? \array_shift($parts) . '/' : '';
 $newPath = $locationstart . $drive . \implode('/', \array_map('rawurlencode', $parts));
 return \str_replace($xmlUri, $newPath, $schemaSource);
 }
 private static function getXmlErrors(bool $internalErrors) : array
 {
 $errors = [];
 foreach (\libxml_get_errors() as $error) {
 $errors[] = ['level' => \LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR', 'code' => $error->code, 'message' => \trim($error->message), 'file' => $error->file ?: 'n/a', 'line' => $error->line, 'column' => $error->column];
 }
 \libxml_clear_errors();
 \libxml_use_internal_errors($internalErrors);
 return $errors;
 }
}
