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

/* forms.html */
class __TwigTemplate_8a6f0398fffc82506ba46e70538b728a453c02bea83ac685560c22b44c40173a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'after_javascript' => [$this, 'block_after_javascript'],
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
        $this->parent = $this->loadTemplate("layout.html", "forms.html", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "  <div id=\"forms_container\"></div>

  <div>
    <p class=\"mailpoet_sending_methods_help help\">
      ";
        // line 8
        $context["allowedHtml"] = ["a" => ["href" => [], "target" => [], "id" => []], "strong" => []];
        // line 9
        yield "      ";
        yield $this->extensions['MailPoet\Twig\Filters']->wpKses(MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translate("<strong>Tip:</strong> check out [link]this list[/link] of form plugins that integrate with MailPoet."), "https://kb.mailpoet.com/article/198-list-of-forms-plugins-that-work-with-mailpoet?utm_source=plugin&utm_medium=settings&utm_campaign=helpdocs", ["target" => "_blank", "id" => "mailpoet_helper_link"]),         // line 11
($context["allowedHtml"] ?? null));
        // line 12
        yield "
    </p>
  </div>

  <script type=\"text/javascript\">
    var mailpoet_listing_per_page = ";
        // line 17
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["items_per_page"] ?? null), "html", null, true);
        yield ";
    var mailpoet_segments = ";
        // line 18
        yield json_encode(($context["segments"] ?? null));
        yield ";
    var mailpoet_form_template_selection_url =
      \"";
        // line 20
        yield admin_url("admin.php?page=mailpoet-form-editor-template-selection");
        yield "\";
    var mailpoet_form_edit_url =
      \"";
        // line 22
        yield admin_url("admin.php?page=mailpoet-form-editor&id=");
        yield "\";

    var mailpoet_display_nps_poll = ";
        // line 24
        yield json_encode(($context["display_nps_survey"] ?? null));
        yield ";

    ";
        // line 26
        if (($context["display_nps_survey"] ?? null)) {
            // line 27
            yield "      var mailpoet_display_nps_form = true;
      var mailpoet_current_wp_user = ";
            // line 28
            yield json_encode(($context["current_wp_user"] ?? null));
            yield ";
      var mailpoet_current_wp_user_firstname = '";
            // line 29
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["current_wp_user_firstname"] ?? null), "html", null, true);
            yield "';
      var mailpoet_review_request_illustration_url = '";
            // line 30
            yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("review-request/review-request-illustration.20190815-1427.svg");
            yield "';
    ";
        }
        // line 32
        yield "  </script>

  ";
        // line 34
        yield $this->extensions['MailPoet\Twig\I18n']->localize(["pageTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Forms"), "searchLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Search"), "loadingItems" => $this->extensions['MailPoet\Twig\I18n']->translate("Loading forms..."), "noItemsFound" => $this->extensions['MailPoet\Twig\I18n']->translate("No forms were found. Why not create a new one?"), "permanentlyDeleted" => $this->extensions['MailPoet\Twig\I18n']->translate("%d forms permanently deleted."), "selectBulkAction" => $this->extensions['MailPoet\Twig\I18n']->translate("Select bulk action"), "bulkActions" => $this->extensions['MailPoet\Twig\I18n']->translate("Bulk Actions"), "apply" => $this->extensions['MailPoet\Twig\I18n']->translate("Apply"), "filter" => $this->extensions['MailPoet\Twig\I18n']->translate("Filter"), "emptyTrash" => $this->extensions['MailPoet\Twig\I18n']->translate("Empty Trash"), "selectAll" => $this->extensions['MailPoet\Twig\I18n']->translate("Select All"), "restore" => $this->extensions['MailPoet\Twig\I18n']->translate("Restore"), "deletePermanently" => $this->extensions['MailPoet\Twig\I18n']->translate("Delete Permanently"), "status" => $this->extensions['MailPoet\Twig\I18n']->translate("Status"), "active" => $this->extensions['MailPoet\Twig\I18n']->translate("Active"), "inactive" => $this->extensions['MailPoet\Twig\I18n']->translate("Not Active"), "formActivated" => $this->extensions['MailPoet\Twig\I18n']->translate("Your Form is now activated!"), "previousPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Previous page"), "firstPage" => $this->extensions['MailPoet\Twig\I18n']->translate("First page"), "nextPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Next page"), "lastPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Last page"), "currentPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Current Page"), "pageOutOf" => $this->extensions['MailPoet\Twig\I18n']->translate("of"), "numberOfItemsSingular" => $this->extensions['MailPoet\Twig\I18n']->translate("1 item"), "numberOfItemsMultiple" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d items"), "formName" => $this->extensions['MailPoet\Twig\I18n']->translate("Name"), "noName" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("no name", "fallback for forms without a name in a form list"), "segments" => $this->extensions['MailPoet\Twig\I18n']->translate("Lists"), "type" => $this->extensions['MailPoet\Twig\I18n']->translate("Type"), "userChoice" => $this->extensions['MailPoet\Twig\I18n']->translate("User choice:"), "signups" => $this->extensions['MailPoet\Twig\I18n']->translate("Sign-ups"), "updatedAt" => $this->extensions['MailPoet\Twig\I18n']->translate("Modified date"), "oneFormTrashed" => $this->extensions['MailPoet\Twig\I18n']->translate("1 form was moved to the trash."), "multipleFormsTrashed" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d forms were moved to the trash."), "oneFormDeleted" => $this->extensions['MailPoet\Twig\I18n']->translate("1 form was permanently deleted."), "multipleFormsDeleted" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d forms were permanently deleted."), "oneFormRestored" => $this->extensions['MailPoet\Twig\I18n']->translate("1 form has been restored from the trash."), "multipleFormsRestored" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d forms have been restored from the trash."), "edit" => $this->extensions['MailPoet\Twig\I18n']->translate("Edit"), "duplicate" => $this->extensions['MailPoet\Twig\I18n']->translate("Duplicate"), "formDuplicated" => $this->extensions['MailPoet\Twig\I18n']->translate("Form \"%1\$s\" has been duplicated."), "trash" => $this->extensions['MailPoet\Twig\I18n']->translate("Trash"), "moveToTrash" => $this->extensions['MailPoet\Twig\I18n']->translate("Move to trash"), "new" => $this->extensions['MailPoet\Twig\I18n']->translate("New Form"), "placeFormBellowPages" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Below pages", "This is a text on a widget that leads to settings for form placement"), "placeFixedBarFormOnPages" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Fixed bar", "This is a text on a widget that leads to settings for form placement - form type is fixed bar"), "placeSlideInFormOnPages" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Slide–in", "This is a text on a widget that leads to settings for form placement - form type is slide in"), "placePopupFormOnPages" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Pop-up", "This is a text on a widget that leads to settings for form placement - form type is pop-up, it will be displayed on page in a small modal window"), "placeFormOthers" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Others (widget)", "Placement of the form using theme widget"), "formSettingsCorrupted" => $this->extensions['MailPoet\Twig\I18n']->translate("Form settings of \"%1\$s\" form are corrupted. Please [link]reconfigure the form in the editor[/link].")]);
        // line 88
        yield "
";
        return; yield '';
    }

    // line 91
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "<script type=\"text/javascript\">
  jQuery('#mailpoet_helper_link').on('click', function() {
    MailPoet.trackEvent('Forms page > link to doc page');
  });
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "forms.html";
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
        return array (  125 => 91,  119 => 88,  117 => 34,  113 => 32,  108 => 30,  104 => 29,  100 => 28,  97 => 27,  95 => 26,  90 => 24,  85 => 22,  80 => 20,  75 => 18,  71 => 17,  64 => 12,  62 => 11,  60 => 9,  58 => 8,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "forms.html", "/home/circleci/mailpoet/mailpoet/views/forms.html");
    }
}
