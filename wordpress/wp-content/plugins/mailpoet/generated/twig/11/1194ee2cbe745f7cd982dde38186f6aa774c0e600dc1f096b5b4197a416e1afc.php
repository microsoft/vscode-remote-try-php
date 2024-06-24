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

/* parsley-translations.html */
class __TwigTemplate_c57fdfc1a59b00dc7129161c18976ad5ba36cdd436a8430e0c1963def69c2803 extends Template
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
        yield "
Parsley.addMessages('mailpoet', {
  defaultMessage: '";
        // line 3
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        yield "',
  type: {
    email: '";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid email.");
        yield "',
    url: '";
        // line 6
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid url.");
        yield "',
    number: '";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid number.");
        yield "',
    integer: '";
        // line 8
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid integer.");
        yield "',
    digits: '";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be digits.");
        yield "',
    alphanum: '";
        // line 10
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be alphanumeric.");
        yield "'
  },
  notblank: '";
        // line 12
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should not be blank.");
        yield "',
  required: '";
        // line 13
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value is required.");
        yield "',
  pattern: '";
        // line 14
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        yield "',
  min: '";
        // line 15
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be greater than or equal to %s.");
        yield "',
  max: '";
        // line 16
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be lower than or equal to %s.");
        yield "',
  range: '";
        // line 17
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be between %s and %s.");
        yield "',
  minlength: '";
        // line 18
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value is too short. It should have %s characters or more.");
        yield "',
  maxlength: '";
        // line 19
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value is too long. It should have %s characters or fewer.");
        yield "',
  length: '";
        // line 20
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value length is invalid. It should be between %s and %s characters long.");
        yield "',
  mincheck: '";
        // line 21
        yield $this->extensions['MailPoet\Twig\I18n']->translate("You must select at least %s choices.");
        yield "',
  maxcheck: '";
        // line 22
        yield $this->extensions['MailPoet\Twig\I18n']->translate("You must select %s choices or fewer.");
        yield "',
  check: '";
        // line 23
        yield $this->extensions['MailPoet\Twig\I18n']->translate("You must select between %s and %s choices.");
        yield "',
  equalto: '";
        // line 24
        yield $this->extensions['MailPoet\Twig\I18n']->translate("This value should be the same.");
        yield "'
});

Parsley.setLocale('mailpoet');
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "parsley-translations.html";
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
        return array (  120 => 24,  116 => 23,  112 => 22,  108 => 21,  104 => 20,  100 => 19,  96 => 18,  92 => 17,  88 => 16,  84 => 15,  80 => 14,  76 => 13,  72 => 12,  67 => 10,  63 => 9,  59 => 8,  55 => 7,  51 => 6,  47 => 5,  42 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "parsley-translations.html", "/home/circleci/mailpoet/mailpoet/views/parsley-translations.html");
    }
}
