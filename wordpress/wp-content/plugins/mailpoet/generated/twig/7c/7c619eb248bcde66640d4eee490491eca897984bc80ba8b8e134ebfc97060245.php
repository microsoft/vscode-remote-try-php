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

/* newsletter/templates/components/save.hbs */
class __TwigTemplate_fabd1248ff21b91584686f92b4c5275cf43b599b650065f182e5564d092d10e5 extends Template
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
        yield "<div class=\"mailpoet_save_wrapper {{ wrapperClass }}\">
{{#if isWoocommerceTransactional}}
    <div class=\"mailpoet_save_button_group\">
        <input type=\"button\" name=\"save\" value=\"";
        // line 4
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        yield "\" class=\"button button-primary mailpoet_save_button\" />
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_editor_messages\">
        <div class=\"mailpoet_save_error\"></div>
        <div class=\"mailpoet_editor_last_saved\">
            &nbsp;
            <span class=\"mailpoet_autosaved_message mailpoet_hidden\">";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Autosaved");
        yield "</span>
            <span class=\"mailpoet_autosaved_at mailpoet_hidden\"></span>
        </div>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_save_woocommerce_customizer_disabled{{#if woocommerceCustomizerEnabled}} mailpoet_hidden{{/if}}\">
      <div class=\"mailpoet_save_woocommerce_error\">";
        // line 17
        yield $this->extensions['MailPoet\Twig\I18n']->translate("The usage of this email template for your WooCommerce emails is not yet activated.");
        yield "</div>
      <input type=\"button\" name=\"activate_wc_customizer\" value=\"";
        // line 18
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Activate now");
        yield "\" class=\"button button-primary mailpoet_save_activate_wc_customizer_button\" style=\"margin-top: 17px\">
    </div>
{{else if isAutomationEmail}}
    <input type=\"button\" name=\"preview\" class=\"button mailpoet_show_preview mailpoet_hidden\" value=\"";
        // line 21
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preview");
        yield "\" />
    <input type=\"button\" name=\"save\" value=\"";
        // line 22
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        yield "\" class=\"button button-primary mailpoet_save_go_to_automation mailpoet_hidden\" />
{{else if isConfirmationEmailTemplate}}
  <div class=\"mailpoet_editor_confirmation_email_section\">
    <div class=\"mailpoet_save_button_group\">
      <input type=\"button\" name=\"preview\" value=\"";
        // line 26
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preview");
        yield "\" class=\"button mailpoet_show_preview\" />
      <input type=\"button\" name=\"save\" value=\"";
        // line 27
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        yield "\" class=\"button button-primary mailpoet_save_button\" />
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_editor_messages_confirmation_email\">
      <div class=\"mailpoet_save_error\"></div>
      <div class=\"mailpoet_editor_last_saved\">
        &nbsp;
        <span class=\"mailpoet_autosaved_message mailpoet_hidden\">";
        // line 34
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Autosaved");
        yield "</span>
        <span class=\"mailpoet_autosaved_at\"></span>
      </div>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_save_confirmation_email_disabled{{#if confirmationEmailCustomizerEnabled}} mailpoet_hidden{{/if}}\">
      <div class=\"mailpoet_save_woocommerce_error\">";
        // line 40
        yield $this->extensions['MailPoet\Twig\I18n']->translate("The usage of this email template for your Confirmation emails is not yet activated.");
        yield "</div>
      <input type=\"button\" name=\"activate_confirmation_email_customizer\" value=\"";
        // line 41
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Activate now");
        yield "\" class=\"button button-primary mailpoet_save_activate_confirmation_email_customizer_button\" style=\"margin-top: 17px\">
    </div>
  </div>
{{else}}
    <input type=\"button\" name=\"preview\" class=\"button mailpoet_show_preview\" value=\"";
        // line 45
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preview");
        yield "\" />
    <input type=\"button\" name=\"next\" value=\"";
        // line 46
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Next");
        yield "\" class=\"button button-primary mailpoet_save_next\" />
    <div class=\"mailpoet_button_group mailpoet_save_button_group\">
        <input type=\"button\" name=\"save\" value=\"";
        // line 48
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        yield "\" class=\"button button-primary mailpoet_save_button\" /><button class=\"button button-primary mailpoet_save_show_options\" data-automation-id=\"newsletter_save_options_toggle\" ><span class=\"dashicons mailpoet_save_show_options_icon\"></span></button>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_editor_messages\">
        <div class=\"mailpoet_save_error\"></div>
        <div class=\"mailpoet_editor_last_saved\">
            &nbsp;
            <span class=\"mailpoet_autosaved_message mailpoet_hidden\">";
        // line 55
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Autosaved");
        yield "</span>
            <span class=\"mailpoet_autosaved_at mailpoet_hidden\"></span>
        </div>
    </div>
    <ul class=\"mailpoet_save_options mailpoet_hidden\">
        <li class=\"mailpoet_save_option\"><a href=\"javascript:;\" class=\"mailpoet_save_template\" data-automation-id=\"newsletter_save_as_template_option\">";
        // line 60
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save as new template");
        yield "</a></li>
        <li class=\"mailpoet_save_option\"><a href=\"javascript:;\" class=\"mailpoet_save_export\">";
        // line 61
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Export as template");
        yield "</a></li>
    </ul>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_save_as_template_container mailpoet_hidden\">
        <p><b class=\"mailpoet_save_as_template_title\">";
        // line 65
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save as new template");
        yield "</b></p>
        <p><input type=\"text\" name=\"template_name\" value=\"\" placeholder=\"";
        // line 66
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Insert template name");
        yield "\" class=\"mailpoet_input mailpoet_save_as_template_name\" /></p>
        <p><input type=\"button\" name=\"save_as_template\" value=\"";
        // line 67
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Save as new template");
        yield "\" class=\"button button-primary mailpoet_button_full mailpoet_save_as_template\" data-automation-id=\"newsletter_save_as_template_button\" /></p>
    </div>
    <div class=\"mailpoet_export_template_container mailpoet_hidden\">
        <p><b class=\"mailpoet_export_template_title\">";
        // line 70
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Export template");
        yield "</b></p>
        <p><input type=\"text\" name=\"export_template_name\" value=\"\" placeholder=\"";
        // line 71
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Template name");
        yield "\" class=\"mailpoet_input mailpoet_export_template_name\" /></p>
        <p><input type=\"button\" name=\"export_template\" value=\"";
        // line 72
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Export template");
        yield "\" class=\"button button-primary mailpoet_button_full mailpoet_export_template\" /></p>
    </div>
{{/if}}
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/components/save.hbs";
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
        return array (  177 => 72,  173 => 71,  169 => 70,  163 => 67,  159 => 66,  155 => 65,  148 => 61,  144 => 60,  136 => 55,  126 => 48,  121 => 46,  117 => 45,  110 => 41,  106 => 40,  97 => 34,  87 => 27,  83 => 26,  76 => 22,  72 => 21,  66 => 18,  62 => 17,  53 => 11,  43 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/save.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/save.hbs");
    }
}
