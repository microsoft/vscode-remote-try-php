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

/* emails/newSubscriberNotification.html */
class __TwigTemplate_1a503b1b477150cebe094ddd70d8a9f0f9df76c4d7e651ce72ce81dad2c9bd66 extends Template
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
        yield "<p>";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Howdy,");
        yield "

<p>";
        // line 3
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::replace($this->extensions['MailPoet\Twig\I18n']->translate("The subscriber %1\$s has just subscribed to your list %2\$s!"), ["%1\$s" =>         // line 4
($context["subscriber_email"] ?? null), "%2\$s" => ($context["segments_names"] ?? null)]), "html", null, true);
        // line 5
        yield "

<p>";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Cheers,");
        yield "

<p>";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translate("The MailPoet Plugin");
        yield "

<p><small>";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate(MailPoet\Util\Helpers::replaceLinkTags("You can disable these emails in your [link]MailPoet Settings.[/link]",         // line 12
($context["link_settings"] ?? null)));
        // line 13
        yield "</small>

";
        // line 15
        if (($this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatDate("now", "Y-m-d") < $this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatDate("2018-11-30", "Y-m-d"))) {
            // line 16
            yield "  <p>
    <small>
      ";
            // line 18
            yield $this->extensions['MailPoet\Twig\I18n']->translate(MailPoet\Util\Helpers::replaceLinkTags("PS. MailPoet annual plans are nearly half price for a limited time.
      [link]Find out more in the Premium page in your admin.[/link]",             // line 20
($context["link_premium"] ?? null)));
            // line 21
            yield "
  </small>
";
        }
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "emails/newSubscriberNotification.html";
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
        return array (  78 => 21,  76 => 20,  74 => 18,  70 => 16,  68 => 15,  64 => 13,  62 => 12,  61 => 11,  56 => 9,  51 => 7,  47 => 5,  45 => 4,  44 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/newSubscriberNotification.html", "/home/circleci/mailpoet/mailpoet/views/emails/newSubscriberNotification.html");
    }
}
