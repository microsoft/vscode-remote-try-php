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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "  <div id=\"newsletters_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 8
        yield "      var mailpoet_update_available = ";
        yield ((($context["is_mailpoet_update_available"] ?? null)) ? ("true") : ("false"));
        yield "
      var mailpoet_listing_per_page = ";
        // line 9
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["items_per_page"] ?? null), "js", null, true);
        yield ";
      var mailpoet_display_nps_poll = ";
        // line 10
        yield ((((($context["sent_newsletters_count"] ?? null) > 0) && CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "display_nps_poll", [], "any", false, false, false, 10))) ? ("true") : ("false"));
        yield ";
      var mailpoet_segments = ";
        // line 11
        yield json_encode(($context["segments"] ?? null));
        yield ";
      var mailpoet_show_congratulate_after_first_newsletter = ";
        // line 12
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["show_congratulate_after_first_newsletter"] ?? null), "js", null, true);
        yield ";
      var mailpoet_current_wp_user = ";
        // line 13
        yield json_encode(($context["current_wp_user"] ?? null));
        yield ";
      var mailpoet_current_wp_user_firstname = '";
        // line 14
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["current_wp_user_firstname"] ?? null), "js", null, true);
        yield "';
      var mailpoet_lists = ";
        // line 15
        yield json_encode(($context["lists"] ?? null));
        yield ";
      var mailpoet_roles = ";
        // line 16
        yield json_encode(($context["roles"] ?? null));
        yield ";
      var mailpoet_current_date = ";
        // line 17
        yield json_encode(($context["current_date"] ?? null));
        yield ";
      var mailpoet_tomorrow_date = ";
        // line 18
        yield json_encode(($context["tomorrow_date"] ?? null));
        yield ";
      var mailpoet_current_time = ";
        // line 19
        yield json_encode(($context["current_time"] ?? null));
        yield ";
      var mailpoet_current_date_time = ";
        // line 20
        yield json_encode(($context["current_date_time"] ?? null));
        yield ";
      var mailpoet_schedule_time_of_day = ";
        // line 21
        yield json_encode(($context["schedule_time_of_day"] ?? null));
        yield ";
      var mailpoet_date_storage_format = \"Y-m-d\";
      var mailpoet_product_categories = ";
        // line 23
        yield json_encode(($context["product_categories"] ?? null));
        yield ";
      var mailpoet_products = ";
        // line 24
        yield json_encode(($context["products"] ?? null));
        yield ";

      var mailpoet_account_url = '";
        // line 26
        yield $this->extensions['MailPoet\Twig\Functions']->addReferralId(((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&email=") . $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 26), "js")));
        yield "';

      var mailpoet_woocommerce_automatic_emails = ";
        // line 28
        yield json_encode(($context["automatic_emails"] ?? null));
        yield ";
      var mailpoet_woocommerce_optin_on_checkout = \"";
        // line 29
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["woocommerce_optin_on_checkout"] ?? null), "js", null, true);
        yield "\";

      var mailpoet_woocommerce_transactional_email_id = ";
        // line 31
        yield json_encode(($context["woocommerce_transactional_email_id"] ?? null));
        yield ";
      var mailpoet_display_detailed_stats = ";
        // line 32
        yield json_encode(($context["display_detailed_stats"] ?? null));
        yield ";
      var mailpoet_last_announcement_seen = ";
        // line 33
        yield json_encode(($context["last_announcement_seen"] ?? null));
        yield ";
      var mailpoet_user_locale = '";
        // line 34
        yield $this->extensions['MailPoet\Twig\I18n']->getLocale();
        yield "';
      var mailpoet_congratulations_success_images = [
        '";
        // line 36
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-1.png");
        yield "',
        '";
        // line 37
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-2.png");
        yield "',
        '";
        // line 38
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-3.png");
        yield "',
        '";
        // line 39
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-4.png");
        yield "',
      ];
      var mailpoet_congratulations_error_image = '";
        // line 41
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/error.png");
        yield "';
      var mailpoet_congratulations_loading_image = '";
        // line 42
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulation-page-illustration-transparent-LQ.20181121-1440.png");
        yield "';
      var mailpoet_emails_page = '";
        // line 43
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["mailpoet_email_page"] ?? null), "js", null, true);
        yield "';
      var mailpoet_review_request_illustration_url = '";
        // line 44
        yield $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("review-request/review-request-illustration.20190815-1427.svg");
        yield "';
      var mailpoet_installed_at = '";
        // line 45
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "installed_at", [], "any", false, false, false, 45), "js", null, true);
        yield "';
      var mailpoet_editor_javascript_url = '";
        // line 46
        yield $this->extensions['MailPoet\Twig\Assets']->getJavascriptScriptUrl("newsletter_editor.js");
        yield "';
      var mailpoet_newsletters_count = ";
        // line 47
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["newsletters_count"] ?? null), "js", null, true);
        yield ";
      var mailpoet_authorized_emails = ";
        // line 48
        yield json_encode(($context["authorized_emails"] ?? null));
        yield ";
      var mailpoet_verified_sender_domains = ";
        // line 49
        yield json_encode(($context["verified_sender_domains"] ?? null));
        yield ";
      var mailpoet_partially_verified_sender_domains = ";
        // line 50
        yield json_encode(($context["partially_verified_sender_domains"] ?? null));
        yield ";
      var mailpoet_all_sender_domains = ";
        // line 51
        yield json_encode(($context["all_sender_domains"] ?? null));
        yield ";
      var mailpoet_sender_restrictions = ";
        // line 52
        yield json_encode(($context["sender_restrictions"] ?? null));
        yield ";
    ";
        // line 54
        yield "
    var mailpoet_newsletters_templates_recently_sent_count = ";
        // line 55
        yield json_decode(($context["newsletters_templates_recently_sent_count"] ?? null));
        yield ";
    var corrupt_newsletters = ";
        // line 56
        yield json_encode(($context["corrupt_newsletters"] ?? null));
        yield ";
    var mailpoet_legacy_automatic_emails_count = ";
        // line 57
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["legacy_automatic_emails_count"] ?? null), "html", null, true);
        yield ";
    var mailpoet_legacy_automatic_emails_notice_dismissed = ";
        // line 58
        yield json_encode(($context["legacy_automatic_emails_notice_dismissed"] ?? null));
        yield ";

  </script>
";
        return; yield '';
    }

    // line 63
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 64
        yield "  ";
        yield do_action("mailpoet_newsletters_translations_after");
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletters.html";
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
        return array (  248 => 64,  244 => 63,  235 => 58,  231 => 57,  227 => 56,  223 => 55,  220 => 54,  216 => 52,  212 => 51,  208 => 50,  204 => 49,  200 => 48,  196 => 47,  192 => 46,  188 => 45,  184 => 44,  180 => 43,  176 => 42,  172 => 41,  167 => 39,  163 => 38,  159 => 37,  155 => 36,  150 => 34,  146 => 33,  142 => 32,  138 => 31,  133 => 29,  129 => 28,  124 => 26,  119 => 24,  115 => 23,  110 => 21,  106 => 20,  102 => 19,  98 => 18,  94 => 17,  90 => 16,  86 => 15,  82 => 14,  78 => 13,  74 => 12,  70 => 11,  66 => 10,  62 => 9,  57 => 8,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletters.html", "/home/circleci/mailpoet/mailpoet/views/newsletters.html");
    }
}
