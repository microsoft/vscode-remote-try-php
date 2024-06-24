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

/* segments/dynamic.html */
class __TwigTemplate_756448057f537f5715d1a262e5a4bc2ae04cea7f694f005ae4bcfe6c68608edd extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'after_translations' => [$this, 'block_after_translations'],
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
        $this->parent = $this->loadTemplate("layout.html", "segments/dynamic.html", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "  <div id=\"dynamic_segments_container\"></div>

  <script type=\"text/javascript\">
    var mailpoet_dynamic_segment_count = ";
        // line 7
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["dynamic_segment_count"] ?? null), "html", null, true);
        yield ";
    var mailpoet_listing_per_page = ";
        // line 8
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["items_per_page"] ?? null), "html", null, true);
        yield ";
    var mailpoet_custom_fields = ";
        // line 9
        yield json_encode(($context["custom_fields"] ?? null));
        yield ";
    var mailpoet_static_segments_list = ";
        // line 10
        yield json_encode(($context["static_segments_list"] ?? null));
        yield ";
    var wordpress_editable_roles_list = ";
        // line 11
        yield json_encode(($context["wordpress_editable_roles_list"] ?? null));
        yield ";
    var mailpoet_newsletters_list = ";
        // line 12
        yield json_encode(($context["newsletters_list"] ?? null));
        yield ";
    var mailpoet_product_attributes = ";
        // line 13
        yield json_encode(($context["product_attributes"] ?? null));
        yield ";
    var mailpoet_local_product_attributes = ";
        // line 14
        yield json_encode(($context["local_product_attributes"] ?? null));
        yield ";
    var mailpoet_product_categories = ";
        // line 15
        yield json_encode(($context["product_categories"] ?? null));
        yield ";
    var mailpoet_product_tags = ";
        // line 16
        yield json_encode(($context["product_tags"] ?? null));
        yield ";
    var mailpoet_products = ";
        // line 17
        yield json_encode(($context["products"] ?? null));
        yield ";
    var mailpoet_membership_plans = ";
        // line 18
        yield json_encode(($context["membership_plans"] ?? null));
        yield ";
    var mailpoet_subscription_products = ";
        // line 19
        yield json_encode(($context["subscription_products"] ?? null));
        yield ";
    var mailpoet_can_use_woocommerce_memberships = ";
        // line 20
        yield json_encode(($context["can_use_woocommerce_memberships"] ?? null));
        yield ";
    var mailpoet_can_use_woocommerce_subscriptions = ";
        // line 21
        yield json_encode(($context["can_use_woocommerce_subscriptions"] ?? null));
        yield ";
    var mailpoet_woocommerce_currency_symbol = ";
        // line 22
        yield json_encode(($context["woocommerce_currency_symbol"] ?? null));
        yield ";
    var mailpoet_woocommerce_countries = ";
        // line 23
        yield json_encode(($context["woocommerce_countries"] ?? null));
        yield ";
    var mailpoet_woocommerce_payment_methods = ";
        // line 24
        yield json_encode(($context["woocommerce_payment_methods"] ?? null));
        yield ";
    var mailpoet_woocommerce_shipping_methods = ";
        // line 25
        yield json_encode(($context["woocommerce_shipping_methods"] ?? null));
        yield ";
    var mailpoet_signup_forms = ";
        // line 26
        yield json_encode(($context["signup_forms"] ?? null));
        yield ";
    var mailpoet_automations = ";
        // line 27
        yield json_encode(($context["automations"] ?? null));
        yield ";
  </script>

  ";
        // line 30
        yield from         $this->loadTemplate("segments/translations.html", "segments/dynamic.html", 30)->unwrap()->yield($context);
        return; yield '';
    }

    // line 34
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 35
        yield "  ";
        yield do_action("mailpoet_segments_translations_after");
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "segments/dynamic.html";
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
        return array (  152 => 35,  148 => 34,  143 => 30,  137 => 27,  133 => 26,  129 => 25,  125 => 24,  121 => 23,  117 => 22,  113 => 21,  109 => 20,  105 => 19,  101 => 18,  97 => 17,  93 => 16,  89 => 15,  85 => 14,  81 => 13,  77 => 12,  73 => 11,  69 => 10,  65 => 9,  61 => 8,  57 => 7,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "segments/dynamic.html", "/home/circleci/mailpoet/mailpoet/views/segments/dynamic.html");
    }
}
