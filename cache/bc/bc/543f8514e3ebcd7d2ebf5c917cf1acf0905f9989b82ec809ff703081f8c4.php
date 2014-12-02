<?php

/* home.php */
class __TwigTemplate_bcbc543f8514e3ebcd7d2ebf5c917cf1acf0905f9989b82ec809ff703081f8c4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("skeleton.php");

        $this->blocks = array(
            'additional_css' => array($this, 'block_additional_css'),
            'article' => array($this, 'block_article'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "skeleton.php";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_additional_css($context, array $blocks = array())
    {
        // line 3
        echo "\t\t\t\t\t\t.do-not-style{
\t\t\t\t\t\t\tcolor: black;
\t\t\t\t\t\t}
\t\t\t\t\t";
    }

    // line 7
    public function block_article($context, array $blocks = array())
    {
        // line 8
        echo "\t\t\t\t\t\t";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["posts"]) ? $context["posts"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["post"]) {
            // line 9
            echo "\t\t                <div class=\"row\">
\t\t                \t";
            // line 11
            echo "\t\t                    <div class=\"col-md-3\"></div>
\t\t                    <div id=\"whitespace\" class=\"col-md-6\">
\t\t                        <h1 class=\"title\"  id=\"theme_name\"><a style=\"color: ";
            // line 13
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["post"], "getTheme", array()), "getColour", array()), "html", null, true);
            echo ";\" href=\"/";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["post"], "getTheme", array()), "getName", array()), "html", null, true);
            echo "/\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["post"], "getTheme", array()), "getName", array()), "html", null, true);
            echo " &#62;</a></h1>
\t\t                        <a href=\"/post/";
            // line 14
            echo twig_escape_filter($this->env, $this->getAttribute($context["post"], "getSlug", array()), "html", null, true);
            echo "\"><p class=\"title\">";
            echo twig_escape_filter($this->env, twig_title_string_filter($this->env, $this->getAttribute($context["post"], "getTitle", array())), "html", null, true);
            echo "</p></a>
\t\t                        <hr>
\t\t                        <h4>";
            // line 16
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($context["post"], "getCreatedAt", array()), "d l, F Y G:i"), "html", null, true);
            echo "</h4>
\t\t                    </div>
\t\t                    <div class=\"col-md-3\"></div>
\t\t                    ";
            // line 20
            echo "\t\t                </div>
\t\t                <div class=\"row\">
\t\t                    <div class=\"col-md-1\"></div>
\t\t                    ";
            // line 24
            echo "\t\t                    <div id=\"left-nav\" class=\"col-md-2\">
\t\t                        <div class=\"row\">
\t\t                            <h3>";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute($context["post"], "pollquestion", array()), "html", null, true);
            echo "</h3>
\t\t                        </div>
\t\t                        <div class=\"row\">
\t\t                            <h2>Popular</h2>
\t\t                            <div class=\"media\">
\t\t                                <a class=\"pull-left\" href=\"#\">
\t\t                                    <img class=\"media-object\" height=\"60px\" src=\"/include/img/skull.png\" alt=\"...\">
\t\t                                </a>
\t\t                                <div class=\"media-body\">
\t\t                                    <h4 class=\"media-heading\">Example Post</h4>
\t\t                                    ";
            // line 36
            echo twig_escape_filter($this->env, (isset($context["jsonThemes"]) ? $context["jsonThemes"] : null), "html", null, true);
            echo "
\t\t                                    <span title=\"int views per week\" class='pull-right label label-danger'>4321 <span class=\"glyphicon glyphicon-fire\"></span></span>
\t\t                                </div>
\t\t                            </div>
\t\t                        </div>
\t\t                    </div>
\t\t                    ";
            // line 43
            echo "\t\t                    ";
            // line 44
            echo "\t\t                    <div class=\"col-md-6\" id=\"content\">
