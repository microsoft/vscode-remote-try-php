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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "  <div id=\"settings_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 8
        yield "      var mailpoet_authorized_emails = ";
        yield json_encode(($context["authorized_emails"] ?? null));
        yield ";
      var mailpoet_verified_sender_domains = ";
        // line 9
        yield json_encode(($context["verified_sender_domains"] ?? null));
        yield ";
      var mailpoet_partially_verified_sender_domains = ";
        // line 10
        yield json_encode(($context["partially_verified_sender_domains"] ?? null));
        yield ";
      var mailpoet_all_sender_domains = ";
        // line 11
        yield json_encode(($context["all_sender_domains"] ?? null));
        yield ";
      var mailpoet_sender_restrictions = ";
        // line 12
        yield json_encode(($context["sender_restrictions"] ?? null));
        yield ";
      var mailpoet_members_plugin_active = ";
        // line 13
        yield json_encode((($context["is_members_plugin_active"] ?? null) == true));
        yield ";
      var mailpoet_settings = ";
        // line 14
        yield json_encode(($context["settings"] ?? null));
        yield ";
      var mailpoet_segments = ";
        // line 15
        yield json_encode(($context["segments"] ?? null));
        yield ";
      var mailpoet_pages = ";
        // line 16
        yield json_encode(($context["pages"] ?? null));
        yield ";
      var mailpoet_mss_key_valid = ";
        // line 17
        yield json_encode(($context["mss_key_valid"] ?? null));
        yield ";
      var mailpoet_premium_key_valid = ";
        // line 18
        yield json_encode(($context["premium_key_valid"] ?? null));
        yield ";
      var mailpoet_paths = ";
        // line 19
        yield json_encode(($context["paths"] ?? null));
        yield ";
      var mailpoet_built_in_captcha_supported = ";
        // line 20
        yield json_encode((($context["built_in_captcha_supported"] ?? null) == true));
        yield ";
      var mailpoet_free_plan_url = \"";
        // line 21
        yield $this->extensions['MailPoet\Twig\Functions']->addReferralId("https://www.mailpoet.com/free-plan");
        yield "\";
      var mailpoet_current_user_email = \"";
        // line 22
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["current_user"] ?? null), "user_email", [], "any", false, false, false, 22), "js", null, true);
        yield "\";
      var mailpoet_hosts = ";
        // line 23
        yield json_encode(($context["hosts"] ?? null));
        yield ";
      var mailpoet_current_site_title = ";
        // line 24
        yield json_encode(($context["current_site_title"] ?? null));
        yield ";
    ";
        // line 26
        yield "  </script>

  ";
        // line 28
        yield from         $this->loadTemplate("settings_translations.html", "settings.html", 28)->unwrap()->yield($context);
        // line 29
        yield "  ";
        yield from         $this->loadTemplate("premium_key_validation_strings.html", "settings.html", 29)->unwrap()->yield($context);
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "settings.html";
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
        return array (  131 => 29,  129 => 28,  125 => 26,  121 => 24,  117 => 23,  113 => 22,  109 => 21,  105 => 20,  101 => 19,  97 => 18,  93 => 17,  89 => 16,  85 => 15,  81 => 14,  77 => 13,  73 => 12,  69 => 11,  65 => 10,  61 => 9,  56 => 8,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "settings.html", "/home/circleci/mailpoet/mailpoet/views/settings.html");
    }
}
