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

/* emails/statsNotificationAutomatedEmails.txt */
class __TwigTemplate_cbe6b7731003366ad97ca691fe5f4c1bccaf16b35087954ed358e9e446555e6f extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "emails/statsNotificationLayout.txt";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("emails/statsNotificationLayout.txt", "emails/statsNotificationAutomatedEmails.txt", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "
";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Your monthly stats are in!");
        yield "

";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["newsletters"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["newsletter"]) {
            // line 8
            yield "------------------------------------------
  ";
            // line 9
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "subject", [], "any", false, false, false, 9), "html", null, true);
            yield "
  ";
            // line 10
            yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "clicked", [], "any", false, false, false, 10));
            yield "% ";
            yield $this->extensions['MailPoet\Twig\I18n']->translate("clicked");
            yield " (";
            yield $this->extensions['MailPoet\Twig\Functions']->clickedStatsText(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "clicked", [], "any", false, false, false, 10));
            yield ")
  ";
            // line 11
            yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "opened", [], "any", false, false, false, 11));
            yield "% ";
            yield $this->extensions['MailPoet\Twig\I18n']->translate("opened");
            yield "
  ";
            // line 12
            yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "machineOpened", [], "any", false, false, false, 12));
            yield "% ";
            yield $this->extensions['MailPoet\Twig\I18n']->translate("machine-opened");
            yield "
  ";
            // line 13
            yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "unsubscribed", [], "any", false, false, false, 13));
            yield "% ";
            yield $this->extensions['MailPoet\Twig\I18n']->translate("unsubscribed");
            yield "
  ";
            // line 14
            yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "bounced", [], "any", false, false, false, 14));
            yield "% ";
            yield $this->extensions['MailPoet\Twig\I18n']->translate("bounced");
            yield "
  ";
            // line 15
            yield $this->extensions['MailPoet\Twig\I18n']->translate("View all stats");
            yield "
    ";
            // line 16
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["newsletter"], "linkStats", [], "any", false, false, false, 16), "html", null, true);
            yield "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['newsletter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        yield "------------------------------------------
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "emails/statsNotificationAutomatedEmails.txt";
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
        return array (  114 => 18,  106 => 16,  102 => 15,  96 => 14,  90 => 13,  84 => 12,  78 => 11,  70 => 10,  66 => 9,  63 => 8,  59 => 7,  54 => 5,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/statsNotificationAutomatedEmails.txt", "/home/circleci/mailpoet/mailpoet/views/emails/statsNotificationAutomatedEmails.txt");
    }
}
