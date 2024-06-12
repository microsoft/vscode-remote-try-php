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

/* subscribers/importExport/export.html */
class __TwigTemplate_d397b2a974f636a1e1c9b326eb2ccfd5695d20d9e10469f6f7ff8efdcee0d044 extends Template
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
        return "layout.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layout.html", "subscribers/importExport/export.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div id=\"mailpoet_subscribers_export\" class=\"wrap\">
  <h1 class=\"mailpoet-h1 mailpoet-title\">
    <span>";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Export");
        echo "</span>
    <a class=\"mailpoet-button button button-secondary button-small\" href=\"?page=mailpoet-subscribers#/\">";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Back to Subscribers");
        echo "</a>
  </h1>
  ";
        // line 9
        if (\MailPoetVendor\twig_test_empty(($context["segments"] ?? null))) {
            // line 10
            echo "  <div class=\"error\">
    <p>";
            // line 11
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Yikes! Couldn't find any subscribers");
            echo "</p>
  </div>
  ";
        }
        // line 14
        echo "  <div id=\"mailpoet-export\" class=\"mailpoet-tab-content\">
    <!-- Template data -->
  </div>
</div>
<script id=\"mailpoet_subscribers_export_template\" type=\"text/x-handlebars-template\">
  <div id=\"export_result_notice\" class=\"updated mailpoet_hidden\">
    <!-- Result message -->
  </div>
  <div class=\"mailpoet-settings-grid\">
    ";
        // line 23
        if ( !\MailPoetVendor\twig_test_empty(($context["segments"] ?? null))) {
            // line 24
            echo "      <div class=\"mailpoet-settings-label\">
        <label for=\"export_lists\">
          ";
            // line 26
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Pick one or multiple lists");
            echo "
        </label>
      </div>
      <div class=\"mailpoet-settings-inputs\">
        <div class=\"mailpoet-form-select mailpoet-form-input\">
          <select id=\"export_lists\" data-placeholder=\"";
            // line 31
            echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select", "Verb");
            echo "\" multiple=\"multiple\"></select>
        </div>
      </div>
    ";
        }
        // line 35
        echo "
    <div class=\"mailpoet-settings-label\">
      <label for=\"export_columns\">
        ";
        // line 38
        echo $this->extensions['MailPoet\Twig\I18n']->translate("List of fields to export");
        echo "
        <p class=\"description\">
          <a href=\"https://kb.mailpoet.com/article/245-what-is-global-status\" target=\"_blank\">
            ";
        // line 41
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read about the Global status.", "Link to a documentation page in the knowledge base about what is the subscriber global status");
        echo "
          </a>
        </p>
      </label>
    </div>
    <div class=\"mailpoet-settings-inputs\">
      <div class=\"mailpoet-form-select mailpoet-form-input\">
        <select id=\"export_columns\" data-placeholder=\"";
        // line 48
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select", "Verb");
        echo "\" multiple=\"multiple\"></select>
      </div>
    </div>

    <div class=\"mailpoet-settings-label\">
      ";
        // line 53
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Format");
        echo "
    </div>
    <div class=\"mailpoet-settings-inputs\">
      <div class=\"mailpoet-settings-inputs-row\">
        <label class=\"mailpoet-form-radio\">
          <input type=\"radio\" name=\"option_format\" id=\"export-format-csv\" value=\"csv\" checked>
          <span class=\"mailpoet-form-radio-control\"></span>
        </label>
        <label for=\"export-format-csv\">";
        // line 61
        echo $this->extensions['MailPoet\Twig\I18n']->translate("CSV file");
        echo "</label>
      </div>
      <div class=\"mailpoet-settings-inputs-row";
        // line 63
        if ( !($context["zipExtensionLoaded"] ?? null)) {
            echo " mailpoet-disabled";
        }
        echo "\">
        <label class=\"mailpoet-form-radio\">
          <input type=\"radio\" name=\"option_format\" id=\"export-format-xlsx\" value=\"xlsx\"";
        // line 65
        if ( !($context["zipExtensionLoaded"] ?? null)) {
            echo " disabled";
        }
        echo ">
          <span class=\"mailpoet-form-radio-control\"></span>
        </label>
        <label for=\"export-format-xlsx\">";
        // line 68
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Excel file");
        echo "</label>
      </div>
      ";
        // line 70
        if ( !($context["zipExtensionLoaded"] ?? null)) {
            // line 71
            echo "        <div class=\"inline notice notice-warning\">
          <p>";
            // line 72
            echo $this->extensions['MailPoet\Twig\I18n']->translate(MailPoet\Util\Helpers::replaceLinkTags("ZIP extension is required to create Excel files. Please refer to the [link]official PHP ZIP installation guide[/link] or contact your hosting provider’s technical support for instructions on how to install and load the ZIP extension.", "http://php.net/manual/en/zip.installation.php"));
            echo "</p>
        </div>
      ";
        }
        // line 75
        echo "    </div>

    <div class=\"mailpoet-settings-save\">
        <a href=\"javascript:;\" class=\"mailpoet-button mailpoet-disabled button-primary\" id=\"mailpoet-export-button\">
          ";
        // line 79
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Export");
        echo "
        </a>
    </div>
  </div>
</script>

<script type=\"text/javascript\">
  var
    segments = JSON.parse(\"";
        // line 87
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, ($context["segments"] ?? null), "js"), "html", null, true);
        echo "\"),
    subscriberFieldsSelect2 = JSON.parse(\"";
        // line 88
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, ($context["subscriberFieldsSelect2"] ?? null), "js"), "html", null, true);
        echo "\"),
    exportData = {
     segments: segments.length || null
    };
</script>

";
        // line 94
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["serverError" => $this->extensions['MailPoet\Twig\I18n']->translate("Server error:"), "exportMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$s subscribers were exported. Get the exported file [link]here[/link].")]);
        // line 97
        echo "
";
    }

    public function getTemplateName()
    {
        return "subscribers/importExport/export.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  214 => 97,  212 => 94,  203 => 88,  199 => 87,  188 => 79,  182 => 75,  176 => 72,  173 => 71,  171 => 70,  166 => 68,  158 => 65,  151 => 63,  146 => 61,  135 => 53,  127 => 48,  117 => 41,  111 => 38,  106 => 35,  99 => 31,  91 => 26,  87 => 24,  85 => 23,  74 => 14,  68 => 11,  65 => 10,  63 => 9,  58 => 7,  54 => 6,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscribers/importExport/export.html", "/home/circleci/mailpoet/mailpoet/views/subscribers/importExport/export.html");
    }
}
