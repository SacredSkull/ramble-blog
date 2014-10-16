<?php

/* home.php */
class __TwigTemplate_bcbc543f8514e3ebcd7d2ebf5c917cf1acf0905f9989b82ec809ff703081f8c4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
\t<head>
\t\t<title>SacredSkull</title>
\t\t<link href='http://fonts.googleapis.com/css?family=Passion+One|Basic|Droid+Sans:400,700|Inika:700|Roboto+Slab|Contrail+One' rel='stylesheet' type='text/css'>
\t\t<link href='./include/css/nanoscroller.css' rel='stylesheet' type='text/css'>
\t\t";
        // line 6
        if ((isset($context["wireframe"]) ? $context["wireframe"] : null)) {
            echo "<link href='./include/css/wireframe.css' rel='stylesheet/css' type='text/css'>";
        } else {
            echo "<link href='./include/css/styles.css' rel='stylesheet' type='text/css'>";
        }
        // line 7
        echo "\t\t<meta charset=\"UTF-8\" />
\t</head>
\t<body class=\"\">
\t\t<header class=\"navbar navbar-fixed-top\">
\t\t\t<a href=\"#\" id=\"head-logo\">
\t\t\t\t<span id=\"first-word\">Sacred<span id=\"second-word\">Skull</span></span>
\t\t\t\t<span id=\"dotdotdot\">. . .</span>
\t\t\t</a>
\t\t\t<span id='skull'></span>
\t\t\t<span id='head-nav-bg'>
\t\t\t</span>
\t\t\t<a href=\"/\" class=\"head-nav-link\">
\t\t\t\t<span>HOME</span>
\t\t\t</a>
\t\t\t<a href=\"/contact\" class=\"head-nav-link\">
\t\t\t\t<span>CONTACT</span>
\t\t\t</a>
\t\t\t<a href=\"/about\" class=\"head-nav-link\">
\t\t\t\t<span>ABOUT</span>
\t\t\t</a>
\t\t\t<div id='skull-bubble' class='bubble'><p>";
        // line 27
        echo twig_escape_filter($this->env, (isset($context["skull_greeting"]) ? $context["skull_greeting"] : null), "html", null, true);
        echo "</p></div>
\t\t</header>
\t\t<div id=\"wrapper\">
\t\t\t<div id=\"main\" class=\"container-fluid\">
\t\t\t\t<div class=\"row\">
\t\t\t\t\t<div class=\"col-md-3\"></div>
\t\t\t\t\t<div id=\"whitespace\" class=\"col-md-6\">
\t\t\t\t\t\t<h1>\t";
        // line 34
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["newestpost"]) ? $context["newestpost"] : null), "getTitle", array()), "html", null, true);
        echo "\t</h1>
\t\t\t\t\t\t<hr>
\t\t\t\t\t\t<h4>\t";
        // line 36
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["newestpost"]) ? $context["newestpost"] : null), "getCreatedAt", array()), "d l, F Y G:i"), "html", null, true);
        echo "\t\t</h4>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-3\"></div>
\t\t\t\t</div>
\t\t\t\t<div class=\"row\">
\t\t\t\t\t<div class=\"col-md-1\"></div>
\t\t\t\t\t<div id=\"left-nav\" class=\"col-md-2\">
\t\t\t\t\t\t<div class=\"row\">
\t\t\t\t\t\t\t<h3>";
        // line 44
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["newestpost"]) ? $context["newestpost"] : null), "pollquestion", array()), "html", null, true);
        echo "</h3>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"row\">
\t\t\t\t\t\t\t<h2>Popular</h2>
\t\t\t\t\t\t\t<div class=\"media\">
\t\t\t\t\t\t\t\t<a class=\"pull-left\" href=\"#\">
\t\t\t\t\t\t\t\t    <img class=\"media-object\" height=\"60px\" src=\"./include/img/skull.png\" alt=\"...\">
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t<div class=\"media-body\">
\t\t\t\t\t\t\t\t   \t<h4 class=\"media-heading\">Example Post</h4>
\t\t\t\t\t\t\t\t    Something so awesome it got 4321 views, in some unknown timeframe we won't reveal to make it look better.
\t\t\t\t\t\t\t\t    <span title=\"int views per week\" class='pull-right label label-danger'>4321 <span class=\"glyphicon glyphicon-fire\"></span></span>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-6\" id=\"content\">
\t                    <p>\t\t";
        // line 61
        echo $this->env->getExtension('Twig_BBCode_Extension')->bbcodeFilter($this->getAttribute((isset($context["newestpost"]) ? $context["newestpost"] : null), "getBody", array()));
        echo "\t\t</p>
\t                    <hr class=\"fin\">
\t                    <div id='end-poll-container'>
\t                    \t<span id=\"end-poll-votes\">
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<span class=\"pull-left glyphicon glyphicon-thumbs-up\"></span>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div width=\"60%\" class=\"progress\">
\t\t\t\t\t\t\t\t  \t<div class=\"progress-bar progress-bar-success\" role=\"progressbar\" style=\"width: 60%;\">
\t\t\t\t\t\t\t\t    \t<span class=\"sr-only\">60% Upvoted</span>

\t\t\t\t\t\t\t\t  \t</div>
\t\t\t\t\t\t\t\t  \t<div class=\"progress-bar progress-bar-danger\" role=\"progressbar\" style=\"width: 40%;\">
\t\t\t\t\t\t\t\t   \t\t<span class=\"sr-only\">40% Downvoted</span>
\t\t\t\t\t\t\t\t  \t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<span class=\"pull=right glyphicon glyphicon-thumbs-down\"></span>
\t\t\t\t\t\t\t\t</div>
\t                    \t</span>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div id=\"right-nav\" class=\"col-md-2\">
\t\t\t\t\t\t<div class=\"row\">
\t\t\t\t\t\t\t<h2>Recent</h2>
\t\t\t\t\t\t\t<div class=\"media\">
\t\t\t\t\t\t\t\t<a class=\"pull-left\" href=\"#\">
\t\t\t\t\t\t\t\t    <img class=\"media-object\" height=\"60px\" src=\"./include/img/skull.png\" alt=\"...\">
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t<div class=\"media-body\">
\t\t\t\t\t\t\t\t   \t<h4 class=\"media-heading\">Media heading</h4>
\t\t\t\t\t\t\t\t    ...
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-1\"></div>
\t\t\t\t</div>
\t\t  \t</div>
\t\t</div>
\t\t<div class=\"footer\">
\t\t\t<span id='footer-white'>&nbsp;</span>
\t\t\t<div class=\"container\">
\t\t\t\t<div class=\"row\">
\t\t\t\t\t<p></p>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t ";
        // line 109
        echo twig_escape_filter($this->env, (isset($context["your_name"]) ? $context["your_name"] : null), "html", null, true);
        echo "
\t\t<script type=\"text/javascript\" src=\"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
\t\t<script type=\"text/javascript\" src=\"./include/js/min/bootstrap.min.js\"></script>
\t\t<script type=\"text/javascript\" src=\"./include/js/min/jquery.nanoscroller.min.js\"></script>
\t\t<script type=\"text/javascript\" src=\"./include/js/min/custom.min.js\"></script>
\t</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "home.php";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  151 => 109,  100 => 61,  80 => 44,  69 => 36,  64 => 34,  54 => 27,  32 => 7,  26 => 6,  19 => 1,);
    }
}
