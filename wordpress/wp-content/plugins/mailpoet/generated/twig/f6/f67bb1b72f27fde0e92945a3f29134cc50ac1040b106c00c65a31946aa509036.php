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

/* emails/statsNotification.txt */
class __TwigTemplate_73223e600e7d98691234a281f9573e6bebabbe006922fa4a10de4b25ee450391 extends Template
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
        $this->parent = $this->loadTemplate("emails/statsNotificationLayout.txt", "emails/statsNotification.txt", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Your stats are in!");
        echo "

";
        // line 6
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["subject"] ?? null), "html", null, true);
        echo "

";
        // line 8
        if (($context["subscribersLimitReached"] ?? null)) {
            // line 9
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more than [subscribersLimit] subscribers!"), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
            echo "

";
            // line 11
            if (($context["hasValidApiKey"] ?? null)) {
                // line 12
                echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("Your plan is limited to [subscribersLimit] subscribers."), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
                echo "
";
            } else {
                // line 14
                echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers."), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
                echo "
";
            }
            // line 16
            echo $this->extensions['MailPoet\Twig\I18n']->translate("You need to upgrade now to be able to continue using MailPoet.");
            echo "

";
            // line 18
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade Now");
            echo "
  ";
            // line 19
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["upgradeNowLink"] ?? null), "html", null, true);
            echo "
";
        }
        // line 21
        echo "
";
        // line 22
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["clicked"] ?? null));
        echo "% ";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("clicked");
        echo "
  ";
        // line 23
        echo $this->extensions['MailPoet\Twig\Functions']->clickedStatsText(($context["clicked"] ?? null));
        echo "

";
        // line 25
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["opened"] ?? null));
        echo "% ";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("opened");
        echo "

";
        // line 27
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["machineOpened"] ?? null));
        echo "% ";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("machine-opened");
        echo "

";
        // line 29
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["unsubscribed"] ?? null));
        echo "% ";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("unsubscribed");
        echo "

";
        // line 31
        echo $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["bounced"] ?? null));
        echo "% ";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("bounced");
        echo "

";
        // line 33
        if ((($context["topLinkClicks"] ?? null) > 0)) {
            // line 34
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Most clicked link");
            echo "
  ";
            // line 35
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["topLink"] ?? null), "html", null, true);
            echo "

  ";
            // line 37
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_replace_filter($this->extensions['MailPoet\Twig\I18n']->translate("%s unique clicks"), ["%s" => ($context["topLinkClicks"] ?? null)]), "html", null, true);
            echo "
";
        }
        // line 39
        echo "
";
        // line 40
        echo $this->extensions['MailPoet\Twig\I18n']->translate("View all stats");
        echo "
  ";
        // line 41
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["linkStats"] ?? null), "html", null, true);
        echo "

";
    }

    public function getTemplateName()
    {
        return "emails/statsNotification.txt";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  158 => 41,  154 => 40,  151 => 39,  146 => 37,  141 => 35,  137 => 34,  135 => 33,  128 => 31,  121 => 29,  114 => 27,  107 => 25,  102 => 23,  96 => 22,  93 => 21,  88 => 19,  84 => 18,  79 => 16,  74 => 14,  69 => 12,  67 => 11,  62 => 9,  60 => 8,  55 => 6,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/statsNotification.txt", "/home/circleci/mailpoet/mailpoet/views/emails/statsNotification.txt");
    }
}
