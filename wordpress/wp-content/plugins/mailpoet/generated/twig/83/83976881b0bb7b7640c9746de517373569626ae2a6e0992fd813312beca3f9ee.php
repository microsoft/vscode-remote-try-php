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

/* upgrade/upgrade_button.html */
class __TwigTemplate_b67c32b7be3df4a420334077f7e14c432f62c4aab97a732513f3dcffc5dfc251 extends Template
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
        if (($context["has_valid_api_key"] ?? null)) {
            // line 2
            echo "  ";
            if ((($context["tier"] ?? null) === ($context["current_mailpoet_plan_tier"] ?? null))) {
                // line 3
                echo "    <button class=\"components-button\" disabled>
      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"17\" height=\"16\" viewBox=\"0 0 17 16\" fill=\"none\">
        <path d=\"M11.885 5.02048L7.38449 11.0731L4.77484 9.13272\" stroke=\"#949494\" stroke-width=\"1.5\"/>
      </svg>
      ";
                // line 7
                echo $this->extensions['MailPoet\Twig\I18n']->translateEscHTML("Your current plan");
                echo "
    </button>
  ";
            } else {
                // line 10
                echo "    ";
                if ((($context["tier"] ?? null) < ($context["current_mailpoet_plan_tier"] ?? null))) {
                    // line 11
                    echo "      <div class=\"mailpoet-tiers-page-price-divider\">
        <hr>
      </div>
    ";
                } else {
                    // line 15
                    echo "      <a
        target=\"_blank\"
        href=\"";
                    // line 17
                    echo \MailPoetVendor\twig_escape_filter($this->env, (("https://account.mailpoet.com/orders/upgrade/" . ($context["plugin_partial_key"] ?? null)) . "?2024=1&utm_source=plugin&utm_medium=premium&utm_campaign=upgrade&ref=plugin-upgrade-page"), "html", null, true);
                    echo "\"
        class=\"components-button ";
                    // line 18
                    echo \MailPoetVendor\twig_escape_filter($this->env, ($context["button_class"] ?? null), "html", null, true);
                    echo " mailpoet-premium-shop-link\"
      >
        ";
                    // line 20
                    echo \MailPoetVendor\twig_escape_filter($this->env, ($context["label"] ?? null), "html", null, true);
                    echo "
      </a>
    ";
                }
                // line 23
                echo "  ";
            }
        } else {
            // line 25
            echo "  <a
    target=\"_blank\"
    href=\"";
            // line 27
            echo \MailPoetVendor\twig_escape_filter($this->env, (((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&email=") . ($context["current_wp_user_email"] ?? null)) . "&g=") . ($context["group"] ?? null)) . "&2024=1&billing=monthly&utm_source=plugin&utm_medium=premium&utm_campaign=purchase&ref=plugin-upgrade-page"), "html", null, true);
            echo "\"
    class=\"components-button ";
            // line 28
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["button_class"] ?? null), "html", null, true);
            echo " mailpoet-premium-shop-link\"
  >
  ";
            // line 30
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["label"] ?? null), "html", null, true);
            echo "
  </a>
";
        }
    }

    public function getTemplateName()
    {
        return "upgrade/upgrade_button.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  99 => 30,  94 => 28,  90 => 27,  86 => 25,  82 => 23,  76 => 20,  71 => 18,  67 => 17,  63 => 15,  57 => 11,  54 => 10,  48 => 7,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "upgrade/upgrade_button.html", "/home/circleci/mailpoet/mailpoet/views/upgrade/upgrade_button.html");
    }
}
