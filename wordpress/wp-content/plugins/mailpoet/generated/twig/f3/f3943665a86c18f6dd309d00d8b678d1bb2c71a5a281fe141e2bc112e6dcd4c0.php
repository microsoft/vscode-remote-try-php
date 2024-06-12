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

/* deactivationPoll/index.html */
class __TwigTemplate_9faa4e87f1ef54ddc698a901ff5f80ef1be75b3ed754540e3a33f2e606c19802 extends Template
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
        if ($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled()) {
            // line 2
            echo "  ";
            $this->loadTemplate("deactivationPoll/embedded-poll.html", "deactivationPoll/index.html", 2)->display($context);
        } else {
            // line 4
            echo "  ";
            $this->loadTemplate("deactivationPoll/link-poll.html", "deactivationPoll/index.html", 4)->display($context);
        }
        // line 6
        echo "
";
    }

    public function getTemplateName()
    {
        return "deactivationPoll/index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 6,  43 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "deactivationPoll/index.html", "/home/circleci/mailpoet/mailpoet/views/deactivationPoll/index.html");
    }
}
