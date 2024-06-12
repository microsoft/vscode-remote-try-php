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

/* newsletter/templates/blocks/spacer/settings.hbs */
class __TwigTemplate_a9aa51a3e17b8a0840edf9e23381b57e79670d98687de6c52fc956f148b59928 extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Spacer");
        echo "</h3>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_spacer_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Background");
        echo "</div>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" data-automation-id=\"spacer_done_button\" value=\"";
        // line 10
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/spacer/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 10,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/spacer/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/spacer/settings.hbs");
    }
}
