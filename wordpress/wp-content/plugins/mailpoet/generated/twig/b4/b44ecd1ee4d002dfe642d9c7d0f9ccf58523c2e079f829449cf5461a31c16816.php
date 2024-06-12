<?php

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* emails/statsNotification.html */
class __TwigTemplate_dca57ced6841059bfa2e8b3dbebd25c20bdcb4ab3fe1554e70cfc14ded6da7ab extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "emails/statsNotificationLayout.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("emails/statsNotificationLayout.html", "emails/statsNotification.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "  <tr>
    <td class=\"mailpoet_content\" align=\"center\" style=\"border-collapse:collapse\">
      <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
        <tbody>
        <tr>
          <td style=\"padding-left:0;padding-right:0;border-collapse:collapse\">
            <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mailpoet_cols-one\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
              <tbody>
              <tr>
                <td class=\"mailpoet_spacer\" height=\"36\" valign=\"top\" style=\"border-collapse:collapse\"></td>
              </tr>
              <tr>
                <td class=\"mailpoet_image mailpoet_padded_vertical mailpoet_padded_side\" align=\"center\" valign=\"top\" style=\"border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                  <img src=\"";
        // line 17
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("logo-orange-400x122.png");
        echo "\" width=\"80\" alt=\"new_logo_orange\" style=\"height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center\"/>
                </td>
              </tr>
              <tr>
                <td class=\"mailpoet_spacer\" height=\"26\" valign=\"top\" style=\"border-collapse:collapse\"></td>
              </tr>
              <tr>
                <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                  <h1 style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 12px;color:#111111;font-family:'Trebuchet MS','Lucida Grande','Lucida Sans Unicode','Lucida Sans',Tahoma,sans-serif;font-size:40px;line-height:64px\">
                    <strong>";
        // line 26
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Your stats are in!");
        echo "</strong>
                  </h1>
                </td>
              </tr>
              <tr>
                <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                  <h3 class=\"title\" style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 6px;color:#333333;font-family:'Courier New',Courier,'Lucida Sans Typewriter','Lucida Typewriter',monospace;font-size:20px;line-height:32px\">
                    <em>";
        // line 33
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["subject"] ?? null), "html", null, true);
        echo "</em>
                  </h3>
                </td>
              </tr>
              <tr>
                <td class=\"mailpoet_spacer\" height=\"55\" valign=\"top\" style=\"border-collapse:collapse\"></td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  ";
        // line 48
        if (($context["subscribersLimitReached"] ?? null)) {
            // line 49
            echo "    <tr>
      <td class=\"mailpoet_content\" align=\"center\" style=\"border-collapse:collapse;background-color:#fe5301!important\" bgcolor=\"#fe5301\">
        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0\">
          <tbody>
          <tr>
            <td style=\"border-collapse:collapse;padding-left:0;padding-right:0\">
              <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mailpoet_cols-one\" style=\"border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_spacer\" height=\"26\" valign=\"top\" style=\"border-collapse:collapse\"></td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word\">
                    <table style=\"border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0\" width=\"100%\" cellpadding=\"0\">
                      <tbody>
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px;word-break:break-word;word-wrap:break-word;text-align:left\">
                          <span style=\"color: #ffffff;\"><strong>";
            // line 66
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more than [subscribersLimit] subscribers!"), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
            echo "</strong></span><br><br>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                    <table style=\"border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0\" width=\"100%\" cellpadding=\"0\">
                      <tbody>
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px;word-break:break-word;word-wrap:break-word;text-align:left\">
                          <span style=\"color: #ffffff;\"><strong></strong></span>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                    <table style=\"border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0\" width=\"100%\" cellpadding=\"0\">
                      <tbody>
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px;word-break:break-word;word-wrap:break-word;text-align:left\">
                          <span style=\"color: #ffffff;\">";
            // line 84
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers. You need to upgrade now to be able to continue using MailPoet."), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
            echo "</span>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px\">
                    <div>
                      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0\">
                        <tbody>
                        <tr>
                          <td class=\"mailpoet_button-container\" style=\"border-collapse:collapse;text-align:center\">
                            <!--[if mso]>
                              <v:roundrect xmlns:v=\"urn:schemas-microsoft-com:vml\"
                                           xmlns:w=\"urn:schemas-microsoft-com:office:word\"
                                           href=\"\"
                                           style=\"height:50px; width:288px; v-text-anchor:middle;\"
                                           arcsize=\"6%\"
                                           strokeweight=\"0px\"
                                           strokecolor=\"#0074a2\"
                                           fillcolor=\"#ffffff\">
                                <w:anchorlock/>
                                <center style=\"color:#fe5301;
                                  font-family:Arial;
                                  font-size:20px;
                                  font-weight:bold;\">Upgrade Now
                                </center>
                              </v:roundrect>
                            <![endif]-->
                            <a class=\"mailpoet_button\" href=\"";
            // line 115
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["upgradeNowLink"] ?? null), "html", null, true);
            echo "\" style=\"color:#fe5301;text-decoration:none !important;display:inline-block;-webkit-text-size-adjust:none;mso-hide:all;text-align:center;background-color:#ffffff;border-color:#0074a2;border-width:0px;border-radius:3px;border-style:solid;width:288px;line-height:50px;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:20px;font-weight:normal\">Upgrade Now</a>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_spacer\" height=\"26\" valign=\"top\" style=\"border-collapse:collapse\"></td>
                </tr>
                <tr>
                  <td class=\"mailpoet_spacer\" bgcolor=\"#ffffff\" height=\"26\" valign=\"top\" style=\"border-collapse:collapse\"></td>
                </tr>
                </tbody>
              </table>
            </td>
          </tr>
          </tbody>
        </table>
      </td>
    </tr>
  ";
        }
        // line 138
        echo "  <tr>
    <td class=\"mailpoet_content-cols-two\" align=\"left\" style=\"border-collapse:collapse\">
      <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
        <tbody>
        <tr>
          <td align=\"center\" style=\"font-size:0;border-collapse:collapse\">
            <!--[if mso]>
            <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
              <tbody>
              <tr>
                <td width=\"330\" valign=\"top\">
            <![endif]-->
            <div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
              <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\"
                      style=\"border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <div>
                      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
                        <tr>
                          <td class=\"mailpoet_button-container\" style=\"text-align:center;border-collapse:collapse\">
                            <a class=\"mailpoet_button\" href=\"\" style=\"display:inline-block;-webkit-text-size-adjust:none;mso-hide:all;text-decoration:none;text-align:center;background-color:";
        // line 160
        echo $this->extensions['MailPoet\Twig\Functions']->statsColor(($context["clicked"] ?? null));
        echo " ;border-color:#0074a2 ;border-width:0px ;border-radius:3px ;border-style:solid ;width:100px ;line-height:20px ;color:#ffffff ;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif ;font-size:10px ;font-weight:normal \">
                              ";
        // line 161
        echo $this->extensions['MailPoet\Twig\Functions']->clickedStatsText(($context["clicked"] ?? null));
        echo "
                            </a>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-left:20px;padding-right:20px\">
                    <h2 style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 12px;color:#222222;font-family:'Courier New',Courier,'Lucida Sans Typewriter','Lucida Typewriter',monospace;font-size:40px;line-height:64px\">
                      <span style=\"color: ";
        // line 172
        echo $this->extensions['MailPoet\Twig\Functions']->statsColor(($context["clicked"] ?? null));
        echo "\">
                        <strong>";
        // line 173
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["clicked"] ?? null));
        echo "%</strong>
                      </span>
                    </h2>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                          <span style=\"color: ";
        // line 183
        echo $this->extensions['MailPoet\Twig\Functions']->statsColor(($context["clicked"] ?? null));
        echo "\">
                            ";
        // line 184
        echo $this->extensions['MailPoet\Twig\I18n']->translate("clicked");
        echo "
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso]>
            </td>
            </tr>
            </tbody>
            </table>
            <![endif]-->
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  <tr>
    <td class=\"mailpoet_content-cols-two\" align=\"left\" style=\"border-collapse:collapse\">
      <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
        <tbody>
        <tr>
          <td align=\"center\" style=\"font-size:0;border-collapse:collapse\">
            <!--[if mso]>
            <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
              <tbody>
              <tr>
                <td width=\"330\" valign=\"top\">
            <![endif]-->
            <div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
              <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-left:20px;padding-right:20px\">
                    <h2 style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 12px;color:#222222;font-family:'Courier New',Courier,'Lucida Sans Typewriter','Lucida Typewriter',monospace;font-size:40px;line-height:64px\">
                      <span>
                        <strong>";
        // line 225
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["opened"] ?? null));
        echo "%</strong>
                      </span>
                    </h2>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                          <span>
                            ";
        // line 236
        echo $this->extensions['MailPoet\Twig\I18n']->translate("opened");
        echo "
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso]>
            </td>
            <td width=\"330\" valign=\"top\">
            <![endif]-->
            <div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
              <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-left:20px;padding-right:20px\">
                    <h2 style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 12px;color:#222222;font-family:'Courier New',Courier,'Lucida Sans Typewriter','Lucida Typewriter',monospace;font-size:40px;line-height:64px\">
                      <span>
                        <strong>";
        // line 257
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["machineOpened"] ?? null));
        echo "%</strong>
                      </span>
                    </h2>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                          <span>
                            ";
        // line 268
        echo $this->extensions['MailPoet\Twig\I18n']->translate("machine-opened");
        echo "
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso]>
            </td>
            </tr>
            </tbody>
            </table>
            <![endif]-->
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  <tr>
    <td class=\"mailpoet_content-cols-two\" align=\"left\" style=\"border-collapse:collapse\">
      <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
        <tbody>
        <tr>
          <td align=\"center\" style=\"font-size:0;border-collapse:collapse\">
            <!--[if mso]>
            <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
              <tbody>
              <tr>
                <td width=\"330\" valign=\"top\">
            <![endif]-->
            <div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
              <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-left:20px;padding-right:20px\">
                    <h2 style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 12px;color:#222222;font-family:'Courier New',Courier,'Lucida Sans Typewriter','Lucida Typewriter',monospace;font-size:40px;line-height:64px\">
                        <span>
                          <strong>";
        // line 309
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["unsubscribed"] ?? null));
        echo "%</strong>
                        </span>
                    </h2>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                            <span>
                              ";
        // line 320
        echo $this->extensions['MailPoet\Twig\I18n']->translate("unsubscribed");
        echo "
                            </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso]>
            </td>
            <td width=\"330\" valign=\"top\">
            <![endif]-->
            <div style=\"display:inline-block; max-width:330px; vertical-align:top; width:100%;\">
              <table width=\"330\" class=\"mailpoet_cols-two\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"left\" style=\"width:100%;max-width:330px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-left:20px;padding-right:20px\">
                    <h2 style=\"text-align:center;padding:0;font-style:normal;font-weight:normal;margin:0 0 12px;color:#222222;font-family:'Courier New',Courier,'Lucida Sans Typewriter','Lucida Typewriter',monospace;font-size:40px;line-height:64px\">
                        <span>
                          <strong>";
        // line 341
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["bounced"] ?? null));
        echo "%</strong>
                        </span>
                    </h2>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                            <span>
                              ";
        // line 352
        echo $this->extensions['MailPoet\Twig\I18n']->translate("bounced");
        echo "
                            </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso]>
            </td>
            </tr>
            </tbody>
            </table>
            <![endif]-->
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  ";
        // line 374
        if ((($context["topLinkClicks"] ?? null) > 0)) {
            // line 375
            echo "    <tr>
      <td class=\"mailpoet_content\" align=\"center\" style=\"border-collapse:collapse\">
        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
          <tbody>
          <tr>
            <td style=\"padding-left:0;padding-right:0;border-collapse:collapse\">
              <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mailpoet_cols-one\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_divider\" valign=\"top\" style=\"padding:26.5px 20px 26.5px 20px;border-collapse:collapse\">
                    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
                      <tr>
                        <td class=\"mailpoet_divider-cell\" style=\"border-top-width:1px;border-top-style:solid;border-top-color:#e8e8e8;border-collapse:collapse\"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </td>
          </tr>
          </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td class=\"mailpoet_content\" align=\"center\" style=\"border-collapse:collapse\">
        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
          <tbody>
          <tr>
            <td style=\"padding-left:0;padding-right:0;border-collapse:collapse\">
              <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mailpoet_cols-one\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
                <tbody>
                <tr>
                  <td class=\"mailpoet_header_footer_padded mailpoet_header\" style=\"line-height:38.4px;text-align:center ;color:#222222 ;font-family:'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif ;font-size:24px ;border-collapse:collapse;padding:10px 20px\">
                    <span style=\"font-weight: 600;\">
                      ";
            // line 411
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Most clicked link");
            echo "
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class=\"mailpoet_text mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"word-break:break-word;word-wrap:break-word;padding-top:0;border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                          ";
            // line 420
            if ((is_string($__internal_compile_0 = ($context["topLink"] ?? null)) && is_string($__internal_compile_1 = "http") && ('' === $__internal_compile_1 || 0 === strpos($__internal_compile_0, $__internal_compile_1)))) {
                // line 421
                echo "                            <a href=\"";
                echo \MailPoetVendor\twig_escape_filter($this->env, ($context["topLink"] ?? null), "html", null, true);
                echo "\" target=\"_blank\" rel=\"noopener noreferrer\"
                               style=\"color:#008282;text-decoration:underline\">
                              ";
                // line 423
                echo \MailPoetVendor\twig_escape_filter($this->env, ($context["topLink"] ?? null), "html", null, true);
                echo "
                            </a>
                          ";
            } else {
                // line 426
                echo "                            ";
                echo \MailPoetVendor\twig_escape_filter($this->env, ($context["topLink"] ?? null), "html", null, true);
                echo "
                          ";
            }
            // line 428
            echo "                        </td>
                      </tr>
                    </table>
                    <table style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\" width=\"100%\" cellpadding=\"0\">
                      <tr>
                        <td class=\"mailpoet_paragraph\" style=\"word-break:break-word;word-wrap:break-word;text-align:center;border-collapse:collapse;color:#000000;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:16px;line-height:25.6px\">
                          <span style=\"color: #000000;\">
                            ";
            // line 435
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("%s unique clicks"), ["%s" => ($context["topLinkClicks"] ?? null)]), "html", null, true);
            echo "
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </tbody>
              </table>
            </td>
          </tr>
          </tbody>
        </table>
      </td>
    </tr>
  ";
        }
        // line 451
        echo "  <tr>
    <td class=\"mailpoet_content\" align=\"center\" style=\"border-collapse:collapse\">
      <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
        <tbody>
        <tr>
          <td style=\"padding-left:0;padding-right:0;border-collapse:collapse\">
            <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mailpoet_cols-one\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;border-collapse:collapse\">
              <tbody>
              <tr>
                <td class=\"mailpoet_divider\" valign=\"top\" style=\"padding:6.5px 20px 6.5px 20px;border-collapse:collapse\">
                  <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
                    <tr>
                      <td class=\"mailpoet_divider-cell\" style=\"border-top-width:1px;border-top-style:solid;border-top-color:#e8e8e8;border-collapse:collapse\"></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class=\"mailpoet_spacer\" height=\"30\" valign=\"top\" style=\"border-collapse:collapse\"></td>
              </tr>
              <tr>
                <td class=\"mailpoet_padded_vertical mailpoet_padded_side\" valign=\"top\" style=\"border-collapse:collapse;padding-bottom:20px;padding-left:20px;padding-right:20px\">
                  <div>
                    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;border-collapse:collapse\">
                      <tr>
                        <td class=\"mailpoet_button-container\" style=\"text-align:center;border-collapse:collapse\">
                          <a class=\"mailpoet_button\" href=\"";
        // line 477
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["linkStats"] ?? null), "html", null, true);
        echo "\" style=\"display:inline-block;-webkit-text-size-adjust:none;mso-hide:all;text-decoration:none;text-align:center;background-color:#fe5301 ;border-color:#0074a2 ;border-width:0px ;border-radius:3px ;border-style:solid ;width:288px ;line-height:50px ;color:#ffffff ;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif ;font-size:20px ;font-weight:normal \">
                            ";
        // line 478
        echo $this->extensions['MailPoet\Twig\I18n']->translate("View all stats");
        echo "
                          </a>
                        </td>
                      </tr>
                    </table>
                  </div>
                </td>
              </tr>
              <tr>
                <td class=\"mailpoet_spacer\" height=\"20\" valign=\"top\" style=\"border-collapse:collapse\"></td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
";
    }

    public function getTemplateName()
    {
        return "emails/statsNotification.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  619 => 478,  615 => 477,  587 => 451,  568 => 435,  559 => 428,  553 => 426,  547 => 423,  541 => 421,  539 => 420,  527 => 411,  489 => 375,  487 => 374,  462 => 352,  448 => 341,  424 => 320,  410 => 309,  366 => 268,  352 => 257,  328 => 236,  314 => 225,  270 => 184,  266 => 183,  253 => 173,  249 => 172,  235 => 161,  231 => 160,  207 => 138,  181 => 115,  147 => 84,  126 => 66,  107 => 49,  105 => 48,  87 => 33,  77 => 26,  65 => 17,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/statsNotification.html", "/home/circleci/mailpoet/mailpoet/views/emails/statsNotification.html");
    }
}
