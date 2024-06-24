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
        yield "{{#if isWoocommerceTransactional}}
  <h1>";
        // line 2
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Edit template for WooCommerce emails", "Name of user interface used to customize email template used for eCommerce related emails (for example order confirmation email)");
        yield "</h1>
  <p class=\"mailpoet_heading_wc_template_description\">";
        // line 3
        yield MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translate("This email template will be used for all your WooCommerce emails. Meaning that any content added to this template will be visible in all WooCommerce emails. If you want to change email-specific content including titles, [link]visit WooCommerce settings[/link]."), "?page=wc-settings&tab=email", ["target" => "_blank"]);
        yield "</p>
  <div class=\"mailpoet_form_field mailpoet_heading_form_field\">
    <label for=\"mailpoet_heading_email_type\">";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Load dummy data for email:", "Label of a dropdown used to switch between email type: order processing, order completed, ...");
        yield "</label>
    <select id=\"mailpoet_heading_email_type\">
      <option value=\"new_account\">";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("New account", "woocommerce");
        yield "</option>
      <option value=\"processing_order\">";
        // line 8
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Processing order", "woocommerce");
        yield "</option>
      <option value=\"completed_order\" selected=\"selected\">";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Completed order", "woocommerce");
        yield "</option>
      <option value=\"customer_note\">";
        // line 10
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Customer note", "woocommerce");
        yield "</option>
    </select>
  </div>
{{else if isAutomationEmail}}

{{else if isConfirmationEmailTemplate}}
  <h3>";
        // line 16
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Edit template for Confirmation emails", "Name of user interface used to customize email template used for confirmation emails");
        yield "</h3>
  <div class=\"mailpoet_form_field mailpoet_heading_form_field\">
    <input
      type=\"text\"
      class=\"mailpoet_input mailpoet_input_title\"
      data-automation-id=\"newsletter_title\"
      value=\"{{ model.subject }}\"
      placeholder=\"";
        // line 23
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Click here to change the subject!");
        yield "\"
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Click here to change the subject!");
        yield "\"
  />
  <span id=\"tooltip-designer-subject-line\" class=\"tooltip-help-designer-subject-line\"></span>
</div>
<div class=\"mailpoet_form_field mailpoet_heading_form_field\">
  <input type=\"text\"
    class=\"mailpoet_input mailpoet_input_preheader\"
    value=\"{{ model.preheader }}\"
    placeholder=\"";
        // line 42
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preview text (usually displayed underneath the subject line in the inbox)");
        yield "\"
    maxlength=\"250\"
  />
  <span id=\"tooltip-designer-preheader\" class=\"tooltip-help-designer-preheader\"></span>
</div>
{{/if}}
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/components/heading.hbs";
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
        return array (  111 => 42,  100 => 34,  86 => 23,  76 => 16,  67 => 10,  63 => 9,  59 => 8,  55 => 7,  50 => 5,  45 => 3,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/heading.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/heading.hbs");
    }
}
