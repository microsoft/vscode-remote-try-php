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

/* newsletter/templates/components/heading.hbs */
class __TwigTemplate_09c5e4020e8979b31e252e5ac1bc9836a0273b471026683178b9a266d7ab5372 extends Template
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
        echo "{{#if isWoocommerceTransactional}}
  <h1>";
        // line 2
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Edit template for WooCommerce emails", "Name of user interface used to customize email template used for eCommerce related emails (for example order confirmation email)");
        echo "</h1>
  <p class=\"mailpoet_heading_wc_template_description\">";
        // line 3
        echo MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translate("This email template will be used for all your WooCommerce emails. Meaning that any content added to this template will be visible in all WooCommerce emails. If you want to change email-specific content including titles, [link]visit WooCommerce settings[/link]."), "?page=wc-settings&tab=email", ["target" => "_blank"]);
        echo "</p>
  <div class=\"mailpoet_form_field mailpoet_heading_form_field\">
    <label for=\"mailpoet_heading_email_type\">";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Load dummy data for email:", "Label of a dropdown used to switch between email type: order processing, order completed, ...");
        echo "</label>
    <select id=\"mailpoet_heading_email_type\">
      <option value=\"new_account\">";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("New account", "woocommerce");
        echo "</option>
      <option value=\"processing_order\">";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Processing order", "woocommerce");
        echo "</option>
      <option value=\"completed_order\" selected=\"selected\">";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Completed order", "woocommerce");
        echo "</option>
      <option value=\"customer_note\">";
        // line 10
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Customer note", "woocommerce");
        echo "</option>
    </select>
  </div>
{{else if isAutomationEmail}}

{{else if isConfirmationEmailTemplate}}
  <h3>";
        // line 16
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Edit template for Confirmation emails", "Name of user interface used to customize email template used for confirmation emails");
        echo "</h3>
  <div class=\"mailpoet_form_field mailpoet_heading_form_field\">
    <input
      type=\"text\"
      class=\"mailpoet_input mailpoet_input_title\"
      data-automation-id=\"newsletter_title\"
      value=\"{{ model.subject }}\"
      placeholder=\"";
        // line 23
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Click here to change the subject!");
        echo "\"
    />
    <span id=\"tooltip-designer-subject-line\" class=\"tooltip-help-designer-subject-line\"></span>
  </div>
{{else}}
<div class=\"mailpoet_form_field mailpoet_heading_form_field\">
  <input
    type=\"text\"
    class=\"mailpoet_input mailpoet_input_title\"
    data-automation-id=\"newsletter_title\"
    value=\"{{ model.subject }}\"
    placeholder=\"";
        // line 34
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Click here to change the subject!");
        echo "\"
  />
  <span id=\"tooltip-designer-subject-line\" class=\"tooltip-help-designer-subject-line\"></span>
</div>
<div class=\"mailpoet_form_field mailpoet_heading_form_field\">
  <input type=\"text\"
    class=\"mailpoet_input mailpoet_input_preheader\"
    value=\"{{ model.preheader }}\"
    placeholder=\"";
        // line 42
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preview text (usually displayed underneath the subject line in the inbox)");
        echo "\"
    maxlength=\"250\"
  />
  <span id=\"tooltip-designer-preheader\" class=\"tooltip-help-designer-preheader\"></span>
</div>
{{/if}}
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/components/heading.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  110 => 42,  99 => 34,  85 => 23,  75 => 16,  66 => 10,  62 => 9,  58 => 8,  54 => 7,  49 => 5,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/heading.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/heading.hbs");
    }
}
