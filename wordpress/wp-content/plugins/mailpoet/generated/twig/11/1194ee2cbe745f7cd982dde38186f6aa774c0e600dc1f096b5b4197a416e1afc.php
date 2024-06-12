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
        echo "
Parsley.addMessages('mailpoet', {
  defaultMessage: '";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
  type: {
    email: '";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid email.");
        echo "',
    url: '";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid url.");
        echo "',
    number: '";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid number.");
        echo "',
    integer: '";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid integer.");
        echo "',
    digits: '";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be digits.");
        echo "',
    alphanum: '";
        // line 10
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be alphanumeric.");
        echo "'
  },
  notblank: '";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should not be blank.");
        echo "',
  required: '";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is required.");
        echo "',
  pattern: '";
        // line 14
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
  min: '";
        // line 15
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be greater than or equal to %s.");
        echo "',
  max: '";
        // line 16
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be lower than or equal to %s.");
        echo "',
  range: '";
        // line 17
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be between %s and %s.");
        echo "',
  minlength: '";
        // line 18
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too short. It should have %s characters or more.");
        echo "',
  maxlength: '";
        // line 19
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too long. It should have %s characters or fewer.");
        echo "',
  length: '";
        // line 20
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value length is invalid. It should be between %s and %s characters long.");
        echo "',
  mincheck: '";
        // line 21
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select at least %s choices.");
        echo "',
  maxcheck: '";
        // line 22
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select %s choices or fewer.");
        echo "',
  check: '";
        // line 23
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select between %s and %s choices.");
        echo "',
  equalto: '";
        // line 24
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be the same.");
        echo "'
});

Parsley.setLocale('mailpoet');
";
    }

    public function getTemplateName()
    {
        return "parsley-translations.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  119 => 24,  115 => 23,  111 => 22,  107 => 21,  103 => 20,  99 => 19,  95 => 18,  91 => 17,  87 => 16,  83 => 15,  79 => 14,  75 => 13,  71 => 12,  66 => 10,  62 => 9,  58 => 8,  54 => 7,  50 => 6,  46 => 5,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "parsley-translations.html", "/home/circleci/mailpoet/mailpoet/views/parsley-translations.html");
    }
}
