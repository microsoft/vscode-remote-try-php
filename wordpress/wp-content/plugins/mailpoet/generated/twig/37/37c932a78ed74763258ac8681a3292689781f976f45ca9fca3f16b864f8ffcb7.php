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
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        return; yield '';
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        yield "<form class=\"mailpoet-manage-subscription\" method=\"post\" action=\"";
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["actionUrl"] ?? null));
        yield "\" novalidate>
  <input type=\"hidden\" name=\"action\" value=\"mailpoet_subscription_update\" />
  <input type=\"hidden\" name=\"data[segments]\" value=\"\" />
  <input type=\"hidden\" name=\"mailpoet_redirect\" value=\"";
        // line 5
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["redirectUrl"] ?? null));
        yield "\"/>
  <input type=\"hidden\" name=\"data[email]\" value=\"";
        // line 6
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["email"] ?? null));
        yield "\" />
  <input type=\"hidden\" name=\"token\" value=\"";
        // line 7
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["token"] ?? null), "html", null, true);
        yield "\" />
  <p class=\"mailpoet_paragraph\">
    <label> ";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Email", "mailpoet");
        yield "*<br /><strong>";
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["email"] ?? null));
        yield "</strong></label>
    <br />
    <span class=\"mailpoet-change-email-info\">
      ";
        // line 12
        $context["allowedHtml"] = ["a" => ["href" => [], "target" => []]];
        // line 13
        yield "      ";
        yield $this->extensions['MailPoet\Twig\Filters']->wpKses(($context["editEmailInfo"] ?? null), ($context["allowedHtml"] ?? null));
        yield "
    </span>
  </p>
  ";
        // line 16
        yield ($context["formHtml"] ?? null);
        yield "
  ";
        // line 17
        if ((($context["formState"] ?? null) == "success")) {
            // line 18
            yield "  <p class=\"mailpoet-submit-success\">
    ";
            // line 19
            yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Your preferences have been saved.", "success message after saving subscription settings");
            yield "
  </p>
  ";
        }
        // line 22
        yield "</form>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "subscription/manage_subscription.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  99 => 22,  93 => 19,  90 => 18,  88 => 17,  84 => 16,  77 => 13,  75 => 12,  67 => 9,  62 => 7,  58 => 6,  54 => 5,  47 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscription/manage_subscription.html", "/home/circleci/mailpoet/mailpoet/views/subscription/manage_subscription.html");
    }
}
