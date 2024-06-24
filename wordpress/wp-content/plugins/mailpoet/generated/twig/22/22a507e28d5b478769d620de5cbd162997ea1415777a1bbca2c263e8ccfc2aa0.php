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

/* woo_system_info.html */
class __TwigTemplate_4e0ede03c63347844b541c798d581116f05e6725f71ff37333dec6557061a07a extends Template
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
        yield "<table class=\"wc_status_table widefat\" cellspacing=\"0\">
  <thead>
  <tr>
    <th colspan=\"3\" data-export-label=\"MailPoet\"><h2
    >";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet");
        yield "
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 6
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("This section shows details of MailPoet"), "html_attr");
        yield "\"></span>
    </h2></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td data-export-label=\"Sending Method\">";
        // line 12
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Sending Method:");
        yield "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 14
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("What method is used to send out newsletters?"), "html_attr");
        yield "\"></span>
    </td>
    <td>";
        // line 16
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["system_info"] ?? null), "sending_method", [], "any", false, false, false, 16), "html", null, true);
        yield "</td>
  </tr>
  <tr>
    <td data-export-label=\"Send all site's emails with\">";
        // line 19
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Send all site's emails with:");
        yield "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 21
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("With which method are transactional emails sent?"), "html_attr");
        yield "\"></span>
    </td>
    <td>";
        // line 23
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["system_info"] ?? null), "transactional_emails", [], "any", false, false, false, 23), "html", null, true);
        yield "</td>
  </tr>
  <tr>
    <td data-export-label=\"Task Scheduler method\">";
        // line 26
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Task Scheduler method:");
        yield "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 28
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("What method controls the cron job?"), "html_attr");
        yield "\"></span>
    </td>
    <td>";
        // line 30
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["system_info"] ?? null), "task_scheduler_method", [], "any", false, false, false, 30), "html", null, true);
        yield "</td>
  </tr>
  <tr>
    <td data-export-label=\"Cron ping URL\">";
        // line 33
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Cron ping URL:");
        yield "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 35
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Which URL needs to be pinged to get the cron started?"), "html_attr");
        yield "\"></span>
    </td>
    <td><a href=\"";
        // line 37
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["system_info"] ?? null), "cron_ping_url", [], "any", false, false, false, 37), "html_attr");
        yield "\">";
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["system_info"] ?? null), "cron_ping_url", [], "any", false, false, false, 37), "html", null, true);
        yield "</a></td>
  </tr>
  </tbody>
</table>

";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "woo_system_info.html";
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
        return array (  115 => 37,  110 => 35,  105 => 33,  99 => 30,  94 => 28,  89 => 26,  83 => 23,  78 => 21,  73 => 19,  67 => 16,  62 => 14,  57 => 12,  48 => 6,  44 => 5,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "woo_system_info.html", "/home/circleci/mailpoet/mailpoet/views/woo_system_info.html");
    }
}
