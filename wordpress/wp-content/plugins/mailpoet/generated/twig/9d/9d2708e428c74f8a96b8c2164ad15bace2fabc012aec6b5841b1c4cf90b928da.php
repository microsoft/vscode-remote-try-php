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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "
";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Your monthly stats are in!");
        echo "

";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = \MailPoetVendor\twig_ensure_traversable(($context["newsletters"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["newsletter"]) {
            // line 8
            echo "------------------------------------------
  ";
            // line 9
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "subject", [], "any", false, false, false, 9), "html", null, true);
            echo "
  ";
            // line 10
            echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(\MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "clicked", [], "any", false, false, false, 10));
            echo "% ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("clicked");
            echo " (";
            echo $this->extensions['MailPoet\Twig\Functions']->clickedStatsText(\MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "clicked", [], "any", false, false, false, 10));
            echo ")
  ";
            // line 11
            echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(\MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "opened", [], "any", false, false, false, 11));
            echo "% ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("opened");
            echo "
  ";
            // line 12
            echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(\MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "machineOpened", [], "any", false, false, false, 12));
            echo "% ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("machine-opened");
            echo "
  ";
            // line 13
            echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(\MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "unsubscribed", [], "any", false, false, false, 13));
            echo "% ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("unsubscribed");
            echo "
  ";
            // line 14
            echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(\MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "bounced", [], "any", false, false, false, 14));
            echo "% ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("bounced");
            echo "
  ";
            // line 15
            echo $this->extensions['MailPoet\Twig\I18n']->translate("View all stats");
            echo "
    ";
            // line 16
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, $context["newsletter"], "linkStats", [], "any", false, false, false, 16), "html", null, true);
            echo "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['newsletter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        echo "------------------------------------------
";
    }

    public function getTemplateName()
    {
        return "emails/statsNotificationAutomatedEmails.txt";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 18,  105 => 16,  101 => 15,  95 => 14,  89 => 13,  83 => 12,  77 => 11,  69 => 10,  65 => 9,  62 => 8,  58 => 7,  53 => 5,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/statsNotificationAutomatedEmails.txt", "/home/circleci/mailpoet/mailpoet/views/emails/statsNotificationAutomatedEmails.txt");
    }
}
