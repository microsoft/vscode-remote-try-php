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

/* layout.html */
class __TwigTemplate_8581a8ede6c645c86df82d14a0f9727da4ff92aa0bb85d1970f9d5219782671e extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'templates' => [$this, 'block_templates'],
            'container' => [$this, 'block_container'],
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
            'after_translations' => [$this, 'block_after_translations'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!-- pre connect to 3d party to speed up page loading -->
<link rel=\"preconnect\" href=\"https://widget.docsbot.ai/\">
<link rel=\"dns-prefetch\" href=\"https://widget.docsbot.ai/\">
<link rel=\"preconnect\" href=\"http://cdn.mxpnl.com\">
<link rel=\"dns-prefetch\" href=\"http://cdn.mxpnl.com\">

<!-- system notices -->
<div id=\"mailpoet_notice_system\" class=\"mailpoet_notice\" style=\"display:none;\"></div>

<!-- handlebars templates -->
";
        // line 11
        $this->displayBlock('templates', $context, $blocks);
        // line 12
        echo "
<!-- main container -->
";
        // line 14
        $this->displayBlock('container', $context, $blocks);
        // line 35
        echo "
<script type=\"text/javascript\">
  var mailpoet_wp_locale = \"";
        // line 37
        echo $this->extensions['MailPoet\Twig\I18n']->getLocale();
        echo "\";
  var mailpoet_datetime_format = \"";
        // line 38
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_date_format = \"";
        // line 39
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_time_format = \"";
        // line 40
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_version = \"";
        // line 41
        echo $this->extensions['MailPoet\Twig\Functions']->getMailPoetVersion();
        echo "\";
  var mailpoet_locale = \"";
        // line 42
        echo $this->extensions['MailPoet\Twig\Functions']->getTwoLettersLocale();
        echo "\";
  var mailpoet_wp_week_starts_on = \"";
        // line 43
        echo $this->extensions['MailPoet\Twig\Functions']->getWPStartOfWeek();
        echo "\";
  var mailpoet_urls = ";
        // line 44
        echo json_encode(($context["urls"] ?? null));
        echo ";
  var mailpoet_premium_version = ";
        // line 45
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getMailPoetPremiumVersion());
        echo ";
  var mailpoet_main_page_slug =   ";
        // line 46
        echo json_encode(($context["main_page"] ?? null));
        echo ";
  var mailpoet_3rd_party_libs_enabled = ";
        // line 47
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled()), "html", null, true);
        echo ";
  var mailpoet_analytics_enabled = ";
        // line 48
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Analytics']->isEnabled()), "html", null, true);
        echo ";
  var mailpoet_analytics_public_id = ";
        // line 49
        echo json_encode($this->extensions['MailPoet\Twig\Analytics']->getPublicId());
        echo ";
  var mailpoet_analytics_new_public_id = ";
        // line 50
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Analytics']->isPublicIdNew()), "html", null, true);
        echo ";
  var mailpoet_free_domains = ";
        // line 51
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getFreeDomains());
        echo ";
  var mailpoet_woocommerce_active = ";
        // line 52
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->isWoocommerceActive());
        echo ";
  var mailpoet_woocommerce_subscriptions_active = ";
        // line 53
        echo json_encode(($context["is_woocommerce_subscriptions_active"] ?? null));
        echo ";
  var mailpoet_woocommerce_store_config = ";
        // line 54
        echo json_encode(($context["woocommerce_store_config"] ?? null));
        echo ";
  // RFC 5322 standard; http://emailregex.com/ combined with https://google.github.io/closure-library/api/goog.format.EmailAddress.html#isValid
  var mailpoet_email_regex = /(?=^[+a-zA-Z0-9_.!#\$%&'*\\/=?^`{|}~-]+@([a-zA-Z0-9-]+\\.)+[a-zA-Z0-9]{2,63}\$)(?=^(([^<>()\\[\\]\\\\.,;:\\s@\"]+(\\.[^<>()\\[\\]\\\\.,;:\\s@\"]+)*)|(\".+\"))@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}])|(([a-zA-Z\\-0-9]+\\.)+[a-zA-Z]{2,})))/;
  var mailpoet_feature_flags = ";
        // line 57
        echo json_encode(($context["feature_flags"] ?? null));
        echo ";
  var mailpoet_referral_id = ";
        // line 58
        echo json_encode(($context["referral_id"] ?? null));
        echo ";
  var mailpoet_feature_announcement_has_news = ";
        // line 59
        echo json_encode(($context["feature_announcement_has_news"] ?? null));
        echo ";
  var mailpoet_wp_segment_state = ";
        // line 60
        echo json_encode(($context["wp_segment_state"] ?? null));
        echo ";
  var mailpoet_mta_method = '";
        // line 61
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mta_method"] ?? null), "html", null, true);
        echo "';
  var mailpoet_tracking_config = ";
        // line 62
        echo json_encode(($context["tracking_config"] ?? null));
        echo ";
  var mailpoet_is_new_user = ";
        // line 63
        echo json_encode((($context["is_new_user"] ?? null) == true));
        echo ";
  var mailpoet_installed_days_ago = ";
        // line 64
        echo json_encode(($context["installed_days_ago"] ?? null));
        echo ";
  var mailpoet_send_transactional_emails = ";
        // line 65
        echo json_encode(($context["send_transactional_emails"] ?? null));
        echo ";
  var mailpoet_transactional_emails_opt_in_notice_dismissed = ";
        // line 66
        echo json_encode(($context["transactional_emails_opt_in_notice_dismissed"] ?? null));
        echo ";
  var mailpoet_deactivate_subscriber_after_inactive_days = ";
        // line 67
        echo json_encode(($context["deactivate_subscriber_after_inactive_days"] ?? null));
        echo ";
  var mailpoet_woocommerce_version = ";
        // line 68
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getWooCommerceVersion());
        echo ";
  var mailpoet_track_wizard_loaded_via_woocommerce = ";
        // line 69
        echo json_encode(($context["track_wizard_loaded_via_woocommerce"] ?? null));
        echo ";
  var mailpoet_track_wizard_loaded_via_woocommerce_marketing_dashboard = ";
        // line 70
        echo json_encode(($context["track_wizard_loaded_via_woocommerce_marketing_dashboard"] ?? null));
        echo ";
  var mailpoet_mail_function_enabled = '";
        // line 71
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mail_function_enabled"] ?? null), "html", null, true);
        echo "';
  var mailpoet_admin_plugins_url = '";
        // line 72
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["admin_plugins_url"] ?? null), "html", null, true);
        echo "';
  var mailpoet_is_dotcom = ";
        // line 73
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->isDotcom());
        echo ";

  var mailpoet_site_name = '";
        // line 75
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo "';
  var mailpoet_site_url = \"";
        // line 76
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["site_url"] ?? null), "html", null, true);
        echo "\";
  var mailpoet_site_address = '";
        // line 77
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["site_address"] ?? null), "html", null, true);
        echo "';

  // Premium status
  var mailpoet_current_wp_user_email = '";
        // line 80
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, ($context["current_wp_user_email"] ?? null), "js"), "html", null, true);
        echo "';
  var mailpoet_premium_link = ";
        // line 81
        echo json_encode(($context["link_premium"] ?? null));
        echo ";
  var mailpoet_premium_plugin_installed = ";
        // line 82
        echo json_encode(($context["premium_plugin_installed"] ?? null));
        echo ";
  var mailpoet_premium_active = ";
        // line 83
        echo json_encode(($context["premium_plugin_active"] ?? null));
        echo ";
  var mailpoet_premium_plugin_download_url = ";
        // line 84
        echo json_encode(($context["premium_plugin_download_url"] ?? null));
        echo ";
  var mailpoet_premium_plugin_activation_url = ";
        // line 85
        echo json_encode(($context["premium_plugin_activation_url"] ?? null));
        echo ";
  var mailpoet_has_valid_api_key = ";
        // line 86
        echo json_encode(($context["has_valid_api_key"] ?? null));
        echo ";
  var mailpoet_has_valid_premium_key = ";
        // line 87
        echo json_encode(($context["has_valid_premium_key"] ?? null));
        echo ";
  var mailpoet_has_premium_support = ";
        // line 88
        echo json_encode(($context["has_premium_support"] ?? null));
        echo ";
  var has_mss_key_specified = ";
        // line 89
        echo json_encode(($context["has_mss_key_specified"] ?? null));
        echo ";
  var mailpoet_mss_key_invalid = ";
        // line 90
        echo json_encode(($context["mss_key_invalid"] ?? null));
        echo ";
  var mailpoet_mss_key_valid = ";
        // line 91
        echo json_encode(($context["mss_key_valid"] ?? null));
        echo ";
  var mailpoet_mss_key_pending_approval = '";
        // line 92
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mss_key_pending_approval"] ?? null), "html", null, true);
        echo "';
  var mailpoet_mss_active = ";
        // line 93
        echo json_encode(($context["mss_active"] ?? null));
        echo ";
  var mailpoet_plugin_partial_key = '";
        // line 94
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["plugin_partial_key"] ?? null), "html", null, true);
        echo "';
  var mailpoet_subscribers_count = ";
        // line 95
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["subscriber_count"] ?? null), "html", null, true);
        echo ";
  var mailpoet_subscribers_counts_cache_created_at = ";
        // line 96
        echo json_encode(($context["subscribers_counts_cache_created_at"] ?? null));
        echo ";
  var mailpoet_subscribers_limit = ";
        // line 97
        ((($context["subscribers_limit"] ?? null)) ? (print (\MailPoetVendor\twig_escape_filter($this->env, ($context["subscribers_limit"] ?? null), "html", null, true))) : (print ("false")));
        echo ";
  var mailpoet_subscribers_limit_reached = ";
        // line 98
        echo json_encode(($context["subscribers_limit_reached"] ?? null));
        echo ";
  var mailpoet_email_volume_limit = ";
        // line 99
        echo json_encode(($context["email_volume_limit"] ?? null));
        echo ";
  var mailpoet_email_volume_limit_reached = ";
        // line 100
        echo json_encode(($context["email_volume_limit_reached"] ?? null));
        echo ";
  var mailpoet_capabilities = ";
        // line 101
        echo json_encode(($context["capabilities"] ?? null));
        echo ";
  var mailpoet_tier = ";
        // line 102
        echo json_encode(($context["tier"] ?? null));
        echo ";
  var mailpoet_cdn_url = ";
        // line 103
        echo json_encode($this->extensions['MailPoet\Twig\Assets']->generateCdnUrl(""));
        echo ";
  var mailpoet_tags = ";
        // line 104
        echo json_encode(($context["tags"] ?? null));
        echo ";

  ";
        // line 106
        if ( !($context["premium_plugin_active"] ?? null)) {
            // line 107
            echo "    var mailpoet_free_premium_subscribers_limit = ";
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["free_premium_subscribers_limit"] ?? null), "html", null, true);
            echo ";
  ";
        }
        // line 109
        echo "</script>

