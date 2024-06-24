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

/* deactivationPoll/link-poll.html */
class __TwigTemplate_24e96a0e6a17ce2c93cf643ff67585ab8fe18e1a7ba8b35cfd7b474c10128b6c extends Template
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
        yield "<div class=\"mailpoet-deactivate-survey-modal\" id=\"mailpoet-deactivate-survey-modal\">
  <div class=\"mailpoet-deactivate-survey-wrap\">
    <div class=\"mailpoet-deactivate-survey\">

      <script type=\"text/javascript\">
        window.addEventListener('load', function() {
          var deactivateLink = document.querySelector('#the-list [data-slug=\"mailpoet\"] span.deactivate a');
          var overlay = document.querySelector('#mailpoet-deactivate-survey-modal');
          var closeButton = document.querySelector('#mailpoet-deactivate-survey-close');
          var participateButton = document.querySelector('#mailpoet-deactivate-survey-participate');
          var formOpen = false;

          if (!deactivateLink || !overlay || !closeButton || !participateButton) {
            return;
          }

          deactivateLink.addEventListener('click', function (event) {
            event.preventDefault();
            overlay.style.display = 'table';
            formOpen = true;
          });

          closeButton.addEventListener('click', function (event) {
            event.preventDefault();
            overlay.style.display = 'none';
            formOpen = false;
            location.href = deactivateLink.getAttribute('href');
          });

          participateButton.addEventListener('click', function (event) {
                setTimeout(function() {closeButton.click(); }, 50);
          });

          document.addEventListener('keyup', function (event) {
            if ((event.keyCode === 27) && formOpen) {
              location.href = deactivateLink.getAttribute('href');
            }
          });
        });
      </script>

      <p><strong>

        ";
        // line 44
        yield $this->extensions['MailPoet\Twig\I18n']->translate("We're sorry to see you go. Would you be open to sharing how MailPoet didn't work for you so we could improve it?");
        yield "
      </strong><br>";
        // line 45
        yield $this->extensions['MailPoet\Twig\I18n']->translate("It will take only a minute.");
        yield "</p>
      <a
        class=\"button button-primary\"
        id=\"mailpoet-deactivate-survey-participate\"
        href=\"https://poll.fm/11161195\"
        target=\"_blank\"
        rel=\"noopener nofollow\"
      >";
        // line 52
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield " &rarr;</a>

      <a
        class=\"button\"
        id=\"mailpoet-deactivate-survey-close\"
      >
        ";
        // line 58
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
      </a>
    </div>
  </div>
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "deactivationPoll/link-poll.html";
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
        return array (  106 => 58,  97 => 52,  87 => 45,  83 => 44,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "deactivationPoll/link-poll.html", "/home/circleci/mailpoet/mailpoet/views/deactivationPoll/link-poll.html");
    }
}
