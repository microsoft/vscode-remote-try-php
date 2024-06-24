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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        yield "<script>
  var mailpoet_logo_url = '";
        // line 3
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/mailpoet-logo.20200623.png");
        yield "';
  var wizard_sender_illustration_url = '";
        // line 4
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/sender.20200623.png");
        yield "';
  var wizard_tracking_illustration_url = '";
        // line 5
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/tracking.20200623.png");
        yield "';
  var wizard_woocommerce_illustration_url = '";
        // line 6
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/woocommerce.20200623.png");
        yield "';
  var wizard_MSS_pitch_illustration_url = '";
        // line 7
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/illu-pitch-mss.20190912.png");
        yield "';
  var finish_wizard_url = '";
        // line 8
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["finish_wizard_url"] ?? null), "html", null, true);
        yield "';
  var admin_email = ";
        // line 9
        yield json_encode(($context["admin_email"] ?? null));
        yield ";
  var hide_mailpoet_beacon = true;
  var mailpoet_show_customers_import = ";
        // line 11
        yield json_encode(($context["show_customers_import"] ?? null));
        yield ";
  var mailpoet_account_url = '";
        // line 12
        yield $this->extensions['MailPoet\Twig\Functions']->addReferralId(((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&email=") . $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 12), "js")));
        yield "';
  var mailpoet_settings = ";
        // line 13
        yield json_encode(($context["settings"] ?? null));
        yield ";
  var mailpoet_premium_key_valid = ";
        // line 14
        yield json_encode(($context["premium_key_valid"] ?? null));
        yield ";
  var mailpoet_mss_key_valid = ";
        // line 15
        yield json_encode(($context["mss_key_valid"] ?? null));
        yield ";
  var wizard_has_tracking_settings = ";
        // line 16
        yield json_encode(($context["has_tracking_settings"] ?? null));
        yield ";
  var mailpoet_welcome_wizard_current_step = ";
        // line 17
        yield json_encode(($context["welcome_wizard_current_step"] ?? null));
        yield ";
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
        yield from         $this->loadTemplate("mss_pitch_translations.html", "welcome_wizard.html", 31)->unwrap()->yield($context);
        // line 32
        yield from         $this->loadTemplate("premium_key_validation_strings.html", "welcome_wizard.html", 32)->unwrap()->yield($context);
        // line 33
        yield from         $this->loadTemplate("settings_translations.html", "welcome_wizard.html", 33)->unwrap()->yield($context);
        // line 34
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "welcome_wizard.html";
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
        return array (  129 => 34,  127 => 33,  125 => 32,  123 => 31,  106 => 17,  102 => 16,  98 => 15,  94 => 14,  90 => 13,  86 => 12,  82 => 11,  77 => 9,  73 => 8,  69 => 7,  65 => 6,  61 => 5,  57 => 4,  53 => 3,  50 => 2,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "welcome_wizard.html", "/home/circleci/mailpoet/mailpoet/views/welcome_wizard.html");
    }
}
