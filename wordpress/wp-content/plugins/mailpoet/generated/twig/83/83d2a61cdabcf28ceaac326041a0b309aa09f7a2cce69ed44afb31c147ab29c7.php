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

/* form/templatesLegacy/settings/segment_selection_item.hbs */
class __TwigTemplate_3c8166e1146014d6c6e6c40c753a6f92cb33e9423fe4045de5eee5d7079da003 extends Template
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
        yield "{{#each segments}}
<li data-segment=\"{{ id }}\">
  <label>
    <input class=\"mailpoet_segment_id\" type=\"hidden\" value=\"{{ id }}\" />
    <input class=\"mailpoet_is_checked\" type=\"checkbox\" value=\"1\"
      {{#ifCond is_checked '>' 0}}checked=\"checked\"{{/ifCond}} />
    <input class=\"mailpoet_segment_name\" type=\"hidden\" value=\"{{ name }}\" />
    {{ name }}
  </label>
  <a class=\"remove\" href=\"javascript:;\">";
        // line 10
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Remove");
        yield "</a>
  <a class=\"handle\" href=\"javascript:;\">";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Move");
        yield "</a>
</li>
{{/each}}";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/segment_selection_item.hbs";
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
        return array (  53 => 11,  49 => 10,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/segment_selection_item.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/segment_selection_item.hbs");
    }
}
