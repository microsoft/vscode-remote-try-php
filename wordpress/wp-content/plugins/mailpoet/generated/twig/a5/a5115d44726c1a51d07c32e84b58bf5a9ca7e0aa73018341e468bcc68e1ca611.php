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

/* newsletter/templates/blocks/spacer/block.hbs */
class __TwigTemplate_fe82214a672852b4438d3bc95af40d7f6b223a020fa4c9d667c486d986e83642 extends Template
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
        echo "<div class=\"mailpoet_tools\"></div>
<div class=\"mailpoet_content\">
    <div class=\"mailpoet_spacer\" data-automation-id=\"spacer\" style=\"height: {{ model.styles.block.height }}; background-color: {{ model.styles.block.backgroundColor }};\">
        <div class=\"mailpoet_resize_handle_container\">
            <div class=\"mailpoet_resize_handle\" data-automation-id=\"spacer_resize_handle\">
                <span class=\"mailpoet_resize_handle_text\">{{ model.styles.block.height }}</span>
                <span class=\"mailpoet_resize_handle_icon\">";
        // line 7
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-icons/spacer.svg");
        echo "</span>
            </div>
        </div>
    </div>
</div>
<div class=\"mailpoet_block_highlight\"></div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/spacer/block.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/spacer/block.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/spacer/block.hbs");
    }
}
