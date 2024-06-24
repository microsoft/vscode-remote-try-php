<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription\Captcha;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoetVendor\Gregwar\Captcha\CaptchaBuilder;

class CaptchaRenderer {


  private $phrase;

  public function __construct(
    CaptchaPhrase $phrase
  ) {
    $this->phrase = $phrase;
  }

  public function isSupported() {
    return extension_loaded('gd') && function_exists('imagettftext');
  }

  public function renderAudio($sessionId, $return = false) {

    $audioPath = Env::$assetsPath . '/audio/';
    $phrase = $this->phrase->getPhraseForType($this->determineAudioType(), $sessionId);
    $audio = null;
    foreach (str_split($phrase) as $character) {
      $file = $audioPath . strtolower($character) . '.mp3';
      if (!file_exists($file)) {
        throw new \RuntimeException("File not found.");
      }
      $audio .= file_get_contents($file);
    }

    if ($return) {
      return $audio;
    }

    header("Cache-Control: no-store, no-cache, must-revalidate");
    header('Content-Type: audio/mpeg');

    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
    echo $audio;
    exit;
  }

  private function determineAudioType(): string {
    $type = 'audio';
    if (isset($_SERVER['HTTP_RANGE'])) {
      $type .= '-range-' . sanitize_text_field(wp_unslash($_SERVER['HTTP_RANGE']));
    }
    return $type;
  }

  public function renderImage($width = null, $height = null, $sessionId = null, $return = false) {
    if (!$this->isSupported()) {
      return false;
    }

    $fontNumbers = array_merge(range(0, 3), [5]); // skip font #4
    $fontNumber = $fontNumbers[mt_rand(0, count($fontNumbers) - 1)];

    $reflector = new \ReflectionClass(CaptchaBuilder::class);
    $captchaDirectory = dirname((string)$reflector->getFileName());
    $font = $captchaDirectory . '/Font/captcha' . $fontNumber . '.ttf';

    $phrase = $this->phrase->getPhraseForType('image', $sessionId);
    $builder = CaptchaBuilder::create($phrase)
      ->setBackgroundColor(255, 255, 255)
      ->setTextColor(1, 1, 1)
      ->setMaxBehindLines(0)
      ->build($width ?: 220, $height ?: 60, $font);

    if ($return) {
      return $builder->get();
    }

    header("Expires: Sat, 01 Jan 2019 01:00:00 GMT"); // time in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('X-Cache-Enabled: False');
    header('X-LiteSpeed-Cache-Control: no-cache');

    header('Content-Type: image/jpeg');
    $builder->output();
    exit;
  }
}
