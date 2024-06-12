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
        echo "<p>";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Howdy,");
        echo "

<p>";
        // line 3
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("The subscriber %1\$s has just subscribed to your list %2\$s!"), ["%1\$s" =>         // line 4
($context["subscriber_email"] ?? null), "%2\$s" => ($context["segments_names"] ?? null)]), "html", null, true);
        // line 5
        echo "

<p>";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Cheers,");
        echo "

<p>";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translate("The MailPoet Plugin");
        echo "

<p><small>";
        // line 11
        echo $this->extensions['MailPoet\Twig\I18n']->translate(MailPoet\Util\Helpers::replaceLinkTags("You can disable these emails in your [link]MailPoet Settings.[/link]",         // line 12
($context["link_settings"] ?? null)));
        // line 13
        echo "</small>

";
        // line 15
        if ((\MailPoetVendor\twig_date_format_filter($this->env, "now", "Y-m-d") < \MailPoetVendor\twig_date_format_filter($this->env, "2018-11-30", "Y-m-d"))) {
            // line 16
            echo "  <p>
    <small>
      ";
            // line 18
            echo $this->extensions['MailPoet\Twig\I18n']->translate(MailPoet\Util\Helpers::replaceLinkTags("PS. MailPoet annual plans are nearly half price for a limited time.
      [link]Find out more in the Premium page in your admin.[/link]",             // line 20
($context["link_premium"] ?? null)));
            // line 21
            echo "
  </small>
";
        }
    }

    public function getTemplateName()
    {
        return "emails/newSubscriberNotification.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 21,  75 => 20,  73 => 18,  69 => 16,  67 => 15,  63 => 13,  61 => 12,  60 => 11,  55 => 9,  50 => 7,  46 => 5,  44 => 4,  43 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/newSubscriberNotification.html", "/home/circleci/mailpoet/mailpoet/views/emails/newSubscriberNotification.html");
    }
}
