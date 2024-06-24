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

/* deactivationPoll/css.html */
class __TwigTemplate_66e5c7f2a25bb7ec2578fe784e34b96a021b3bac5a6c8373f944c68964e62ca6 extends Template
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
        yield "<style type=\"text/css\">
  .mailpoet-deactivate-survey-modal {
    display: none;
    table-layout: fixed;
    position: fixed;
    z-index: 9999;
    width: 100%;
    height: 100%;
    text-align: center;
    font-size: 14px;
    top: 0;
    left: 0;
    background: rgba(0,0,0,0.8);
  }
  .mailpoet-deactivate-survey-wrap {
    display: table-cell;
    vertical-align: middle;
  }

  .mailpoet-deactivate-survey {
    background-color: #fff;
    border: 0 solid #ccc;
    border-radius: 3px;
    margin: 0 auto;
    padding: 12px;
    width: 520px;
    direction: ltr;
  }

  .mailpoet-deactivate-survey a.button {
    white-space: normal;
    height: auto;
  }

  .pds-box .pds-vote {
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "deactivationPoll/css.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array ();
    }

    public function getSourceContext()
    {
        return new Source("", "deactivationPoll/css.html", "/home/circleci/mailpoet/mailpoet/views/deactivationPoll/css.html");
    }
}
