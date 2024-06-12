<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\Form\Widget;
use MailPoet\WP\Functions as WPFunctions;

class Export {
  public static function getAll() {
    return [
      'html' => static::get('html'),
      'php' => static::get('php'),
      'iframe' => static::get('iframe'),
      'shortcode' => static::get('shortcode'),
    ];
  }

  public static function get($type = 'html') {
    switch ($type) {
      case 'iframe':
        // generate url to load iframe's content
        $iframeUrl = WPFunctions::get()->addQueryArg([
          'mailpoet_form_iframe' => ':form_id:',
        ], WPFunctions::get()->trailingslashit(WPFunctions::get()->siteUrl()));

        $onload = "var _this = this; window.addEventListener('message', function(e) {if(e.data.MailPoetIframeHeight){_this.style.height = e.data.MailPoetIframeHeight;}})";
        // generate iframe
        return join(' ', [
          '<iframe',
          'width="100%"',
          'height="100%"',
          'scrolling="no"',
          'frameborder="0"',
          'src="' . WPFunctions::get()->escUrl($iframeUrl) . '"',
          'class="mailpoet_form_iframe"',
          'id="mailpoet_form_iframe"',
          'vspace="0"',
          'tabindex="0"',
          sprintf('onload="%s"', $onload),
          'marginwidth="0"',
          'marginheight="0"',
          'hspace="0"',
          'allowtransparency="true"></iframe>',
        ]);

      case 'php':
        $output = [
          '$form_widget = new \MailPoet\Form\Widget();',
          'echo $form_widget->widget(array(\'form\' => ' .
            ':form_id:' .
            ', \'form_type\' => \'php\'));',
          ];
        return join("\n", $output);

      case 'html':
        $output = [];

        $output[] = '<!-- ' .
          __(
            'BEGIN Scripts: you should place them in the header of your theme',
            'mailpoet'
          ) .
        ' -->';

        // CSS
        $output[] = '<link rel="stylesheet" type="text/css" href="' .
          Env::$assetsUrl . '/dist/css/mailpoet-public.css?mp_ver=' . MAILPOET_VERSION .
        '" />';

        // jQuery
        $output[] = '<script type="text/javascript" src="' .
          WPFunctions::get()->includesUrl() . 'js/jquery/jquery.js?mp_ver' . MAILPOET_VERSION .
        '"></script>';

        // JS
        $output[] = '<script type="text/javascript" src="' .
          Env::$assetsUrl . '/dist/js/vendor.js?mp_ver=' . MAILPOET_VERSION .
        '"></script>';
        $output[] = '<script type="text/javascript" src="' .
          Env::$assetsUrl . '/dist/js/public.js?mp_ver=' . MAILPOET_VERSION .
        '"></script>';

        // (JS) variables...
        $output[] = '<script type="text/javascript">';
        $output[] = '   var MailPoetForm = MailPoetForm || {';
        $output[] = '       is_rtl: ' . ((int)is_rtl()) . ",";
        $output[] = '       ajax_url: "' . admin_url('admin-ajax.php') . '"';
        $output[] = '   };';
        $output[] = '</script>';
        $output[] = '<!-- ' .
          __('END Scripts', 'mailpoet') .
        '-->';

        $formWidget = new Widget();
        $output[] = $formWidget->widget([
          'form' => ':form_id:',
          'form_type' => 'php',
        ]);
        return join("\n", $output);

      case 'shortcode':
        return '[mailpoet_form id=":form_id:"]';
    }
  }
}
