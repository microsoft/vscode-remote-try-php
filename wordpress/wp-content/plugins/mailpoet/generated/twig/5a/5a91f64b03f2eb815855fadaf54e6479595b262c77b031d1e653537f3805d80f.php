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

/* emails/congratulatoryMssEmail.txt */
class __TwigTemplate_aab054d1ce71cebb286bd048e43bc4591e33d0913d0f18c524b7aaf2e003ae26 extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Congrats!");
        yield "

";
        // line 3
        yield $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet is now sending your emails");
        yield "

";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This email was sent automatically with the MailPoet Sending Service after you activated your key in your MailPoet settings.");
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "emails/congratulatoryMssEmail.txt";
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
        return array (  48 => 5,  43 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "emails/congratulatoryMssEmail.txt", "/home/circleci/mailpoet/mailpoet/views/emails/congratulatoryMssEmail.txt");
    }
}
