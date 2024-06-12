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

/* newsletter/templates/blocks/posts/settings.hbs */
class __TwigTemplate_4805cb8f70abc450fe61e6eb69c62c747430babfe7cc19b22bb4602f5bbbf75f extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Post selection");
        echo "</h3>
<div class=\"mailpoet_settings_posts_selection\"></div>
<div class=\"mailpoet_settings_posts_display_options mailpoet_closed\"></div>
<div class=\"mailpoet_settings_posts_controls\">
  <div class=\"mailpoet_form_field\">
      <a href=\"javascript:;\" class=\"mailpoet_settings_posts_show_post_selection mailpoet_hidden\">";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Back to selection");
        echo "</a>
      <a href=\"javascript:;\" class=\"mailpoet_settings_posts_show_display_options\">";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        echo "</a>
  </div>
  <input type=\"button\" class=\"button button-primary mailpoet_settings_posts_insert_selected\" value=\"";
        // line 9
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Insert selected"), "html_attr");
        echo "\" />
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/posts/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 9,  50 => 7,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/posts/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/posts/settings.hbs");
    }
}