";
        // line 111
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["topBarLogoTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to section root"), "topBarUpdates" => $this->extensions['MailPoet\Twig\I18n']->translate("Updates"), "whatsNew" => $this->extensions['MailPoet\Twig\I18n']->translate("What’s new"), "updateMailPoetNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("[link]Update MailPoet[/link] to see the latest changes"), "ajaxFailedErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server has responded with response code %d"), "ajaxTimeoutErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server request has timed out after %d seconds"), "senderEmailAddressWarning1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You might not reach the inbox of your subscribers if you use this email address.", "In the last step, before sending a newsletter. URL: ?page=mailpoet-newsletters#/send/2"), "senderEmailAddressWarning3" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more."), "mailerSendingNotResumedUnauthorized" => $this->extensions['MailPoet\Twig\I18n']->translate("Failed to resume sending because the email address is unauthorized. Please authorize it and try again."), "dismissNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Dismiss this notice."), "confirmEdit" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending is in progress. Do you want to pause sending and edit the newsletter?"), "confirmAutomaticNewsletterEdit" => $this->extensions['MailPoet\Twig\I18n']->translate("To edit this email, it needs to be deactivated. You can activate it again after you make the changes."), "subscribersLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Action required: Upgrade your plan for more than [subscribersLimit] subscribers!"), "subscribersLimitNoticeTitleUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Action required: Upgrade your plan!"), "subscribersLimitReached" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations on reaching over [subscribersLimit] subscribers!"), "subscribersLimitReachedUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more subscribers than your plan’s limit!"), "freeVersionLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers."), "yourPlanLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Your plan is limited to [subscribersLimit] subscribers."), "youNeedToUpgrade" => $this->extensions['MailPoet\Twig\I18n']->translate("To continue using MailPoet without interruption, it’s time to upgrade your plan."), "actToSeamlessService" => $this->extensions['MailPoet\Twig\I18n']->translate("Act now to ensure seamless service to your growing audience."), "checkHowToManageSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("Alternatively, [link]check how to manage your subscribers[/link] to keep your numbers below your plan’s limit."), "upgradeNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade Now"), "refreshMySubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("Refresh subscriber limit"), "emailVolumeLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you sent more than [emailVolumeLimit] emails this month!"), "emailVolumeLimitNoticeTitleUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you sent a lot of emails this month!"), "youReachedEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("You have sent more emails this month than your MailPoet plan includes ([emailVolumeLimit]), and sending has been temporarily paused."), "youReachedEmailVolumeLimitUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("You have sent more emails this month than your MailPoet plan includes, and sending has been temporarily paused."), "toContinueUpgradeYourPlanOrWaitUntil" => $this->extensions['MailPoet\Twig\I18n']->translate("To continue sending with MailPoet Sending Service please [link]upgrade your plan[/link], or wait until sending is automatically resumed on <b>[date]</b>."), "refreshMyEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Refresh monthly email limit"), "manageSenderDomainHeaderSubtitle" => $this->extensions['MailPoet\Twig\I18n']->translate("To help your audience and MailPoet authenticate you as the domain owner, please add the following DNS records to your domain’s DNS and click “Verify the DNS records”. Please note that it may take up to 24 hours for DNS changes to propagate after you make the change. [link]Read the guide[/link].", "mailpoet"), "reviewRequestHeading" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Thank you! Time to tell the world?", "After a user gives us positive feedback via the NPS poll, we ask them to review our plugin on WordPress.org."), "reviewRequestDidYouKnow" => $this->extensions['MailPoet\Twig\I18n']->translate("[username], did you know that hundreds of WordPress users read the reviews on the plugin repository? They’re also a source of inspiration for our team."), "reviewRequestUsingForDays" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [days] day now, and we would love to read your own review.", "You’ve been using MailPoet for [days] days now, and we would love to read your own review.",         // line 147
($context["installed_days_ago"] ?? null)), "reviewRequestUsingForMonths" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [months] month now, and we would love to read your own review.", "You’ve been using MailPoet for [months] months now, and we would love to read your own review.", \MailPoetVendor\twig_round((        // line 148
($context["installed_days_ago"] ?? null) / 30))), "reviewRequestRateUsNow" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Rate us now", "Review our plugin on WordPress.org."), "reviewRequestNotNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Not now"), "sent" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent"), "notSentYet" => $this->extensions['MailPoet\Twig\I18n']->translate("Not sent yet!"), "renderingProblem" => $this->extensions['MailPoet\Twig\I18n']->translate("There was a problem with rendering!", "mailpoet"), "allSendingPausedHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("All sending is currently paused!"), "allSendingPausedBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your [link]API key[/link] to send with MailPoet is invalid."), "allSendingPausedLink" => $this->extensions['MailPoet\Twig\I18n']->translate("Purchase a key"), "transactionalEmailNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Good news! MailPoet can now send your website’s emails too"), "transactionalEmailNoticeBody" => $this->extensions['MailPoet\Twig\I18n']->translate("All of your WordPress and WooCommerce emails are sent with your hosting company, unless you have an SMTP plugin. Would you like such emails to be delivered with MailPoet’s active sending method for better deliverability?"), "transactionalEmailNoticeBodyReadMore" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more.", "This is a link that leads to more information about transactional emails"), "transactionalEmailNoticeCTA" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Enable", "Button, after clicking it we will enable transactional emails"), "mailerSendErrorNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending has been paused due to a technical issue with %1\$s"), "mailerSendErrorCheckConfiguration" => $this->extensions['MailPoet\Twig\I18n']->translate("Please check your sending method configuration, you may need to consult with your hosting company."), "mailerSendErrorUseSendingService" => $this->extensions['MailPoet\Twig\I18n']->translate("The easy alternative is to <b>send emails with MailPoet Sending Service</b> instead, like thousands of other users do."), "mailerSendErrorSignUpForSendingService" => $this->extensions['MailPoet\Twig\I18n']->translate("Sign up for free in minutes"), "mailerConnectionErrorNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending is paused because the following connection issue prevents MailPoet from delivering emails"), "mailerErrorCode" => $this->extensions['MailPoet\Twig\I18n']->translate("Error code: %1\$s"), "mailerCheckSettingsNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Check your [link]sending method settings[/link]."), "mailerResumeSendingButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Resume sending"), "mailerResumeSendingAfterUpgradeButton" => $this->extensions['MailPoet\Twig\I18n']->translate("I have upgraded my subscription, resume sending"), "topBarLogoTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to section root"), "close" => $this->extensions['MailPoet\Twig\I18n']->translate("Close"), "today" => $this->extensions['MailPoet\Twig\I18n']->translate("Today")]);
        // line 179
        echo "

