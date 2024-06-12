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

/* form/front_end_form.html */
class __TwigTemplate_53b89d2127c3e3553aef87a0684de61c60938709506258cd8a7cd55ed9684156 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $this->displayBlock('content', $context, $blocks);
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        echo "  ";
        if (($context["before_widget"] ?? null)) {
            // line 3
            echo "    ";
            echo ($context["before_widget"] ?? null);
            echo "
  ";
        }
        // line 5
        echo "
  ";
        // line 6
        if (($context["title"] ?? null)) {
            // line 7
            echo "    ";
            echo ($context["before_title"] ?? null);
            echo ($context["title"] ?? null);
            echo ($context["after_title"] ?? null);
            echo "
  ";
        }
        // line 9
        echo "
  <div class=\"
    mailpoet_form_popup_overlay
    ";
        // line 12
        if ((($context["animation"] ?? null) != "")) {
            // line 13
            echo "      mailpoet_form_overlay_animation_";
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["animation"] ?? null), "html", null, true);
            echo "
      mailpoet_form_overlay_animation
    ";
        }
        // line 16
        echo "  \"></div>
  <div
    id=\"";
        // line 18
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_html_id"] ?? null), "html", null, true);
        echo "\"
    class=\"
      mailpoet_form
      mailpoet_form_";
        // line 21
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_type"] ?? null), "html", null, true);
        echo "
      mailpoet_form_position_";
        // line 22
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["position"] ?? null), "html", null, true);
        echo "
      mailpoet_form_animation_";
        // line 23
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["animation"] ?? null), "html", null, true);
        echo "
    \"
    ";
        // line 25
        if (($context["is_preview"] ?? null)) {
            // line 26
            echo "      data-is-preview=\"1\"
      data-editor-url=\"";
            // line 27
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["editor_url"] ?? null), "html", null, true);
            echo "\"
    ";
        }
        // line 29
        echo "  >

    <style type=\"text/css\">
     ";
        // line 32
        echo ($context["styles"] ?? null);
        echo "
    </style>

    <form
      target=\"_self\"
      method=\"post\"
      action=\"";
        // line 38
        echo admin_url("admin-post.php?action=mailpoet_subscription_form");
        echo "\"
      class=\"mailpoet_form mailpoet_form_form mailpoet_form_";
        // line 39
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_type"] ?? null), "html", null, true);
        echo "\"
      novalidate
      data-delay=\"";
        // line 41
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["delay"] ?? null), "html", null, true);
        echo "\"
      data-exit-intent-enabled=\"";
        // line 42
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["enableExitIntent"] ?? null), "html", null, true);
        echo "\"
      data-font-family=\"";
        // line 43
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["fontFamily"] ?? null), "html", null, true);
        echo "\"
      data-cookie-expiration-time=\"";
        // line 44
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["cookieFormExpirationTime"] ?? null), "html", null, true);
        echo "\"
    >
      <input type=\"hidden\" name=\"data[form_id]\" value=\"";
        // line 46
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_id"] ?? null), "html", null, true);
        echo "\" />
      <input type=\"hidden\" name=\"token\" value=\"";
        // line 47
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["token"] ?? null), "html", null, true);
        echo "\" />
      <input type=\"hidden\" name=\"api_version\" value=\"";
        // line 48
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["api_version"] ?? null), "html", null, true);
        echo "\" />
      <input type=\"hidden\" name=\"endpoint\" value=\"subscribers\" />
      <input type=\"hidden\" name=\"mailpoet_method\" value=\"subscribe\" />

      ";
        // line 52
        echo ($context["html"] ?? null);
        echo "
      <div class=\"mailpoet_message\">
        <p class=\"mailpoet_validate_success\"
        ";
        // line 55
        if ( !($context["success"] ?? null)) {
            // line 56
            echo "        style=\"display:none;\"
        ";
        }
        // line 58
        echo "        >";
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_success_message"] ?? null), "html", null, true);
        echo "
        </p>
        <p class=\"mailpoet_validate_error\"
        ";
        // line 61
        if ( !($context["error"] ?? null)) {
            // line 62
            echo "        style=\"display:none;\"
        ";
        }
        // line 64
        echo "        >";
        if (($context["error"] ?? null)) {
            // line 65
            echo "        ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("An error occurred, make sure you have filled all the required fields.");
            echo "
        ";
        }
        // line 67
        echo "        </p>
      </div>
    </form>

    ";
        // line 71
        if ((((($context["form_type"] ?? null) == "popup") || (($context["form_type"] ?? null) == "fixed_bar")) || (($context["form_type"] ?? null) == "slide_in"))) {
            // line 72
            echo "      <input type=\"image\"
        class=\"mailpoet_form_close_icon\"
        alt=\"";
            // line 74
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Close");
            echo "\"
        src='";
            // line 75
            echo $this->extensions['MailPoet\Twig\Assets']->generateImageUrl((("form_close_icon/" . ($context["close_button_icon"] ?? null)) . ".svg"));
            echo "'
      />
    ";
        }
        // line 78
        echo "  </div>

  ";
        // line 80
        if (($context["after_widget"] ?? null)) {
            // line 81
            echo "    ";
            echo ($context["after_widget"] ?? null);
            echo "
  ";
        }
    }

    public function getTemplateName()
    {
        return "form/front_end_form.html";
    }

    public function getDebugInfo()
    {
        return array (  231 => 81,  229 => 80,  225 => 78,  219 => 75,  215 => 74,  211 => 72,  209 => 71,  203 => 67,  197 => 65,  194 => 64,  190 => 62,  188 => 61,  181 => 58,  177 => 56,  175 => 55,  169 => 52,  162 => 48,  158 => 47,  154 => 46,  149 => 44,  145 => 43,  141 => 42,  137 => 41,  132 => 39,  128 => 38,  119 => 32,  114 => 29,  109 => 27,  106 => 26,  104 => 25,  99 => 23,  95 => 22,  91 => 21,  85 => 18,  81 => 16,  74 => 13,  72 => 12,  67 => 9,  59 => 7,  57 => 6,  54 => 5,  48 => 3,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/front_end_form.html", "/home/circleci/mailpoet/mailpoet/views/form/front_end_form.html");
    }
}
