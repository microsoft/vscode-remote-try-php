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

/* subscription/manage_subscription.html */
class __TwigTemplate_8af1b0123e44e0dbc01ef4376ab3d01514a0306b6a607ad264d10a53f473fb22 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $this->displayBlock('content', $context, $blocks);
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        echo "<form class=\"mailpoet-manage-subscription\" method=\"post\" action=\"";
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["actionUrl"] ?? null));
        echo "\" novalidate>
  <input type=\"hidden\" name=\"action\" value=\"mailpoet_subscription_update\" />
  <input type=\"hidden\" name=\"data[segments]\" value=\"\" />
  <input type=\"hidden\" name=\"mailpoet_redirect\" value=\"";
        // line 5
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["redirectUrl"] ?? null));
        echo "\"/>
  <input type=\"hidden\" name=\"data[email]\" value=\"";
        // line 6
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["email"] ?? null));
        echo "\" />
  <input type=\"hidden\" name=\"token\" value=\"";
        // line 7
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["token"] ?? null), "html", null, true);
        echo "\" />
  <p class=\"mailpoet_paragraph\">
    <label> ";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Email", "mailpoet");
        echo "*<br /><strong>";
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["email"] ?? null));
        echo "</strong></label>
    <br />
    <span class=\"mailpoet-change-email-info\">
      ";
        // line 12
        $context["allowedHtml"] = ["a" => ["href" => [], "target" => []]];
        // line 13
        echo "      ";
        echo $this->extensions['MailPoet\Twig\Filters']->wpKses(($context["editEmailInfo"] ?? null), ($context["allowedHtml"] ?? null));
        echo "
    </span>
  </p>
  ";
        // line 16
        echo ($context["formHtml"] ?? null);
        echo "
  ";
        // line 17
        if ((($context["formState"] ?? null) == "success")) {
            // line 18
            echo "  <p class=\"mailpoet-submit-success\">
    ";
            // line 19
            echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Your preferences have been saved.", "success message after saving subscription settings");
            echo "
  </p>
  ";
        }
        // line 22
        echo "</form>
";
    }

    public function getTemplateName()
    {
        return "subscription/manage_subscription.html";
    }

    public function getDebugInfo()
    {
        return array (  97 => 22,  91 => 19,  88 => 18,  86 => 17,  82 => 16,  75 => 13,  73 => 12,  65 => 9,  60 => 7,  56 => 6,  52 => 5,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscription/manage_subscription.html", "/home/circleci/mailpoet/mailpoet/views/subscription/manage_subscription.html");
    }
}
