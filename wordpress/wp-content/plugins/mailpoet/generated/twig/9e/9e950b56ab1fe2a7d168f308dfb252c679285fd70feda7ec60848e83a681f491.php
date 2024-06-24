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

/* homepage.html */
class __TwigTemplate_e93f1843f587ff72b997c53dfebf2ffe3fe3c73139e95193f6816aeb1e21eb2c extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "homepage.html", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "<div id=\"mailpoet_homepage_container\">
</div>

<script>
  ";
        // line 9
        yield "    var mailpoet_mta_log = ";
        yield json_encode(($context["mta_log"] ?? null));
        yield ";
    var mailpoet_homepage_data = ";
        // line 10
        yield json_encode(($context["homepage"] ?? null));
        yield ";
  ";
        // line 12
        yield "</script>

";
        // line 14
        yield $this->extensions['MailPoet\Twig\I18n']->localize(["welcomeToMailPoet" => $this->extensions['MailPoet\Twig\I18n']->translate("Welcome to MailPoet 🎉"), "beginByCompletingSetup" => $this->extensions['MailPoet\Twig\I18n']->translate("Begin by completing your setup"), "hideList" => $this->extensions['MailPoet\Twig\I18n']->translate("Hide setup list"), "senderSetTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Add your sender information", "Link in a task list"), "senderSetTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sender information added!", "Link in a task list"), "mssConnectedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Connect MailPoet Sending Service", "Link in a task list"), "mssConnectedTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("MailPoet Sending Service activated!", "Link in a task list"), "wooSubscribersImportedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import WooCommerce subscribers", "Link in a task list"), "wooSubscribersImportedTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("WooCommerce subscribers imported!", "Link in a task list"), "subscribersAddedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import existing subscribers", "Link in a task list"), "subscribersAddedTaskDoneByImport" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscribers imported!", "Link in a task list"), "subscribersAddedTaskDoneByForm" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscription form created!", "Link in a task list"), "senderDomainAuthenticatedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Authenticate your sender domain", "Link in a task list"), "senderDomainAuthenticatedTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sender domain authenticated!", "Link in a task list"), "improveDeliveryRates" => $this->extensions['MailPoet\Twig\I18n']->translate("Improve email delivery rates"), "youAreSet" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You’re all set!", "Part of string \"You’re all set! Dismiss setup list\""), "dismissList" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Dismiss setup list", "Part of string \"You’re all set! Dismiss setup list\""), "noSubscribersQuestion" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("No subscribers yet?", "Part of string \"No subscribers yet? Set up a subscription form\""), "setUpForm" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Set up a subscription form", "Part of string \"No subscribers yet? Set up a subscription form\""), "haveSubscribersQuestion" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Have existing subscribers?", "Part of string \"Have existing subscribers? Import\""), "import" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import", "Part of string \"Have existing subscribers? Import\""), "startEngagingWithYourCustomers" => $this->extensions['MailPoet\Twig\I18n']->translate("Start engaging with your customers"), "setUpWelcomeCampaign" => $this->extensions['MailPoet\Twig\I18n']->translate("Set up a welcome campaign"), "setUpWelcomeCampaignDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Choose from our list of templates or start with a blank editor"), "setUpWelcomeCampaignDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You started a welcome campaign"), "addSubscriptionForm" => $this->extensions['MailPoet\Twig\I18n']->translate("Add a subscription form"), "addSubscriptionFormDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Collect new contacts from signup forms"), "addSubscriptionFormDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You added a subscription form"), "sendFirstNewsletter" => $this->extensions['MailPoet\Twig\I18n']->translate("Send your first newsletter"), "sendFirstNewsletterDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Drive traffic with newsletters that are easy to build and measure"), "sendFirstNewsletterDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You sent or scheduled your first newsletter"), "setUpAbandonedCartEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("Set up an abandoned cart email"), "setUpAbandonedCartEmailDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Create customized email sequences with our new automation editor"), "setUpAbandonedCartEmailDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You set up an abandoned cart campaign"), "brandWooEmails" => $this->extensions['MailPoet\Twig\I18n']->translate("Brand your WooCommerce transactional emails"), "brandWooEmailsDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Make your store notification emails shine"), "brandWooEmailsDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You branded your transactional emails"), "allDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("All done!", "Part of string \"All done! Dismiss tasks\""), "dismissTasks" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Dismiss tasks", "Part of string \"All done! Dismiss tasks\""), "accelerateYourGrowth" => $this->extensions['MailPoet\Twig\I18n']->translate("Accelerate your growth with our Business plan"), "detailedAnalytics" => $this->extensions['MailPoet\Twig\I18n']->translate("Detailed analytics"), "advancedSubscriberSegmentation" => $this->extensions['MailPoet\Twig\I18n']->translate("Advanced subscriber segmentation"), "emailMarketingAutomations" => $this->extensions['MailPoet\Twig\I18n']->translate("Email marketing automations"), "prioritySupport" => $this->extensions['MailPoet\Twig\I18n']->translate("Priority support"), "upgradePlan" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade plan"), "close" => $this->extensions['MailPoet\Twig\I18n']->translate("Close"), "subscribersHeading" => $this->extensions['MailPoet\Twig\I18n']->translate("Subscribers"), "subscribersSectionDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Changes in the last 30 days"), "newSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("new"), "unsubscribedSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("unsubscribed", "Label for a number of subscribers who unsubscribed from email delivery"), "subscribedSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("subscribed", "Label for a number of subscribers who subscribed to email delivery"), "listName" => $this->extensions['MailPoet\Twig\I18n']->translate("List name"), "listScore" => $this->extensions['MailPoet\Twig\I18n']->translate("List score"), "unknownBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Unknown"), "unknownBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Not enough data."), "tooltipUnknown" => $this->extensions['MailPoet\Twig\I18n']->translate("Fewer than 3 emails sent"), "excellentBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Excellent"), "excellentBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Congrats!"), "tooltipExcellent" => $this->extensions['MailPoet\Twig\I18n']->translate("50% or more"), "goodBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Good"), "goodBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Good stuff."), "tooltipGood" => $this->extensions['MailPoet\Twig\I18n']->translate("between 20 and 50%"), "averageBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Low"), "averageBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Something to improve."), "tooltipAverage" => $this->extensions['MailPoet\Twig\I18n']->translate("20% or fewer"), "engagementScoreDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Average percent of emails subscribers read in the last year"), "changesWillAppear" => $this->extensions['MailPoet\Twig\I18n']->translate("Changes to your audience will appear here."), "starBySettingUpForm" => $this->extensions['MailPoet\Twig\I18n']->translate("Start by setting up a subscription form."), "subscriberCountHasNotChangeLongTime" => $this->extensions['MailPoet\Twig\I18n']->translate("Your subscriber count hasn’t changed in the last 30 days. Launch a new campaign to grow your audience."), "createForm" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Create a subscription form", "Cta link title"), "exploreCampaigns" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Explore campaigns", "Cta link title"), "learnMoreAboutEmailMarketing" => $this->extensions['MailPoet\Twig\I18n']->translate("Learn more about email marketing"), "createAnEmailTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Create an Email: Types of Campaigns"), "createAnEmailAbstract" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet allows you to create five different types of campaigns: Newsletter, Welcome Email, Latest Post Notifications, Re-engagement Emails, and WooCommerce behavior-based emails. Each one has a different purpose and you can read more about it in this article."), "createAFormTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Create a Subscription Form"), "createAFormAbstract" => $this->extensions['MailPoet\Twig\I18n']->translate("You can create sign-up forms with the MailPoet form editor, to gather new subscribers for your email campaign."), "segmentationGuideTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("The beginner’s guide to segmentation"), "segmentationGuideAbstract" => $this->extensions['MailPoet\Twig\I18n']->translate("Do you still have just one big email list for all your subscribers? If so, you’re probably not getting the best performance. The good news is that email segmentation is a relatively straightforward way to enhance what you’re already doing."), "reEngagementTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Re-Engagement Emails: Examples and Best Practices to Win Back Subscribers"), "reEngagementAbstract" => $this->extensions['MailPoet\Twig\I18n']->translate("Beyond people unsubscribing, you might also have people who stop engaging with your emails. By sending re-engagement emails, you have a chance to win back those unengaged subscribers and get them to start opening and clicking your emails again."), "marketingStrategyTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Why You Need a Newsletter Marketing Strategy + How to Make One"), "marketingStrategyAbstract" => $this->extensions['MailPoet\Twig\I18n']->translate("Your email list is only as valuable as the number of engaged subscribers on it. This makes it essential to create an effective long-term newsletter marketing strategy."), "promotingSalesTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("How to promote your sales with email marketing and segmentation"), "promotingSalesAbstract" => $this->extensions['MailPoet\Twig\I18n']->translate("Planning a sale? Discounts are great, but when you’re competing against millions of other online stores, \"build it and they will come\" isn’t a winning strategy. Instead, you can ensure the success of your sale by learning how to use email marketing to promote it."), "pageOf" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Page %1\$d of %2\$d", "Page 1 of 3"), "previousPostsPage" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Next page with posts", "Title of a link to a previous page of posts"), "nextPostsPage" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Previous page with posts", "Title of a link to a next page of posts")]);
        // line 106
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "homepage.html";
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
        return array (  72 => 106,  70 => 14,  66 => 12,  62 => 10,  57 => 9,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "homepage.html", "/home/circleci/mailpoet/mailpoet/views/homepage.html");
    }
}
