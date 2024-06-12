<?php
namespace MailPoetVendor\Gregwar\Captcha;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Finder\Finder;
class ImageFileHandler
{
 protected $imageFolder;
 protected $webPath;
 protected $gcFreq;
 protected $expiration;
 public function __construct($imageFolder, $webPath, $gcFreq, $expiration)
 {
 $this->imageFolder = $imageFolder;
 $this->webPath = $webPath;
 $this->gcFreq = $gcFreq;
 $this->expiration = $expiration;
 }
 public function saveAsFile($contents)
 {
 $this->createFolderIfMissing();
 $filename = \md5(\uniqid()) . '.jpg';
 $filePath = $this->webPath . '/' . $this->imageFolder . '/' . $filename;
 \imagejpeg($contents, $filePath, 15);
 return '/' . $this->imageFolder . '/' . $filename;
 }
 public function collectGarbage()
 {
 if (!\mt_rand(1, $this->gcFreq) == 1) {
 return \false;
 }
 $this->createFolderIfMissing();
 $finder = new Finder();
 $criteria = \sprintf('<= now - %s minutes', $this->expiration);
 $finder->in($this->webPath . '/' . $this->imageFolder)->date($criteria);
 foreach ($finder->files() as $file) {
 \unlink($file->getPathname());
 }
 return \true;
 }
 protected function createFolderIfMissing()
 {
 if (!\file_exists($this->webPath . '/' . $this->imageFolder)) {
 \mkdir($this->webPath . '/' . $this->imageFolder, 0755);
 }
 }
}