";
        // line 181
        $this->displayBlock('after_translations', $context, $blocks);
        // line 182
        echo "
";
        // line 183
        if ($this->extensions['MailPoet\Twig\Analytics']->isEnabled()) {
            // line 184
            echo "  ";
            $this->loadTemplate("analytics.html", "layout.html", 184)->display($context);
        }
        // line 186
        echo "
";
        // line 187
        if ((($context["display_docsbot_widget"] ?? null) &&  !$this->extensions['MailPoet\Twig\Functions']->isDotcomEcommercePlan())) {
            // line 188
            echo "  <script type=\"text/javascript\">window.DocsBotAI=window.DocsBotAI||{},DocsBotAI.init=function(c){return new Promise(function(e,o){var t=document.createElement(\"script\");t.type=\"text/javascript\",t.async=!0,t.src=\"https://widget.docsbot.ai/chat.js\";var n=document.getElementsByTagName(\"script\")[0];n.parentNode.insertBefore(t,n),t.addEventListener(\"load\",function(){window.DocsBotAI.mount({id:c.id,supportCallback:c.supportCallback,identify:c.identify,options:c.options});var t;t=function(n){return new Promise(function(e){if(document.querySelector(n))return e(document.querySelector(n));var o=new MutationObserver(function(t){document.querySelector(n)&&(e(document.querySelector(n)),o.disconnect())});o.observe(document.body,{childList:!0,subtree:!0})})},t&&t(\"#docsbotai-root\").then(e).catch(o)}),t.addEventListener(\"error\",function(t){o(t.message)})})};</script>
  <script type=\"text/javascript\">
    DocsBotAI.init({
      id: \"TqTdebbGjJeUjrmBIFjh/kzFE5FBebBJiSJ2Tm0nR\",
      // We want to redirect users to the proper page depending on their plan
      supportCallback: function (event, history) {
        ";
            // line 194
            if (($context["has_premium_support"] ?? null)) {
                // line 195
                echo "          const mailpoet_redirect_support_link = 'https://www.mailpoet.com/support/premium/';
        ";
            } else {
                // line 197
                echo "          const mailpoet_redirect_support_link = 'https://wordpress.org/support/plugin/mailpoet/';
        ";
            }
            // line 199
            echo "        event.preventDefault(); // Prevent default behavior opening the url.
        window.open(mailpoet_redirect_support_link, '_blank');
      },
    }).then(() => {
        // stopping propagation to avoid conflicts with other keydown events form WP.com
      function writingFixer (e) {
        e.stopPropagation();
      }
      document.getElementById('docsbotai-root').addEventListener(\"keydown\", writingFixer);
    })
  </script>
";
        }
        // line 211
        echo "
<div id=\"mailpoet-modal\"></div>
";
    }

    // line 11
    public function block_templates($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 14
    public function block_container($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 15
        echo "<div class=\"wrap\">
  <div class=\"wp-header-end\"></div>
  <!-- notices -->
  <div id=\"mailpoet_notice_error\" class=\"mailpoet_notice\" style=\"display:none;\"></div>
  <div id=\"mailpoet_notice_success\" class=\"mailpoet_notice\" style=\"display:none;\"></div>
  <!-- React notices -->
  <div id=\"mailpoet_notices\"></div>

  <!-- Set FROM address modal React root -->
  <div id=\"mailpoet_set_from_address_modal\"></div>

  <!-- Set Authorize sender email React root -->
  <div id=\"mailpoet_authorize_sender_email_modal\"></div>

  <!-- title block -->
  ";
        // line 30
        $this->displayBlock('title', $context, $blocks);
        // line 31
        echo "  <!-- content block -->
  ";
        // line 32
        $this->displayBlock('content', $context, $blocks);
        // line 33
        echo "</div>
";
    }

    // line 30
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 32
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 181
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "layout.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  449 => 181,  443 => 32,  437 => 30,  432 => 33,  430 => 32,  427 => 31,  425 => 30,  408 => 15,  404 => 14,  398 => 11,  392 => 211,  378 => 199,  374 => 197,  370 => 195,  368 => 194,  360 => 188,  358 => 187,  355 => 186,  351 => 184,  349 => 183,  346 => 182,  344 => 181,  340 => 179,  338 => 148,  337 => 147,  336 => 111,  332 => 109,  326 => 107,  324 => 106,  319 => 104,  315 => 103,  311 => 102,  307 => 101,  303 => 100,  299 => 99,  295 => 98,  291 => 97,  287 => 96,  283 => 95,  279 => 94,  275 => 93,  271 => 92,  267 => 91,  263 => 90,  259 => 89,  255 => 88,  251 => 87,  247 => 86,  243 => 85,  239 => 84,  235 => 83,  231 => 82,  227 => 81,  223 => 80,  217 => 77,  213 => 76,  209 => 75,  204 => 73,  200 => 72,  196 => 71,  192 => 70,  188 => 69,  184 => 68,  180 => 67,  176 => 66,  172 => 65,  168 => 64,  164 => 63,  160 => 62,  156 => 61,  152 => 60,  148 => 59,  144 => 58,  140 => 57,  134 => 54,  130 => 53,  126 => 52,  122 => 51,  118 => 50,  114 => 49,  110 => 48,  106 => 47,  102 => 46,  98 => 45,  94 => 44,  90 => 43,  86 => 42,  82 => 41,  78 => 40,  74 => 39,  70 => 38,  66 => 37,  62 => 35,  60 => 14,  56 => 12,  54 => 11,  42 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "layout.html", "/home/circleci/mailpoet/mailpoet/views/layout.html");
    }
}
