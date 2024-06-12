<?php declare(strict_types = 1);

if (!defined('ABSPATH')) exit;


// phpcs:disable Generic.Files.InlineHTML.Found
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
/**
 * Template file to render the current 'wp_template', specifcally for emails.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <title><?php echo esc_html($subject); ?></title>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="format-detection" content="telephone=no" />
  <?php echo $metaRobots; // HTML defined by MailPoet--do not escape ?>
  <!-- Forced Styles -->
</head>
<body>
    <div class="email_layout_wrapper">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
          <tbody>
            <tr>
              <td class="email_preheader" height="1">
                <?php echo esc_html(wp_strip_all_tags($preHeader)); ?>
              </td>
            </tr>
            <tr>
              <td class="email_content_wrapper">
                <?php echo $templateHtml; ?>
              </td>
            </tr>
          </tbody>
        </table>
  </div>
</body>
</html>
