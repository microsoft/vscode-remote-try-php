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

/* settings.html */
class __TwigTemplate_d7e5c6eabd771def3ba904afb1433c0226390ff983f51ef08486a758acfd35d3 extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "settings.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "  <div id=\"settings_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 8
        echo "      var mailpoet_authorized_emails = ";
        echo json_encode(($context["authorized_emails"] ?? null));
        echo ";
      var mailpoet_verified_sender_domains = ";
        // line 9
        echo json_encode(($context["verified_sender_domains"] ?? null));
        echo ";
      var mailpoet_partially_verified_sender_domains = ";
        // line 10
        echo json_encode(($context["partially_verified_sender_domains"] ?? null));
        echo ";
      var mailpoet_all_sender_domains = ";
        // line 11
        echo json_encode(($context["all_sender_domains"] ?? null));
        echo ";
      var mailpoet_sender_restrictions = ";
        // line 12
        echo json_encode(($context["sender_restrictions"] ?? null));
        echo ";
      var mailpoet_members_plugin_active = ";
        // line 13
        echo json_encode((($context["is_members_plugin_active"] ?? null) == true));
        echo ";
      var mailpoet_settings = ";
        // line 14
        echo json_encode(($context["settings"] ?? null));
        echo ";
      var mailpoet_segments = ";
        // line 15
        echo json_encode(($context["segments"] ?? null));
        echo ";
      var mailpoet_pages = ";
        // line 16
        echo json_encode(($context["pages"] ?? null));
        echo ";
      var mailpoet_mss_key_valid = ";
        // line 17
        echo json_encode(($context["mss_key_valid"] ?? null));
        echo ";
      var mailpoet_premium_key_valid = ";
        // line 18
        echo json_encode(($context["premium_key_valid"] ?? null));
        echo ";
      var mailpoet_paths = ";
        // line 19
        echo json_encode(($context["paths"] ?? null));
        echo ";
      var mailpoet_built_in_captcha_supported = ";
        // line 20
        echo json_encode((($context["built_in_captcha_supported"] ?? null) == true));
        echo ";
      var mailpoet_free_plan_url = \"";
        // line 21
        echo $this->extensions['MailPoet\Twig\Functions']->addReferralId("https://www.mailpoet.com/free-plan");
        echo "\";
      var mailpoet_current_user_email = \"";
        // line 22
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_user"] ?? null), "user_email", [], "any", false, false, false, 22), "js", null, true);
        echo "\";
      var mailpoet_hosts = ";
        // line 23
        echo json_encode(($context["hosts"] ?? null));
        echo ";
      var mailpoet_current_site_title = ";
        // line 24
        echo json_encode(($context["current_site_title"] ?? null));
        echo ";
    ";
        // line 26
        echo "  </script>

  ";
        // line 28
        $this->loadTemplate("settings_translations.html", "settings.html", 28)->display($context);
        // line 29
        echo "  ";
        $this->loadTemplate("premium_key_validation_strings.html", "settings.html", 29)->display($context);
    }

    public function getTemplateName()
    {
        return "settings.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  130 => 29,  128 => 28,  124 => 26,  120 => 24,  116 => 23,  112 => 22,  108 => 21,  104 => 20,  100 => 19,  96 => 18,  92 => 17,  88 => 16,  84 => 15,  80 => 14,  76 => 13,  72 => 12,  68 => 11,  64 => 10,  60 => 9,  55 => 8,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "settings.html", "/home/circleci/mailpoet/mailpoet/views/settings.html");
    }
}
