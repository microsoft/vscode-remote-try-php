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

/* newsletters.html */
class __TwigTemplate_9641d7769a94575cd44aaeff4df310da82a8fe07490b66905fc1e575dbaf51b2 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'after_translations' => [$this, 'block_after_translations'],
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
        $this->parent = $this->loadTemplate("layout.html", "newsletters.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "  <div id=\"newsletters_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 8
        echo "      var mailpoet_update_available = ";
        echo ((($context["is_mailpoet_update_available"] ?? null)) ? ("true") : ("false"));
        echo "
      var mailpoet_listing_per_page = ";
        // line 9
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["items_per_page"] ?? null), "js", null, true);
        echo ";
      var mailpoet_display_nps_poll = ";
        // line 10
        echo ((((($context["sent_newsletters_count"] ?? null) > 0) && \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["settings"] ?? null), "display_nps_poll", [], "any", false, false, false, 10))) ? ("true") : ("false"));
        echo ";
      var mailpoet_segments = ";
        // line 11
        echo json_encode(($context["segments"] ?? null));
        echo ";
      var mailpoet_show_congratulate_after_first_newsletter = ";
        // line 12
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["show_congratulate_after_first_newsletter"] ?? null), "js", null, true);
        echo ";
      var mailpoet_current_wp_user = ";
        // line 13
        echo json_encode(($context["current_wp_user"] ?? null));
        echo ";
      var mailpoet_current_wp_user_firstname = '";
        // line 14
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["current_wp_user_firstname"] ?? null), "js", null, true);
        echo "';
      var mailpoet_lists = ";
        // line 15
        echo json_encode(($context["lists"] ?? null));
        echo ";
      var mailpoet_roles = ";
        // line 16
        echo json_encode(($context["roles"] ?? null));
        echo ";
      var mailpoet_current_date = ";
        // line 17
        echo json_encode(($context["current_date"] ?? null));
        echo ";
      var mailpoet_tomorrow_date = ";
        // line 18
        echo json_encode(($context["tomorrow_date"] ?? null));
        echo ";
      var mailpoet_current_time = ";
        // line 19
        echo json_encode(($context["current_time"] ?? null));
        echo ";
      var mailpoet_current_date_time = ";
        // line 20
        echo json_encode(($context["current_date_time"] ?? null));
        echo ";
      var mailpoet_schedule_time_of_day = ";
        // line 21
        echo json_encode(($context["schedule_time_of_day"] ?? null));
        echo ";
      var mailpoet_date_storage_format = \"Y-m-d\";
      var mailpoet_product_categories = ";
        // line 23
        echo json_encode(($context["product_categories"] ?? null));
        echo ";
      var mailpoet_products = ";
        // line 24
        echo json_encode(($context["products"] ?? null));
        echo ";

      var mailpoet_account_url = '";
        // line 26
        echo $this->extensions['MailPoet\Twig\Functions']->addReferralId(((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&email=") . \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 26), "js")));
        echo "';

      var mailpoet_woocommerce_automatic_emails = ";
        // line 28
        echo json_encode(($context["automatic_emails"] ?? null));
        echo ";
      var mailpoet_woocommerce_optin_on_checkout = \"";
        // line 29
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["woocommerce_optin_on_checkout"] ?? null), "js", null, true);
        echo "\";

      var mailpoet_woocommerce_transactional_email_id = ";
        // line 31
        echo json_encode(($context["woocommerce_transactional_email_id"] ?? null));
        echo ";
      var mailpoet_display_detailed_stats = ";
        // line 32
        echo json_encode(($context["display_detailed_stats"] ?? null));
        echo ";
      var mailpoet_last_announcement_seen = ";
        // line 33
        echo json_encode(($context["last_announcement_seen"] ?? null));
        echo ";
      var mailpoet_user_locale = '";
        // line 34
        echo $this->extensions['MailPoet\Twig\I18n']->getLocale();
        echo "';
      var mailpoet_congratulations_success_images = [
        '";
        // line 36
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-1.png");
        echo "',
        '";
        // line 37
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-2.png");
        echo "',
        '";
        // line 38
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-3.png");
        echo "',
        '";
        // line 39
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-4.png");
        echo "',
      ];
      var mailpoet_congratulations_error_image = '";
        // line 41
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/error.png");
        echo "';
      var mailpoet_congratulations_loading_image = '";
        // line 42
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulation-page-illustration-transparent-LQ.20181121-1440.png");
        echo "';
      var mailpoet_emails_page = '";
        // line 43
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mailpoet_email_page"] ?? null), "js", null, true);
        echo "';
      var mailpoet_review_request_illustration_url = '";
        // line 44
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("review-request/review-request-illustration.20190815-1427.svg");
        echo "';
      var mailpoet_installed_at = '";
        // line 45
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["settings"] ?? null), "installed_at", [], "any", false, false, false, 45), "js", null, true);
        echo "';
      var mailpoet_editor_javascript_url = '";
        // line 46
        echo $this->extensions['MailPoet\Twig\Assets']->getJavascriptScriptUrl("newsletter_editor.js");
        echo "';
      var mailpoet_newsletters_count = ";
        // line 47
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["newsletters_count"] ?? null), "js", null, true);
        echo ";
      var mailpoet_authorized_emails = ";
        // line 48
        echo json_encode(($context["authorized_emails"] ?? null));
        echo ";
      var mailpoet_verified_sender_domains = ";
        // line 49
        echo json_encode(($context["verified_sender_domains"] ?? null));
        echo ";
      var mailpoet_partially_verified_sender_domains = ";
        // line 50
        echo json_encode(($context["partially_verified_sender_domains"] ?? null));
        echo ";
      var mailpoet_all_sender_domains = ";
        // line 51
        echo json_encode(($context["all_sender_domains"] ?? null));
        echo ";
      var mailpoet_sender_restrictions = ";
        // line 52
        echo json_encode(($context["sender_restrictions"] ?? null));
        echo ";
    ";
        // line 54
        echo "
    var mailpoet_newsletters_templates_recently_sent_count = ";
        // line 55
        echo json_decode(($context["newsletters_templates_recently_sent_count"] ?? null));
        echo ";
    var corrupt_newsletters = ";
        // line 56
        echo json_encode(($context["corrupt_newsletters"] ?? null));
        echo ";
    var mailpoet_legacy_automatic_emails_count = ";
        // line 57
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["legacy_automatic_emails_count"] ?? null), "html", null, true);
        echo ";
    var mailpoet_legacy_automatic_emails_notice_dismissed = ";
        // line 58
        echo json_encode(($context["legacy_automatic_emails_notice_dismissed"] ?? null));
        echo ";

  </script>
";
    }

    // line 63
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 64
        echo "  ";
        echo do_action("mailpoet_newsletters_translations_after");
        echo "
";
    }

    public function getTemplateName()
    {
        return "newsletters.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  246 => 64,  242 => 63,  234 => 58,  230 => 57,  226 => 56,  222 => 55,  219 => 54,  215 => 52,  211 => 51,  207 => 50,  203 => 49,  199 => 48,  195 => 47,  191 => 46,  187 => 45,  183 => 44,  179 => 43,  175 => 42,  171 => 41,  166 => 39,  162 => 38,  158 => 37,  154 => 36,  149 => 34,  145 => 33,  141 => 32,  137 => 31,  132 => 29,  128 => 28,  123 => 26,  118 => 24,  114 => 23,  109 => 21,  105 => 20,  101 => 19,  97 => 18,  93 => 17,  89 => 16,  85 => 15,  81 => 14,  77 => 13,  73 => 12,  69 => 11,  65 => 10,  61 => 9,  56 => 8,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletters.html", "/home/circleci/mailpoet/mailpoet/views/newsletters.html");
    }
}
