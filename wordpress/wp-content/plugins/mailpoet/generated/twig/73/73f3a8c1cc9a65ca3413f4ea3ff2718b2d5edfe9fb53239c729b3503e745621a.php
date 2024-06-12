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
        echo "<!DOCTYPE html>
<!--[if IE 7]>
<html class=\"ie ie7\" ";
        // line 3
        echo ($context["language_attributes"] ?? null);
        echo ">
<![endif]-->
<!--[if IE 8]>
<html class=\"ie ie8\" ";
        // line 6
        echo ($context["language_attributes"] ?? null);
        echo ">
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html ";
        // line 9
        echo ($context["language_attributes"] ?? null);
        echo ">
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Subscription Form");
        echo "</title>
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
        echo "    ";
        echo $this->extensions['MailPoet\Twig\Filters']->wpKses(($context["fonts_link"] ?? null), ($context["allowedHtml"] ?? null));
        echo "
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        // line 66
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mailpoet_public_css_url"] ?? null), "html", null, true);
        echo "\" />
    ";
        // line 67
        echo ($context["scripts"] ?? null);
        echo "
  </head>
  <body>
    ";
        // line 70
        echo ($context["form"] ?? null);
        echo "
    <script type=\"text/javascript\">
      var MailPoetForm = ";
        // line 72
        echo json_encode(($context["mailpoet_form"] ?? null));
        echo ";
    </script>
  </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "form/iframe.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  136 => 72,  131 => 70,  125 => 67,  121 => 66,  116 => 65,  114 => 64,  93 => 46,  53 => 9,  47 => 6,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/iframe.html", "/home/circleci/mailpoet/mailpoet/views/form/iframe.html");
    }
}
