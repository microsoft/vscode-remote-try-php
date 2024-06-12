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

/* welcome_wizard.html */
class __TwigTemplate_040602ebc7669cdfb9da26116daadadf72855abd5f9690ec0e527b909dcfd9c2 extends Template
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
        return "layout.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layout.html", "welcome_wizard.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        echo "<script>
  var mailpoet_logo_url = '";
        // line 3
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/mailpoet-logo.20200623.png");
        echo "';
  var wizard_sender_illustration_url = '";
        // line 4
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/sender.20200623.png");
        echo "';
  var wizard_tracking_illustration_url = '";
        // line 5
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/tracking.20200623.png");
        echo "';
  var wizard_woocommerce_illustration_url = '";
        // line 6
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/woocommerce.20200623.png");
        echo "';
  var wizard_MSS_pitch_illustration_url = '";
        // line 7
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/illu-pitch-mss.20190912.png");
        echo "';
  var finish_wizard_url = '";
        // line 8
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["finish_wizard_url"] ?? null), "html", null, true);
        echo "';
  var admin_email = ";
        // line 9
        echo json_encode(($context["admin_email"] ?? null));
        echo ";
  var hide_mailpoet_beacon = true;
  var mailpoet_show_customers_import = ";
        // line 11
        echo json_encode(($context["show_customers_import"] ?? null));
        echo ";
  var mailpoet_account_url = '";
        // line 12
        echo $this->extensions['MailPoet\Twig\Functions']->addReferralId(((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&email=") . \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 12), "js")));
        echo "';
  var mailpoet_settings = ";
        // line 13
        echo json_encode(($context["settings"] ?? null));
        echo ";
  var mailpoet_premium_key_valid = ";
        // line 14
        echo json_encode(($context["premium_key_valid"] ?? null));
        echo ";
  var mailpoet_mss_key_valid = ";
        // line 15
        echo json_encode(($context["mss_key_valid"] ?? null));
        echo ";
  var wizard_has_tracking_settings = ";
        // line 16
        echo json_encode(($context["has_tracking_settings"] ?? null));
        echo ";
  var mailpoet_welcome_wizard_current_step = ";
        // line 17
        echo json_encode(($context["welcome_wizard_current_step"] ?? null));
        echo ";
</script>

<div id=\"mailpoet-wizard-container\"></div>

<div class=\"mailpoet-wizard-video\">
  <iframe
    width=\"1\"
    height=\"1\"
    src=\"https://player.vimeo.com/video/279123953\"
    frameborder=\"0\"
  ></iframe>
</div>

";
        // line 31
        $this->loadTemplate("mss_pitch_translations.html", "welcome_wizard.html", 31)->display($context);
        // line 32
        $this->loadTemplate("premium_key_validation_strings.html", "welcome_wizard.html", 32)->display($context);
        // line 33
        $this->loadTemplate("settings_translations.html", "welcome_wizard.html", 33)->display($context);
        // line 34
        echo "
";
    }

    public function getTemplateName()
    {
        return "welcome_wizard.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  128 => 34,  126 => 33,  124 => 32,  122 => 31,  105 => 17,  101 => 16,  97 => 15,  93 => 14,  89 => 13,  85 => 12,  81 => 11,  76 => 9,  72 => 8,  68 => 7,  64 => 6,  60 => 5,  56 => 4,  52 => 3,  49 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "welcome_wizard.html", "/home/circleci/mailpoet/mailpoet/views/welcome_wizard.html");
    }
}
