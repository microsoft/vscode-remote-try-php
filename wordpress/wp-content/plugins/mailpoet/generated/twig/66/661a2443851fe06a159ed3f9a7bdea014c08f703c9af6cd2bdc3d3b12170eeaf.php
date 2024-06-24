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

/* premium_key_validation_strings.html */
class __TwigTemplate_ef144945da24072956cf965a52a49e213b9cecc6662a1aecc7f22c9163064c8e extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->localize(["learnMore" => $this->extensions['MailPoet\Twig\I18n']->translate("Learn more"), "premiumTabPremiumNotActivatedMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Premium is installed but not activated.", "mailpoet"), "premiumTabMssActivateMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Activate MailPoet Sending Service", "mailpoet")]);
        // line 5
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "premium_key_validation_strings.html";
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
        return array (  40 => 5,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "premium_key_validation_strings.html", "/home/circleci/mailpoet/mailpoet/views/premium_key_validation_strings.html");
    }
}
