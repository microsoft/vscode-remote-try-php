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

/* woocommerce/settings_button.html */
class __TwigTemplate_c9698819ba62cbd2a50d0d8966b632b15dab68db5924228afc7775b0b336ff53 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<script type=\"text/javascript\">
  jQuery(function(\$){
    \$('#mailpoet_woocommerce_customize_button')
      .insertAfter(\$('#email_notification_settings-description'))
      .show();
  });
</script>

<p id=\"mailpoet_woocommerce_customize_button\" style=\"display: none;\">
  <a class=\"button button-primary\"
    href=\"?page=mailpoet-newsletter-editor&id=";
        // line 11
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["woocommerce_template_id"] ?? null), "html", null, true);
        yield "\"
    data-automation-id=\"mailpoet_woocommerce_customize_button\"
  >
    ";
        // line 14
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Customize with MailPoet", "Button in WooCommerce settings page");
        yield "
  </a>
</p>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "woocommerce/settings_button.html";
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
        return array (  56 => 14,  50 => 11,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "woocommerce/settings_button.html", "/home/circleci/mailpoet/mailpoet/views/woocommerce/settings_button.html");
    }
}
