<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\SuccessResponse;
use MailPoet\Config\AccessControl;
use MailPoet\Newsletter\AutomatedLatestContent as ALC;
use MailPoet\Newsletter\BlockPostQuery;
use MailPoet\Util\APIPermissionHelper;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Posts as WPPosts;

class AutomatedLatestContent extends APIEndpoint {
  /** @var ALC  */
  public $ALC;

  /*** @var WPFunctions */
  private $wp;

  /*** @var APIPermissionHelper */
  private $permissionHelper;

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  public function __construct(
    ALC $alc,
    APIPermissionHelper $permissionHelper,
    WPFunctions $wp
  ) {
    $this->ALC = $alc;
    $this->wp = $wp;
    $this->permissionHelper = $permissionHelper;
  }

  public function getPostTypes() {
    $postTypes = array_map(function($postType) {
      return [
        'name' => $postType->name,
        'label' => $postType->label,
      ];
    }, WPPosts::getTypes([], 'objects'));
    return $this->successResponse(
      array_filter($postTypes)
    );
  }

  public function getTaxonomies($data = []) {
    $postType = (isset($data['postType'])) ? $data['postType'] : 'post';
    $allTaxonomies = WPFunctions::get()->getObjectTaxonomies($postType, 'objects');
    $taxonomiesWithLabel = array_filter($allTaxonomies, function($taxonomy) {
      return $taxonomy->label;
    });
    return $this->successResponse($taxonomiesWithLabel);
  }

  public function getTerms($data = []) {
    $taxonomies = (isset($data['taxonomies'])) ? $data['taxonomies'] : [];
    $search = (isset($data['search'])) ? $data['search'] : '';
    $limit = (isset($data['limit'])) ? (int)$data['limit'] : 100;
    $page = (isset($data['page'])) ? (int)$data['page'] : 1;
    $args = [
      'taxonomy' => $taxonomies,
      'hide_empty' => false,
      'search' => $search,
      'number' => $limit,
      'offset' => $limit * ($page - 1),
      'orderby' => 'name',
      'order' => 'ASC',
    ];

    $args = (array)$this->wp->applyFilters('mailpoet_search_terms_args', $args);
    $terms = WPFunctions::get()->getTerms($args);

    return $this->successResponse(array_values($terms));
  }

  /**
   * Fetches posts for Posts static block
   */
  public function getPosts(array $data = []): SuccessResponse {
    return $this->successResponse(
      $this->getPermittedPosts($this->ALC->getPosts(new BlockPostQuery(['args' => $data, 'dynamic' => false])))
    );
  }

  /**
   * Fetches products for Abandoned Cart Content dynamic block
   */
  public function getTransformedPosts(array $data = []): SuccessResponse {
    $posts = $this->getPermittedPosts($this->ALC->getPosts(new BlockPostQuery([
      'args' => $data,
      // If the request is for Posts or Products block then we are fetching data for a static block
      'dynamic' => !(isset($data['type']) && in_array($data['type'], ["posts", "products"])),
    ])));
    return $this->successResponse(
      $this->ALC->transformPosts($data, $posts)
    );
  }

  /**
   * Fetches different post types for ALC dynamic block
   */
  public function getBulkTransformedPosts(array $data = []): SuccessResponse {
    $usedPosts = [];
    $renderedPosts = [];

    foreach ($data['blocks'] as $block) {
      $query = new BlockPostQuery(['args' => $block, 'postsToExclude' => $usedPosts]);
      $posts = $this->getPermittedPosts($this->ALC->getPosts($query));
      $renderedPosts[] = $this->ALC->transformPosts($block, $posts);

      foreach ($posts as $post) {
        $usedPosts[] = $post->ID;
      }
    }

    return $this->successResponse($renderedPosts);
  }

  /**
   * @param \WP_Post[] $posts
   * @return \WP_Post[]
   */
  private function getPermittedPosts($posts) {
    return array_filter($posts, function ($post) {
      return $this->permissionHelper->checkReadPermission($post);
    });
  }
}
