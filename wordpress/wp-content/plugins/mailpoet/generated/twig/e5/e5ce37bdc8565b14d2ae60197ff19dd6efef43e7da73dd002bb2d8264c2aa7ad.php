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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "<div id=\"mailpoet_subscribers_export\" class=\"wrap\">
  <h1 class=\"mailpoet-h1 mailpoet-title\">
    <span>";
        // line 6
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Export");
        yield "</span>
    <a class=\"mailpoet-button button button-secondary button-small\" href=\"?page=mailpoet-subscribers#/\">";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Back to Subscribers");
        yield "</a>
  </h1>
  ";
        // line 9
        if (MailPoetVendor\Twig\Extension\CoreExtension::testEmpty(($context["segments"] ?? null))) {
            // line 10
            yield "  <div class=\"error\">
    <p>";
            // line 11
            yield $this->extensions['MailPoet\Twig\I18n']->translate("Yikes! Couldn't find any subscribers");
            yield "</p>
  </div>
  ";
        }
        // line 14
        yield "  <div id=\"mailpoet-export\" class=\"mailpoet-tab-content\">
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
        if ( !MailPoetVendor\Twig\Extension\CoreExtension::testEmpty(($context["segments"] ?? null))) {
            // line 24
            yield "      <div class=\"mailpoet-settings-label\">
        <label for=\"export_lists\">
          ";
            // line 26
            yield $this->extensions['MailPoet\Twig\I18n']->translate("Pick one or multiple lists");
            yield "
        </label>
      </div>
      <div class=\"mailpoet-settings-inputs\">
        <div class=\"mailpoet-form-select mailpoet-form-input\">
          <select id=\"export_lists\" data-placeholder=\"";
            // line 31
            yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select", "Verb");
            yield "\" multiple=\"multiple\"></select>
        </div>
      </div>
    ";
        }
        // line 35
        yield "
    <div class=\"mailpoet-settings-label\">
      <label for=\"export_columns\">
        ";
        // line 38
        yield $this->extensions['MailPoet\Twig\I18n']->translate("List of fields to export");
        yield "
        <p class=\"description\">
          <a href=\"https://kb.mailpoet.com/article/245-what-is-global-status\" target=\"_blank\">
            ";
        // line 41
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read about the Global status.", "Link to a documentation page in the knowledge base about what is the subscriber global status");
        yield "
          </a>
        </p>
      </label>
    </div>
    <div class=\"mailpoet-settings-inputs\">
      <div class=\"mailpoet-form-select mailpoet-form-input\">
        <select id=\"export_columns\" data-placeholder=\"";
        // line 48
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select", "Verb");
        yield "\" multiple=\"multiple\"></select>
      </div>
    </div>

    <div class=\"mailpoet-settings-label\">
      ";
        // line 53
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Format");
        yield "
    </div>
    <div class=\"mailpoet-settings-inputs\">
      <div class=\"mailpoet-settings-inputs-row\">
        <label class=\"mailpoet-form-radio\">
          <input type=\"radio\" name=\"option_format\" id=\"export-format-csv\" value=\"csv\" checked>
          <span class=\"mailpoet-form-radio-control\"></span>
        </label>
        <label for=\"export-format-csv\">";
        // line 61
        yield $this->extensions['MailPoet\Twig\I18n']->translate("CSV file");
        yield "</label>
      </div>
      <div class=\"mailpoet-settings-inputs-row";
        // line 63
        if ( !($context["zipExtensionLoaded"] ?? null)) {
            yield " mailpoet-disabled";
        }
        yield "\">
        <label class=\"mailpoet-form-radio\">
          <input type=\"radio\" name=\"option_format\" id=\"export-format-xlsx\" value=\"xlsx\"";
        // line 65
        if ( !($context["zipExtensionLoaded"] ?? null)) {
            yield " disabled";
        }
        yield ">
          <span class=\"mailpoet-form-radio-control\"></span>
        </label>
        <label for=\"export-format-xlsx\">";
        // line 68
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Excel file");
        yield "</label>
      </div>
      ";
        // line 70
        if ( !($context["zipExtensionLoaded"] ?? null)) {
            // line 71
            yield "        <div class=\"inline notice notice-warning\">
          <p>";
            // line 72
            yield $this->extensions['MailPoet\Twig\I18n']->translate(MailPoet\Util\Helpers::replaceLinkTags("ZIP extension is required to create Excel files. Please refer to the [link]official PHP ZIP installation guide[/link] or contact your hosting provider’s technical support for instructions on how to install and load the ZIP extension.", "http://php.net/manual/en/zip.installation.php"));
            yield "</p>
        </div>
      ";
        }
        // line 75
        yield "    </div>

    <div class=\"mailpoet-settings-save\">
        <a href=\"javascript:;\" class=\"mailpoet-button mailpoet-disabled button-primary\" id=\"mailpoet-export-button\">
          ";
        // line 79
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Export");
        yield "
        </a>
    </div>
  </div>
</script>

<script type=\"text/javascript\">
  var
    segments = JSON.parse(\"";
        // line 87
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["segments"] ?? null), "js"), "html", null, true);
        yield "\"),
    subscriberFieldsSelect2 = JSON.parse(\"";
        // line 88
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["subscriberFieldsSelect2"] ?? null), "js"), "html", null, true);
        yield "\"),
    exportData = {
     segments: segments.length || null
    };
</script>

";
        // line 94
        yield $this->extensions['MailPoet\Twig\I18n']->localize(["serverError" => $this->extensions['MailPoet\Twig\I18n']->translate("Server error:"), "exportMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$s subscribers were exported. Get the exported file [link]here[/link].")]);
        // line 97
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "subscribers/importExport/export.html";
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
        return array (  215 => 97,  213 => 94,  204 => 88,  200 => 87,  189 => 79,  183 => 75,  177 => 72,  174 => 71,  172 => 70,  167 => 68,  159 => 65,  152 => 63,  147 => 61,  136 => 53,  128 => 48,  118 => 41,  112 => 38,  107 => 35,  100 => 31,  92 => 26,  88 => 24,  86 => 23,  75 => 14,  69 => 11,  66 => 10,  64 => 9,  59 => 7,  55 => 6,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscribers/importExport/export.html", "/home/circleci/mailpoet/mailpoet/views/subscribers/importExport/export.html");
    }
}
