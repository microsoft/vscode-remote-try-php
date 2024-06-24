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

/* newsletter/templates/blocks/coupon/block.hbs */
class __TwigTemplate_57bfdd4fb362dd94264b1d68ab1e6f4bdf9b65226909b1d15784e661ea7f7234 extends Template
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
        yield "<div class=\"mailpoet_tools\"></div>
<div class=\"mailpoet_content\" data-automation-id=\"coupon_block\">
    <div class=\"mailpoet_editor_coupon\" style=\"{{#ifCond model.styles.block.textAlign '==' 'left'}}margin: 0 auto 0 0; {{/ifCond}}{{#ifCond model.styles.block.textAlign '==' 'center'}}margin: auto; {{/ifCond}}{{#ifCond model.styles.block.textAlign '==' 'right'}}margin: 0 0 0 auto; {{/ifCond}}line-height: {{ model.styles.block.lineHeight }}; width: {{ model.styles.block.width }}; background-color: {{ model.styles.block.backgroundColor }}; color: {{ model.styles.block.fontColor }}; font-family: {{fontWithFallback model.styles.block.fontFamily }}; font-size: {{ model.styles.block.fontSize }}; font-weight: {{ model.styles.block.fontWeight }}; border: {{ model.styles.block.borderWidth }} {{ model.styles.block.borderStyle }} {{ model.styles.block.borderColor }}; border-radius: {{ model.styles.block.borderRadius }};\">{{ model.code }}</div>
    <div class=\"mailpoet_editor_coupon_overlay\" style=\"{{#ifCond model.source '==' 'allCoupons'}}visibility: hidden;{{/ifCond}}\">
        ";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("The coupon code will be auto-generated when this campaign is activated.");
        yield "
        {{#ifCond model.isStandardEmail '==' true}}
          ";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("All subscribers of this campaign will receive the same coupon code.");
        yield "
        {{else}}
          ";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Each subscriber of this campaign will receive a new coupon code.");
        yield "
        {{/ifCond}}
    </div>
</div>
<div class=\"mailpoet_block_highlight\"></div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/coupon/block.hbs";
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
        return array (  54 => 9,  49 => 7,  44 => 5,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/coupon/block.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/coupon/block.hbs");
    }
}
