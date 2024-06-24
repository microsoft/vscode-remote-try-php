<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder;
use MailPoet\Listing\PageLimit;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\UserFlagsController;
use MailPoet\WP\Functions as WPFunctions;

class Forms {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var PageLimit */
  private $listingPageLimit;

  /** @var UserFlagsController */
  private $userFlags;

  /** @var WPFunctions */
  private $wp;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SegmentsResponseBuilder */
  private $segmentsResponseBuilder;

  public function __construct(
    PageRenderer $pageRenderer,
    PageLimit $listingPageLimit,
    UserFlagsController $userFlags,
    SegmentsRepository $segmentsRepository,
    SegmentsResponseBuilder $segmentsResponseBuilder,
    WPFunctions $wp
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->listingPageLimit = $listingPageLimit;
    $this->userFlags = $userFlags;
    $this->wp = $wp;
    $this->segmentsRepository = $segmentsRepository;
    $this->segmentsResponseBuilder = $segmentsResponseBuilder;
  }

  public function render() {
    $data = [];
    $data['items_per_page'] = $this->listingPageLimit->getLimitPerPage('forms');
    $data['segments'] = $this->segmentsResponseBuilder->buildForListing($this->segmentsRepository->findAll());

    $data = $this->getNPSSurveyData($data);

    $this->pageRenderer->displayPage('forms.html', $data);
  }

  public function getNPSSurveyData($data) {
    $data['display_nps_survey'] = false;
    if ($this->userFlags->get('display_new_form_editor_nps_survey')) {
      $data['current_wp_user'] = $this->wp->wpGetCurrentUser()->to_array();
      $data['current_wp_user_firstname'] = $this->wp->wpGetCurrentUser()->user_firstname;
      $data['display_nps_survey'] = true;
      $this->userFlags->set('display_new_form_editor_nps_survey', false);
    }
    return $data;
  }
}
