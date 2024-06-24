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

/* emails/newSubscriberNotification.txt */
class __TwigTemplate_cc8efaa8d3c388c3a2e0e52f0b49037b3b775dd64304859aa3e0b97b4d76d2b0 extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Howdy,");
        yield "

";
        // line 3
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::replace($this->extensions['MailPoet\Twig\I18n']->translate("The subscriber %1\$s has just subscribed to your list %2\$s!"), ["%1\$s" =>         // line 4
($context["subscriber_email"] ?? null), "%2\$s" => ($context["segments_names"] ?? null)]), "html", null, true);
        // line 5
        yield "

";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Cheers,");
        yield "
";
        // line 8
        yield $this->extensions['MailPoet\Twig\I18n']->translate("The MailPoet Plugin");
        yield "

";
        // line 10
        yield $this->extensions['MailPoet\Twig\I18n']->translate("You can disable these emails in your MailPoet Settings.");
        yield "
";
        // line 11
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["link_settings"] ?? null), "html", null, true);
        yield "

";
        // line 13
        if (($this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatDate("now", "Y-m-d") < $this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatDate("2018-11-30", "Y-m-d"))) {
            // line 14
            yield "    ";
            yield $this->extensions['MailPoet\Twig\I18n']->translate("PS. MailPoet annual plans are nearly half price for a limited time. Find out more in the Premium page in your admin.");
            yield "
    ";
            // line 15
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["link_premium"] ?? null), "html", null, true);
            yield "
";
        }
        // line 17
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "emails/newSubscriberNotification.txt";
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
        return array (  80 => 17,  75 => 15,  70 => 14,  68 => 13,  63 => 11,  59 => 10,  54 => 8,  50 => 7,  46 => 5,  44 => 4,  43 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/newSubscriberNotification.txt", "/home/circleci/mailpoet/mailpoet/views/emails/newSubscriberNotification.txt");
    }
}
