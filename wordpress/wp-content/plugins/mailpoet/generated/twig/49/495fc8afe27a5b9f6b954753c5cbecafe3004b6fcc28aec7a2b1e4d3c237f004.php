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

/* newsletter/templates/blocks/social/settings.hbs */
class __TwigTemplate_057d90fe2d8b70e6391f41de22f26510bd3c9fd07231d908f25ca03dea163a24 extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Select icons");
        echo "</h3>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alignment");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_social_block_alignment\" value=\"left\" {{#ifCond model.styles.block.textAlign '===' 'left'}}CHECKED{{/ifCond}}/>
            ";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Left", "Visual alignment settings");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_social_block_alignment\" value=\"center\" {{#ifCond model.styles.block.textAlign '===' 'center'}}CHECKED{{/ifCond}}/>
            ";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Center", "Visual alignment settings");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_social_block_alignment\" value=\"right\" {{#ifCond model.styles.block.textAlign '===' 'right'}}CHECKED{{/ifCond}}/>
            ";
        // line 19
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Right", "Visual alignment settings");
        echo "
        </label>
    </div>
</div>

<hr>

<div id=\"mailpoet_social_icons_selection\" class=\"mailpoet_form_field\"></div>
<h3>";
        // line 27
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Styles");
        echo "</h3>
<div id=\"mailpoet_social_icons_styles\"></div>
<div class=\"mailpoet_form_field\">
  <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" data-automation-id=\"social_done_button\" value=\"";
        // line 30
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/social/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 30,  79 => 27,  68 => 19,  59 => 13,  50 => 7,  43 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/social/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/social/settings.hbs");
    }
}
