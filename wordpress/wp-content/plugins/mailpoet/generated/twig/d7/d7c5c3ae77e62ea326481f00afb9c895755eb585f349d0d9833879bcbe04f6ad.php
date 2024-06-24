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

/* newsletter/templates/blocks/unknownBlockFallback/block.hbs */
class __TwigTemplate_d6341c5162908a885b57705e06e73154b2d52297c4365c96e9d029d349446804 extends Template
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
<div class=\"mailpoet_content mailpoet_unknown_block_fallback\">
  <h2 class=\"mailpoet_unknown_block_fallback_heading\">";
        // line 3
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Unsupported block type");
        yield "</h2>
  <p>";
        // line 4
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::sprintf($this->extensions['MailPoet\Twig\I18n']->translate("\"%s\" block type is not supported and will not be included in the email. You can either delete this block, or enable a plugin that adds support for this block."), "{{blockType}}"), "html", null, true);
        yield "</p>
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
        return "newsletter/templates/blocks/unknownBlockFallback/block.hbs";
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
        return array (  46 => 4,  42 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/unknownBlockFallback/block.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/unknownBlockFallback/block.hbs");
    }
}
