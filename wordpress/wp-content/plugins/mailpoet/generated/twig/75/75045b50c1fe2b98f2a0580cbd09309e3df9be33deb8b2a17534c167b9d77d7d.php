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

/* subscribers/importExport/import/step_data_manipulation.html */
class __TwigTemplate_1a7328011b0cf7158e35fc314e7b6993b8417919d1f5ef0a796941a8e337f4a3 extends Template
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
        echo "<div id=\"step_data_manipulation\" class=\"mailpoet_hidden\">
  <div class=\"inside\">

    <!-- New segment template -->
    <script id=\"new_segment_template\" type=\"text/x-handlebars-template\">
      <p>
        <label>";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Name");
        echo ":</label>
        <br/>
        <div class=\"mailpoet-form-input\">
          <input id=\"new_segment_name\" type=\"text\" name=\"name\" />
        </div>
      </p>
      <p class=\"mailpoet_validation_error\" data-error=\"segment_name_required\">
        ";
        // line 14
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Please specify a name.");
        echo "
      </p>
      <p class=\"mailpoet_validation_error\" data-error=\"segment_name_not_unique\">
        ";
        // line 17
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_sprintf($this->extensions['MailPoet\Twig\I18n']->translate("Another record already exists. Please specify a different \"%1\$s\"."), "name"), "html", null, true);
        echo "
      </p>
      <p>
        <label>";
        // line 20
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Description");
        echo ":</label>
        <br/>
        <div class=\"mailpoet-form-textarea\">
          <textarea id=\"new_segment_description\" cols=\"40\" rows=\"3\" name=\"description\"></textarea>
        </div>
      </p>

      <p class=\"mailpoet_align_right\">
        <input type=\"submit\" value=\"";
        // line 28
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Cancel");
        echo "\" id=\"new_segment_cancel\" class=\"mailpoet-button button-secondary\"/>
        <input type=\"submit\" value=\"";
        // line 29
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Done");
        echo "\" id=\"new_segment_process\" class=\"mailpoet-button button-primary\"/>
      </p>

      </form>
    </script>

    <!-- New custom field logic -->
    ";
        // line 36
        $this->loadTemplate("form/custom_fields_legacy.html", "subscribers/importExport/import/step_data_manipulation.html", 36)->display($context);
        // line 37
        echo "  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "subscribers/importExport/import/step_data_manipulation.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 37,  92 => 36,  82 => 29,  78 => 28,  67 => 20,  61 => 17,  55 => 14,  45 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscribers/importExport/import/step_data_manipulation.html", "/home/circleci/mailpoet/mailpoet/views/subscribers/importExport/import/step_data_manipulation.html");
    }
}
