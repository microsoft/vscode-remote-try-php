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

/* newsletter/templates/blocks/abandonedCartContent/settings.hbs */
class __TwigTemplate_fa065697f25afa3f448331ec5feeaeff06376f390132a244e352df6f05279716 extends Template
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
        echo "<h3>";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        echo "</h3>
<div class=\"mailpoet_settings_abandoned_cart_content_display_options\"></div>
<div class=\"mailpoet_settings_abandoned_cart_content_controls\">
  <div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 5
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/abandonedCartContent/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/abandonedCartContent/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/abandonedCartContent/settings.hbs");
    }
}
