<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription\Captcha;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Gregwar\Captcha\PhraseBuilder;

class CaptchaPhrase {


  /** @var CaptchaSession  */
  private $session;

  /** @var PhraseBuilder  */
  private $phraseBuilder;

  public function __construct(
    CaptchaSession $session,
    PhraseBuilder $phraseBuilder = null
  ) {
    $this->session = $session;
    $this->phraseBuilder = $phraseBuilder ? $phraseBuilder : new PhraseBuilder();
  }

  public function getPhrase(): ?string {
    $storage = $this->session->getCaptchaHash();
    return (isset($storage['phrase']) && is_string($storage['phrase'])) ? $storage['phrase'] : null;
  }

  public function resetPhrase() {
    $this->session->setCaptchaHash(null);
  }

  public function getPhraseForType(string $type, string $sessionId = null): string {
    $this->session->init($sessionId);
    $storage = $this->session->getCaptchaHash();
    if (!$storage) {
      $storage = [
        'phrase' => $this->phraseBuilder->build(),
        'total_loaded' => 1,
        'loaded_by_types' => [],
      ];
    }
    if (!isset($storage['loaded_by_types'][$type])) {
      $storage['loaded_by_types'][$type] = 0;
    }

    if ($this->needsToRegenerateCaptcha($storage, $type)) {
      $storage['phrase'] = $this->phraseBuilder->build();
      $storage['total_loaded']++;
    }
    $storage['loaded_by_types'][$type]++;

    $this->session->setCaptchaHash($storage);
    return $storage['phrase'];
  }

  private function needsToRegenerateCaptcha(array $storage, string $type): bool {
    return $storage['loaded_by_types'][$type] === $storage['total_loaded'];
  }
}
