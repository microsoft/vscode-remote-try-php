<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer;

class WooSystemInfoController {


  /** @var WooSystemInfo  */
  private $systemInfo;

  private $renderer;

  public function __construct(
    WooSystemInfo $systemInfo,
    Renderer $renderer
  ) {
    $this->systemInfo = $systemInfo;
    $this->renderer = $renderer;
  }

  public function render() {

    $output = $this->renderer->render('woo_system_info.html', [
      'system_info' => $this->systemInfo->toArray(),
    ]);

    // We are in control of the template and the data can be considered safe at this point
    echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
  }

  public function addFields($response) {
    $response->data['mailpoet'] = $this->systemInfo->toArray();
    return $response;
  }

  public function addSchema($schema) {
    $schema['mailpoet'] = [
      [
        'description' => __('MailPoet', 'mailpoet'),
        'type' => 'object',
        'context' => ['view'],
        'readonly' => true,
        'properties' => [
          'sending_method' => [
            'description' => __('What method is used to sent out newsletters?', 'mailpoet'),
            'type' => 'boolean',
            'context' => ['view'],
            'readonly' => true,
          ],
          'transactional_emails' => [
            'description' => __('With which method are transactional emails sent?', 'mailpoet'),
            'type' => 'string',
            'context' => ['view'],
            'readonly' => true,
          ],
          'task_scheduler_method' => [
            'description' => __('What method controls the cron job?', 'mailpoet'),
            'type' => 'string',
            'context' => ['view'],
            'readonly' => true,
          ],
          'cron_ping_url' => [
            'description' => __('The URL which needs to be pinged to get the cron started?', 'mailpoet'),
            'type' => 'string',
            'context' => ['view'],
            'readonly' => true,
          ],
        ],
          ],
        ];

    return $schema;
  }
}
