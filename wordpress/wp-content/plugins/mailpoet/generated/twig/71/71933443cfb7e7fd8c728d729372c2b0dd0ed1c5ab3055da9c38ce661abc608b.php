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

/* form/form_preview.html */
class __TwigTemplate_adb8eaf54da5046390145eb13175732db5c6d227482e34326cc5460308397132 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        return; yield '';
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        yield "  ";
        if ((($context["formType"] ?? null) == "others")) {
            // line 3
            yield "    <div id=\"mailpoet_widget_preview\" class=\"mailpoet_widget_preview\">
      <div id=\"sidebar\" class=\"sidebar widget-area si-sidebar-container\">
        <div class=\"widget si-widget\">
          ";
            // line 6
            yield ($context["form"] ?? null);
            yield "
        </div>
      </div>
    </div>
  ";
        } else {
            // line 11
            yield "    ";
            yield ($context["post"] ?? null);
            yield "
    ";
            // line 12
            yield ($context["form"] ?? null);
            yield "
  ";
        }
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/form_preview.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  68 => 12,  63 => 11,  55 => 6,  50 => 3,  47 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/form_preview.html", "/home/circleci/mailpoet/mailpoet/views/form/form_preview.html");
    }
}
