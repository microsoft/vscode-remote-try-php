<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Settings;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\UserFlagEntity;
use MailPoet\WP\Functions as WPFunctions;

class UserFlagsController {

  /** @var array|null */
  private $data = null;

  /** @var array */
  private $defaults;

  /** @var UserFlagsRepository */
  private $userFlagsRepository;

  public function __construct(
    UserFlagsRepository $userFlagsRepository
  ) {
    $this->defaults = [
      'last_announcement_seen' => false,
      'editor_tutorial_seen' => false,
      'form_editor_tutorial_seen' => false,
      'display_new_form_editor_nps_survey' => false,
      'transactional_emails_opt_in_notice_dismissed' => false,
      'legacy_automations_notice_dismissed' => false,
      'legacy_automatic_emails_notice_dismissed' => false,
    ];
    $this->userFlagsRepository = $userFlagsRepository;
  }

  public function get($name) {
    $this->ensureLoaded();
    if (!isset($this->data[$name])) {
      return $this->defaults[$name];
    }
    return $this->data[$name];
  }

  public function getAll() {
    $this->ensureLoaded();
    $data = $this->data;
    if (!is_array($data)) {
      $data = [];
    }
    return array_merge($this->defaults, $data);
  }

  public function set($name, $value) {
    $currentUserId = WPFunctions::get()->getCurrentUserId();
    $flag = $this->userFlagsRepository->findOneBy([
      'userId' => $currentUserId,
      'name' => $name,
    ]);

    if (!$flag) {
      $flag = new UserFlagEntity();
      $flag->setUserId($currentUserId);
      $flag->setName($name);
      $this->userFlagsRepository->persist($flag);
    }
    $flag->setValue($value);
    $this->userFlagsRepository->flush();

    if ($this->isLoaded()) {
      $this->data[$name] = $value;
    }
  }

  public function delete($name) {
    $currentUserId = WPFunctions::get()->getCurrentUserId();
    $flag = $this->userFlagsRepository->findOneBy([
      'userId' => $currentUserId,
      'name' => $name,
    ]);

    if (!$flag) {
      return;
    }

    $this->userFlagsRepository->remove($flag);
    $this->userFlagsRepository->flush();

    if ($this->isLoaded()) {
      unset($this->data[$name]);
    }
  }

  private function load() {
    $currentUserId = WPFunctions::get()->getCurrentUserId();
    $flags = $this->userFlagsRepository->findBy(['userId' => $currentUserId]);
    $this->data = [];
    foreach ($flags as $flag) {
      $this->data[$flag->getName()] = $flag->getValue();
    }
  }

  private function isLoaded() {
    return $this->data !== null;
  }

  private function ensureLoaded() {
    if (!$this->isLoaded()) {
      $this->load();
    }
  }
}
