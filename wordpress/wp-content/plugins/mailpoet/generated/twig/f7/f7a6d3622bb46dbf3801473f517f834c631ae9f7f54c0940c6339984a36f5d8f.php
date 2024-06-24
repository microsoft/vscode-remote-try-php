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

/* mss_pitch_translations.html */
class __TwigTemplate_817e35bc9901e106cb80d1f854e91e7d5bc9a9be36014be5fa1cd05f1e39249e extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->localize(["welcomeWizardMSSFirstPartTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Connect your MailPoet account", "Promotion for our email sending service: Title"), "welcomeWizardMSSFirstPartSubtitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("To start sending emails, create and connect a MailPoet account with your site. With a MailPoet account, you’ll get:", "Promotion for our email sending service: Paragraph"), "welcomeWizardMSSList1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Emails that reach inboxes, not spam boxes", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSList2" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Less hassle setting up, as we would manage all the configurations", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSList3Free" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Start sending for free and scale as you grow", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSList3Paid" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Super fast: send up to 50,000 emails per hour", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSFirstPartButton" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Connect MailPoet", "Promotion for our email sending service: Button"), "welcomeWizardMSSAdvancedUsers" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("(For advanced users) You can choose to use MailPoet with your own email delivery service. [link]I’ll set up my own email service[/link].", "Promotion for our email sending service: message for users about not using MSS"), "welcomeWizardMSSConfirmationModalTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Confirm sending service selection", "Promotion for our email sending service: title of the modal where the user confirms that they want to use their own sending service"), "welcomeWizardMSSConfirmationModalFirstParagraph" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Are you sure that you would like to continue using your own email delivery service? This would require configuring your web host or a third-party email delivery service to work with the MailPoet plugin.", "Promotion for our email sending service: first paragraph of the modal where the user confirms that they want to use their own sending service"), "welcomeWizardMSSConfirmationModalFirstParagraphWithoutMailFunction" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Are you sure that you would like to continue using your own email delivery service? This would require configuring a third-party email delivery service to work with the MailPoet plugin.", "Promotion for our email sending service: first paragraph of the modal where the user confirms that they want to use their own sending service"), "welcomeWizardMSSConfirmationModalSecondParagraph" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("We do not recommend this option if you are unfamiliar with setting up your own email service.", "Promotion for our email sending service: second paragraph of the modal where the user confirms that they want to use their own sending service"), "welcomeWizardMSSConfirmationModalGoBackButton" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Go back", "Promotion for our email sending service: go back button"), "welcomeWizardMSSConfirmationModalOkButton" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Yes, I’ll use my own service", "Promotion for our email sending service: confirm button"), "welcomeWizardMSSSecondPartTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Activate your MailPoet account", "Promotion for our email sending service: title of the second part"), "welcomeWizardMSSSecondPartEnterKey" => $this->extensions['MailPoet\Twig\I18n']->translate("Enter your activation key to validate your account."), "welcomeWizardMSSSecondPartNoAccount" => $this->extensions['MailPoet\Twig\I18n']->translate("Don’t have one yet? [link]Sign up for a MailPoet plan[/link]."), "welcomeWizardMSSSecondPartInputLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Activation key"), "welcomeWizardMSSSecondPartInputPlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("Enter your key here"), "welcomeWizardMSSSecondPartButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Verify account"), "welcomeWizardMSSThirdPartTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("MailPoet account connected", "Promotion for our email sending service: title of the third part"), "welcomeWizardMSSThirdPartFirstParagraph" => $this->extensions['MailPoet\Twig\I18n']->translate("You have successfully connected your MailPoet account."), "welcomeWizardMSSThirdPartSecondParagraph" => $this->extensions['MailPoet\Twig\I18n']->translate("Start using MailPoet to send beautiful emails that reach inboxes every time, and create loyal subscribers."), "welcomeWizardMSSThirdPartButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Start using MailPoet")]);
        // line 26
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "mss_pitch_translations.html";
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
        return array (  40 => 26,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "mss_pitch_translations.html", "/home/circleci/mailpoet/mailpoet/views/mss_pitch_translations.html");
    }
}
