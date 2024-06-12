<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use JsonSerializable;
use ReturnTypeWillChange;
class Language implements JsonSerializable
{
 protected static $languagesNames;
 protected static $regionsNames;
 protected $id;
 protected $code;
 protected $variant;
 protected $region;
 protected $names;
 protected $isoName;
 protected $nativeName;
 public function __construct(string $id)
 {
 $this->id = \str_replace('-', '_', $id);
 $parts = \explode('_', $this->id);
 $this->code = $parts[0];
 if (isset($parts[1])) {
 if (!\preg_match('/^[A-Z]+$/', $parts[1])) {
 $this->variant = $parts[1];
 $parts[1] = $parts[2] ?? null;
 }
 if ($parts[1]) {
 $this->region = $parts[1];
 }
 }
 }
 public static function all()
 {
 if (!static::$languagesNames) {
 static::$languagesNames = (require __DIR__ . '/List/languages.php');
 }
 return static::$languagesNames;
 }
 public static function regions()
 {
 if (!static::$regionsNames) {
 static::$regionsNames = (require __DIR__ . '/List/regions.php');
 }
 return static::$regionsNames;
 }
 public function getNames() : array
 {
 if (!$this->names) {
 $this->names = static::all()[$this->code] ?? ['isoName' => $this->code, 'nativeName' => $this->code];
 }
 return $this->names;
 }
 public function getId() : string
 {
 return $this->id;
 }
 public function getCode() : string
 {
 return $this->code;
 }
 public function getVariant() : ?string
 {
 return $this->variant;
 }
 public function getVariantName() : ?string
 {
 if ($this->variant === 'Latn') {
 return 'Latin';
 }
 if ($this->variant === 'Cyrl') {
 return 'Cyrillic';
 }
 return $this->variant;
 }
 public function getRegion() : ?string
 {
 return $this->region;
 }
 public function getRegionName() : ?string
 {
 return $this->region ? static::regions()[$this->region] ?? $this->region : null;
 }
 public function getFullIsoName() : string
 {
 if (!$this->isoName) {
 $this->isoName = $this->getNames()['isoName'];
 }
 return $this->isoName;
 }
 public function setIsoName(string $isoName) : self
 {
 $this->isoName = $isoName;
 return $this;
 }
 public function getFullNativeName() : string
 {
 if (!$this->nativeName) {
 $this->nativeName = $this->getNames()['nativeName'];
 }
 return $this->nativeName;
 }
 public function setNativeName(string $nativeName) : self
 {
 $this->nativeName = $nativeName;
 return $this;
 }
 public function getIsoName() : string
 {
 $name = $this->getFullIsoName();
 return \trim(\strstr($name, ',', \true) ?: $name);
 }
 public function getNativeName() : string
 {
 $name = $this->getFullNativeName();
 return \trim(\strstr($name, ',', \true) ?: $name);
 }
 public function getIsoDescription()
 {
 $region = $this->getRegionName();
 $variant = $this->getVariantName();
 return $this->getIsoName() . ($region ? ' (' . $region . ')' : '') . ($variant ? ' (' . $variant . ')' : '');
 }
 public function getNativeDescription()
 {
 $region = $this->getRegionName();
 $variant = $this->getVariantName();
 return $this->getNativeName() . ($region ? ' (' . $region . ')' : '') . ($variant ? ' (' . $variant . ')' : '');
 }
 public function getFullIsoDescription()
 {
 $region = $this->getRegionName();
 $variant = $this->getVariantName();
 return $this->getFullIsoName() . ($region ? ' (' . $region . ')' : '') . ($variant ? ' (' . $variant . ')' : '');
 }
 public function getFullNativeDescription()
 {
 $region = $this->getRegionName();
 $variant = $this->getVariantName();
 return $this->getFullNativeName() . ($region ? ' (' . $region . ')' : '') . ($variant ? ' (' . $variant . ')' : '');
 }
 public function __toString()
 {
 return $this->getId();
 }
 #[\ReturnTypeWillChange]
 public function jsonSerialize()
 {
 return $this->getIsoDescription();
 }
}
