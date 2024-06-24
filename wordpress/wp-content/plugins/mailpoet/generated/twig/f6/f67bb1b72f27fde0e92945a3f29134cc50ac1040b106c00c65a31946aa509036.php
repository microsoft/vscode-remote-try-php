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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Your stats are in!");
        yield "

";
        // line 6
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["subject"] ?? null), "html", null, true);
        yield "

";
        // line 8
        if (($context["subscribersLimitReached"] ?? null)) {
            // line 9
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::replace($this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more than [subscribersLimit] subscribers!"), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
            yield "

";
            // line 11
            if (($context["hasValidApiKey"] ?? null)) {
                // line 12
                yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::replace($this->extensions['MailPoet\Twig\I18n']->translate("Your plan is limited to [subscribersLimit] subscribers."), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
                yield "
";
            } else {
                // line 14
                yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::replace($this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers."), ["[subscribersLimit]" => ($context["subscribersLimit"] ?? null)]), "html", null, true);
                yield "
";
            }
            // line 16
            yield $this->extensions['MailPoet\Twig\I18n']->translate("You need to upgrade now to be able to continue using MailPoet.");
            yield "

";
            // line 18
            yield $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade Now");
            yield "
  ";
            // line 19
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["upgradeNowLink"] ?? null), "html", null, true);
            yield "
";
        }
        // line 21
        yield "
";
        // line 22
        yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["clicked"] ?? null));
        yield "% ";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("clicked");
        yield "
  ";
        // line 23
        yield $this->extensions['MailPoet\Twig\Functions']->clickedStatsText(($context["clicked"] ?? null));
        yield "

";
        // line 25
        yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["opened"] ?? null));
        yield "% ";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("opened");
        yield "

";
        // line 27
        yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["machineOpened"] ?? null));
        yield "% ";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("machine-opened");
        yield "

";
        // line 29
        yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["unsubscribed"] ?? null));
        yield "% ";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("unsubscribed");
        yield "

";
        // line 31
        yield $this->extensions['MailPoet\Twig\Functions']->statsNumberFormatI18n(($context["bounced"] ?? null));
        yield "% ";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("bounced");
        yield "

";
        // line 33
        if ((($context["topLinkClicks"] ?? null) > 0)) {
            // line 34
            yield $this->extensions['MailPoet\Twig\I18n']->translate("Most clicked link");
            yield "
  ";
            // line 35
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["topLink"] ?? null), "html", null, true);
            yield "

  ";
            // line 37
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(MailPoetVendor\Twig\Extension\CoreExtension::replace($this->extensions['MailPoet\Twig\I18n']->translate("%s unique clicks"), ["%s" => ($context["topLinkClicks"] ?? null)]), "html", null, true);
            yield "
";
        }
        // line 39
        yield "
";
        // line 40
        yield $this->extensions['MailPoet\Twig\I18n']->translate("View all stats");
        yield "
  ";
        // line 41
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["linkStats"] ?? null), "html", null, true);
        yield "

";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "emails/statsNotification.txt";
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
        return array (  159 => 41,  155 => 40,  152 => 39,  147 => 37,  142 => 35,  138 => 34,  136 => 33,  129 => 31,  122 => 29,  115 => 27,  108 => 25,  103 => 23,  97 => 22,  94 => 21,  89 => 19,  85 => 18,  80 => 16,  75 => 14,  70 => 12,  68 => 11,  63 => 9,  61 => 8,  56 => 6,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/statsNotification.txt", "/home/circleci/mailpoet/mailpoet/views/emails/statsNotification.txt");
    }
}
