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

/* newsletter/templates/blocks/woocommerceContent/widget.hbs */
class __TwigTemplate_77d59f140a098af6dd45178c94dd951cfc4ba7d8ad38d8d7d28a8de13fc328a4 extends Template
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
        yield "<div class=\"mailpoet_widget_icon\">
";
        // line 2
        yield MailPoetVendor\Twig\Extension\CoreExtension::source($this->env, "newsletter/templates/svg/block-icons/text.svg");
        yield "
</div>
<div class=\"mailpoet_widget_title\">";
        // line 4
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("WooCommerce Transactional Email Content", "Name of a widget in the email editor. This widget is used to display WooCommerce order information (list of bought items, shipping address, ...)");
        yield "</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/woocommerceContent/widget.hbs";
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
        return array (  46 => 4,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/woocommerceContent/widget.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/woocommerceContent/widget.hbs");
    }
}
