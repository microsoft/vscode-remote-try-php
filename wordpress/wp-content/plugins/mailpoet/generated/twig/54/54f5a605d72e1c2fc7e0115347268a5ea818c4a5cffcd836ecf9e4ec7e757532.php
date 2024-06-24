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

/* newsletter/templates/components/newsletterPreview.hbs */
class __TwigTemplate_414e57f5d39be30b7212208368ba358fb4ae1c2f2d02bf5028ce033882d079e8 extends Template
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
        yield "<div class=\"mailpoet_browser_preview_link\">
  <a href=\"{{ previewUrl }}\" target=\"_blank\" rel=\"noopener noreferrer\">";
        // line 2
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Open in new tab", "Open email preview in new tab");
        yield "</a>
</div>
<div class=\"mailpoet_browser_preview_toggle\">
  <label>
    <input type=\"radio\" name=\"mailpoet_browser_preview_type\" class=\"mailpoet_browser_preview_type\" value=\"desktop\" {{#ifCond previewType '==' 'desktop'}}CHECKED{{/ifCond}} />";
        // line 6
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Desktop", "Desktop browser preview mode");
        yield "
  </label>
  <label>
    <input type=\"radio\" name=\"mailpoet_browser_preview_type\" class=\"mailpoet_browser_preview_type\" value=\"mobile\" {{#ifCond previewType '==' 'mobile'}}CHECKED{{/ifCond}} />";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Mobile", "Mobile browser preview mode");
        yield "
  </label>
  <label>
    <input data-automation-id=\"switch_send_to_email\" type=\"radio\" name=\"mailpoet_browser_preview_type\" class=\"mailpoet_browser_preview_type\" value=\"send_to_email\" {{#ifCond previewType '==' 'send_to_email'}}CHECKED{{/ifCond}} />";
        // line 12
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Send to email");
        yield "
  </label>
</div>
<div
  class=\"
    mailpoet_browser_preview_container
   {{#ifCond previewType '==' 'mobile'}}mailpoet_browser_preview_container_mobile{{/ifCond}}
   {{#ifCond previewType '==' 'desktop'}}mailpoet_browser_preview_container_desktop{{/ifCond}}
   {{#ifCond previewType '==' 'send_to_email'}}mailpoet_browser_preview_container_send_to_email{{/ifCond}}
  \"
>
  <div class=\"mailpoet_browser_preview_border\">
    <iframe id=\"mailpoet_browser_preview_iframe\" class=\"mailpoet_browser_preview_iframe\" src=\"{{ previewUrl }}\" width=\"{{ width }}\" height=\"{{ height }}\"></iframe>
  </div>

  <div class=\"mailpoet_preview_send_to_email\">
    <iframe name=\"mailpoet_save_preview_email_for_autocomplete\" style=\"display:none\" src=\"about:blank\"></iframe>
    <form target=\"mailpoet_save_preview_email_for_autocomplete\">
      <div class=\"mailpoet_form_field\">
        <label>
          ";
        // line 32
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Send preview to");
        yield "<br />
          <input id=\"mailpoet_preview_to_email\" class=\"mailpoet_input mailpoet_input_full\" type=\"text\" name=\"to_email\" value=\"{{ email }}\" autocomplete=\"email\" />
        </label>
      </div>

      <div class=\"mailpoet_form_field relative-holder\">
        <input
          type=\"submit\"
          id=\"mailpoet_send_preview\"
          class=\"button button-primary mailpoet_button_full\"
          value=\"
            {{#if sendingPreview}}";
        // line 43
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Sending…");
        yield "{{/if}}
            {{#unless sendingPreview}}";
        // line 44
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Send preview");
        yield "{{/unless}}
          \"
          {{#if sendingPreview}}disabled{{/if}}
        />
        <p>";
        // line 48
        yield $this->extensions['MailPoet\Twig\I18n']->translate("A MailPoet logo will appear in the footer of all emails sent with the free version of MailPoet.");
        yield "</p>
        <p class=\"{{#unless previewSendingSuccess}}mailpoet_hidden{{/unless}} mailpoet_success\">
          ";
        // line 50
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Your test email has been sent!");
        yield "
        </p>
        <p class=\"{{#unless previewSendingSuccess}}mailpoet_hidden{{/unless}}\">
          ";
        // line 53
        yield MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translate("Didn’t receive the test email? Read our [link]quick guide[/link] to sending issues."), "https://kb.mailpoet.com/article/146-my-newsletters-are-not-being-received", ["target" => "_blank", "rel" => "noopener noreferrer"]);
        yield "
        </p>
        <div class=\"{{#unless previewSendingError}}mailpoet_hidden{{/unless}} mailpoet_error\" id=\"mailpoet_preview_sending_error\"></div>
      </div>
    </form>

    {{#if mssKeyPendingApproval }}
      <div class=\"mailpoet_error pendindig_approval_error{{#if awaitingKeyCheck}} with-spinner{{/if}}\">
        <p>
          ";
        // line 62
        yield $this->extensions['MailPoet\Twig\Functions']->pendingApprovalMessage();
        yield "
        </p>
        {{#if mssKeyPendingApprovalRefreshMessage }}
        <p>
          ";
        // line 66
        yield MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translate("If you have already received approval email, click [link]here[/link] to update the status."), "#", ["id" => "refresh-mss-key-status"]);
        yield "
        </p>
        {{/if}}
      </div>
    {{/if}}
  </div>
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/components/newsletterPreview.hbs";
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
        return array (  138 => 66,  131 => 62,  119 => 53,  113 => 50,  108 => 48,  101 => 44,  97 => 43,  83 => 32,  60 => 12,  54 => 9,  48 => 6,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/newsletterPreview.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/newsletterPreview.hbs");
    }
}
