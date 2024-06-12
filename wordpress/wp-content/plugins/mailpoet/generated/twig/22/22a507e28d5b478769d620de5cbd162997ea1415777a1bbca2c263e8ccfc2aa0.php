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
        echo "<table class=\"wc_status_table widefat\" cellspacing=\"0\">
  <thead>
  <tr>
    <th colspan=\"3\" data-export-label=\"MailPoet\"><h2
    >";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet");
        echo "
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 6
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("This section shows details of MailPoet"), "html_attr");
        echo "\"></span>
    </h2></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td data-export-label=\"Sending Method\">";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Sending Method:");
        echo "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 14
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("What method is used to send out newsletters?"), "html_attr");
        echo "\"></span>
    </td>
    <td>";
        // line 16
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["system_info"] ?? null), "sending_method", [], "any", false, false, false, 16), "html", null, true);
        echo "</td>
  </tr>
  <tr>
    <td data-export-label=\"Send all site's emails with\">";
        // line 19
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Send all site's emails with:");
        echo "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 21
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("With which method are transactional emails sent?"), "html_attr");
        echo "\"></span>
    </td>
    <td>";
        // line 23
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["system_info"] ?? null), "transactional_emails", [], "any", false, false, false, 23), "html", null, true);
        echo "</td>
  </tr>
  <tr>
    <td data-export-label=\"Task Scheduler method\">";
        // line 26
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Task Scheduler method:");
        echo "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 28
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("What method controls the cron job?"), "html_attr");
        echo "\"></span>
    </td>
    <td>";
        // line 30
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["system_info"] ?? null), "task_scheduler_method", [], "any", false, false, false, 30), "html", null, true);
        echo "</td>
  </tr>
  <tr>
    <td data-export-label=\"Cron ping URL\">";
        // line 33
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Cron ping URL:");
        echo "</td>
    <td class=\"help\">
      <span class=\"woocommerce-help-tip\" data-tip=\"";
        // line 35
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Which URL needs to be pinged to get the cron started?"), "html_attr");
        echo "\"></span>
    </td>
    <td><a href=\"";
        // line 37
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["system_info"] ?? null), "cron_ping_url", [], "any", false, false, false, 37), "html_attr");
        echo "\">";
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["system_info"] ?? null), "cron_ping_url", [], "any", false, false, false, 37), "html", null, true);
        echo "</a></td>
  </tr>
  </tbody>
</table>

";
    }

    public function getTemplateName()
    {
        return "woo_system_info.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  114 => 37,  109 => 35,  104 => 33,  98 => 30,  93 => 28,  88 => 26,  82 => 23,  77 => 21,  72 => 19,  66 => 16,  61 => 14,  56 => 12,  47 => 6,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "woo_system_info.html", "/home/circleci/mailpoet/mailpoet/views/woo_system_info.html");
    }
}
