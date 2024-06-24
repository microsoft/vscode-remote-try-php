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

/* form/iframe.html */
class __TwigTemplate_9b0ddf3688f2dc81e331ee62fce3c53bfc8110f9d6615c3cf10d5c5c68f6704b extends Template
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
        yield "<!DOCTYPE html>
<!--[if IE 7]>
<html class=\"ie ie7\" ";
        // line 3
        yield ($context["language_attributes"] ?? null);
        yield ">
<![endif]-->
<!--[if IE 8]>
<html class=\"ie ie8\" ";
        // line 6
        yield ($context["language_attributes"] ?? null);
        yield ">
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html ";
        // line 9
        yield ($context["language_attributes"] ?? null);
        yield ">
<script>
  var additionalHeight = 10,
    attemptsToAdjust = 3,
    delayToAdjust = 400,
    intervalHandle;

  var sendSize = function() {
    if(!attemptsToAdjust && intervalHandle !== undefined) {
      window.clearInterval(intervalHandle)
      return;
    }
    window.top.postMessage({MailPoetIframeHeight: document.body.scrollHeight + additionalHeight + 'px'}, '*');
    attemptsToAdjust--;
  };

  window.addEventListener('load', function () {
    sendSize();
    if(!window.MutationObserver) {
      intervalHandle = setInterval(sendSize, delayToAdjust);
      return;
    }

    var observer = new MutationObserver(sendSize);

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  });
</script>

<!--<![endif]-->
  <head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width\">
    <meta name=\"robots\" content=\"noindex, nofollow\">
    <title>";
        // line 46
        yield $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Subscription Form");
        yield "</title>
    <style>
      html {
        box-sizing: border-box;
      }
      *,
      *:before,
      *:after {
        box-sizing: inherit;
      }
      body {
        color: rgb(40, 48, 61);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 16px;
        font-weight: normal;
        text-align: left;
      }
    </style>
    ";
        // line 64
        $context["allowedHtml"] = ["link" => ["href" => [], "rel" => []]];
        // line 65
        yield "    ";
        yield $this->extensions['MailPoet\Twig\Filters']->wpKses(($context["fonts_link"] ?? null), ($context["allowedHtml"] ?? null));
        yield "
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        // line 66
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape(($context["mailpoet_public_css_url"] ?? null), "html", null, true);
        yield "\" />
    ";
        // line 67
        yield ($context["scripts"] ?? null);
        yield "
  </head>
  <body>
    ";
        // line 70
        yield ($context["form"] ?? null);
        yield "
    <script type=\"text/javascript\">
      var MailPoetForm = ";
        // line 72
        yield json_encode(($context["mailpoet_form"] ?? null));
        yield ";
    </script>
  </body>
</html>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/iframe.html";
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
        return array (  137 => 72,  132 => 70,  126 => 67,  122 => 66,  117 => 65,  115 => 64,  94 => 46,  54 => 9,  48 => 6,  42 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/iframe.html", "/home/circleci/mailpoet/mailpoet/views/form/iframe.html");
    }
}
