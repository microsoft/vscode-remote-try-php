<?php

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\CoreExtension;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* emails/statsNotificationLayout.html */
class __TwigTemplate_983c70cc527e9ec7b888be667e99a11703d8dd9de70fb8285afd14f53c34c1b8 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<html lang=\"";
        yield $this->extensions['MailPoet\Twig\Assets']->language();
        yield "\" style=\"margin:0;padding:0\">
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
  <meta name=\"format-detection\" content=\"telephone=no\" />
  <title>";
        // line 7
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["subject"] ?? null), "html", null, true);
        yield "</title>
  <style type=\"text/css\"> @media screen and (max-width: 480px) {
    .mailpoet_button {width:100% !important;}
  }
  @media screen and (max-width: 599px) {
    .mailpoet_header {
      padding: 10px 20px;
    }
    .mailpoet_button {
      width: 100% !important;
      padding: 5px 0 !important;
      box-sizing:border-box !important;
    }
    div, .mailpoet_cols-two {
      max-width: 100% !important;
    }
  }
  </style>

</head>
<body leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" style=\"margin:0;padding:0;background-color:#f0f0f0\">
<table class=\"mailpoet_template\" border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
  <tbody>
  <tr>
    ";
        // line 31
        if (array_key_exists("preheader", $context)) {
            // line 32
            yield "      <td class=\"mailpoet_preheader\" style=\"-webkit-text-size-adjust:none;font-size:1px;line-height:1px;color:#ffffff;border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;max-height:0;max-width:0;opacity:0;overflow:hidden\" height=\"1\">
    ";
            // line 33
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["preheader"] ?? null), "html", null, true);
            yield "
  </td>
    ";
        }
        // line 36
        yield "  </tr>
  <tr>
    <td align=\"center\" class=\"mailpoet-wrapper\" valign=\"top\" style=\"border-collapse:collapse;background-color:#f0f0f0\"><!--[if mso]>
      <table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"
             width=\"660\">
        <tr>
          <td class=\"mailpoet_content-wrapper\" align=\"center\" valign=\"top\" width=\"660\">
      <![endif]--><table class=\"mailpoet_content-wrapper\" border=\"0\" width=\"660\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%;border-collapse:collapse;background-color:#ffffff\">
        <tbody>

        ";
        // line 46
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 47
        yield "
        <tr>
          <td class=\"mailpoet_content-cols-two\" align=\"left\" style=\"border-collapse:collapse;background-color:#fe5301\" bgcolor=\"#fe5301\">
            <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
              <tbody>
              <tr>
                <td align=\"center\" style=\"font-size:0;border-collapse:collapse\"><!--[if mso]>
                  <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
                    <tbody>
                    <tr>
                      <td width=\"330\" valign=\"top\">
                  <![endif]--><div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
                    <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                      <tbody>
                      <tr>
                        <td class=\"mailpoet_spacer\" bgcolor=\"#fe5301\" height=\"24\" valign=\"top\" style=\"border-collapse:collapse\"></td>
                      </tr>
                      <tr>
                        <td class=\"mailpoet_image mailpoet_padded_vertical mailpoet_padded_side\" align=\"left\" valign=\"top\" style=\"border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                          <img src=\"";
        // line 66
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("logo-white-400x122.png");
        yield "\" width=\"130\" alt=\"new_logo_white\" style=\"height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center\" />
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div><!--[if mso]>
                  </td>
                  <td width=\"330\" valign=\"top\">
                  <![endif]--><div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
                    <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                      <tbody>
                      <tr>
                        <td class=\"mailpoet_spacer\" bgcolor=\"#fe5301\" height=\"20\" valign=\"top\" style=\"border-collapse:collapse\"></td>
                      </tr>
                      <tr>
                        <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                          <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                            <tr>
                              <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:right;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                                <span style=\"color: #ffffff;\">
                                  <a href=\"https://www.mailpoet.com/how-to-improve-open-rates/\" title=\"";
        // line 86
        yield $this->extensions['MailPoet\Twig\I18n']->translate("How to Improve Open and Click Rates");
        yield "\" style=\"color:#fff;text-decoration:underline\">
                                    ";
        // line 87
        yield $this->extensions['MailPoet\Twig\I18n']->translate("How to improve my open rate?");
        yield "
                                  </a>
                                </span>
                              </td>
                            </tr></table>
                          <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                            <tr>
                              <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:right;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                                <span style=\"color: #ffffff;\"><a href=\"https://www.mailpoet.com/how-to-improve-click-rates/\" title=\"";
        // line 95
        yield $this->extensions['MailPoet\Twig\I18n']->translate("How to Improve Open and Click Rates");
        yield "\" style=\"color:#fff;text-decoration:underline\">
                                  ";
        // line 96
        yield $this->extensions['MailPoet\Twig\I18n']->translate("And my click rate?");
        yield "
                                </a></span>
                              </td>
                            </tr></table>
                          <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                            <tr>
                              <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:right;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                                <a href=\"";
        // line 103
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["linkSettings"] ?? null), "html", null, true);
        yield "\" style=\"color:#fff;text-decoration:underline\">
                                  ";
        // line 104
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Disable these emails");
        yield "
                                </a>
                              </td>
                            </tr></table>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div><!--[if mso]>
                  </td>
                  </tr>
                  </tbody>
                  </table>
                  <![endif]--></td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table><!--[if mso]>
      </td>
      </tr>
      </table>
      <![endif]--></td>
  </tr>
  </tbody>
</table>
</body>
</html>
";
        return; yield '';
    }

    // line 46
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "emails/statsNotificationLayout.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  214 => 46,  178 => 104,  174 => 103,  164 => 96,  160 => 95,  149 => 87,  145 => 86,  122 => 66,  101 => 47,  99 => 46,  87 => 36,  81 => 33,  78 => 32,  76 => 31,  49 => 7,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/statsNotificationLayout.html", "/home/circleci/mailpoet/mailpoet/views/emails/statsNotificationLayout.html");
    }
}