\t\t                        ";
            // line 45
            if ((isset($context["admin"]) ? $context["admin"] : null)) {
                echo "<a class=\"pull-right\" title=\"Edit Post\" style=\"font-size: 32px;\" href=\"/admin/";
                echo twig_escape_filter($this->env, $this->getAttribute($context["post"], "getId", array()), "html", null, true);
                echo "\"><span class=\"glyphicon glyphicon-edit\"></span></a>";
            }
            // line 46
            echo "\t\t                        <a class=\"do-not-style\" href=\"/post/";
            echo twig_escape_filter($this->env, $this->getAttribute($context["post"], "getSlug", array()), "html", null, true);
            echo "\">";
            echo $this->env->getExtension('Twig_Parsedown_Extension')->markdownFilter(twig_truncate_filter($this->env, $this->getAttribute($context["post"], "getBody", array()), 650, true, " ..."));
            echo "</a>
\t\t                        <hr>
\t\t                    </div>
\t\t                    ";
            // line 50
            echo "\t\t                    ";
            // line 51
            echo "\t\t                    <div id=\"right-nav\" class=\"col-md-2\">
\t\t                        <div class=\"row\">
\t\t                            <h2>Recent</h2>
\t\t                            <div class=\"media\">
\t\t                                <a class=\"pull-left\" href=\"#\">
\t\t                                    <img class=\"media-object\" height=\"60px\" src=\"/include/img/skull.png\" alt=\"...\">
\t\t                                </a>
\t\t                                <div class=\"media-body\">
\t\t                                    <h4 class=\"media-heading\">Media heading</h4>
\t\t                                    ...
\t\t                                </div>
\t\t                            </div>
\t\t                        </div>
\t\t                    </div>
\t\t                    ";
            // line 66
            echo "\t\t                    <div class=\"col-md-1\"></div>
\t\t                </div>
\t\t                \t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['post'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 69
        echo "\t\t                ";
        if ($this->getAttribute((isset($context["posts"]) ? $context["posts"] : null), "haveToPaginate", array())) {
            // line 70
            echo "\t\t                <div class=\"row\">
                            <div class=\"text-center\">
    \t\t\t\t\t\t\t<ul class=\"pagination\">
    \t\t\t\t\t\t\t\t<li ";
            // line 73
            if (((isset($context["current_page"]) ? $context["current_page"] : null) == 1)) {
                echo "class=\"disabled\"";
            }
            echo "><a href=\"/\">&laquo;</a></li>
    \t\t\t\t\t\t\t";
            // line 74
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["page_list"]) ? $context["page_list"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["page"]) {
                // line 75
                echo "    \t\t\t\t\t\t\t\t<li ";
                if (((isset($context["current_page"]) ? $context["current_page"] : null) == $context["page"])) {
                    echo "class=\"active\"";
                }
                echo "><a href=\"/";
                echo twig_escape_filter($this->env, $context["page"], "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $context["page"], "html", null, true);
                echo "</a></li>
    \t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['page'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 77
            echo "    \t\t\t\t\t\t\t\t<li ";
            if (((isset($context["max_pages"]) ? $context["max_pages"] : null) == (isset($context["current_page"]) ? $context["current_page"] : null))) {
                echo "class=\"disabled\"";
            }
            echo "><a href=\"/";
            echo twig_escape_filter($this->env, (isset($context["max_pages"]) ? $context["max_pages"] : null), "html", null, true);
            echo "\">&raquo;</a></li>
    \t\t\t\t\t\t\t</ul>
                            </div>
\t\t                </div>
\t\t                ";
        }
        // line 82
        echo "\t\t\t\t\t";
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
        return array (  198 => 82,  185 => 77,  170 => 75,  166 => 74,  160 => 73,  155 => 70,  152 => 69,  144 => 66,  128 => 51,  126 => 50,  117 => 46,  111 => 45,  108 => 44,  106 => 43,  97 => 36,  84 => 26,  80 => 24,  75 => 20,  69 => 16,  62 => 14,  54 => 13,  50 => 11,  47 => 9,  42 => 8,  39 => 7,  32 => 3,  29 => 2,);
    }
}
