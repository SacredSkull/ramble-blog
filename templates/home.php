{% extends 'skeleton.php' %}
                    {% block additional_css%}
                        .do-not-style{
                            color: black;
                        }
                    {% endblock additional_css %}
                    {% block article_cont %}
                        {# Start left-nav #}
                        <div id="spacer" class="col-md-3">
                            <span id="left-nav-cap"></span>
                        </div>
                        <div id="left-nav">
                            <h2>The Specifics.</h2>
                            <div id="left-nav-categories">
                                {% for theme in themes %}
                                <a href="#" class="category">
                                    <img height="20px" src="/include/img/skull.png" alt="...">
                                    <p style="color: {{theme.getColour}};">{{theme.getName}}</p>
                                </a>
                                {% endfor %}
                            </div>
                        </div>
                        {# end left-nav #}
                        <div id="posts" class="col-md-6">
                            {% for post in posts %}
                            <div class="row">
                                <div id="whitespace">
                                    <h1 class="title"  id="theme_name"><a style="color: {{post.getTheme.getColour}};" href="/{{post.getTheme.getName}}/">{{post.getTheme.getName}} &#62;</a></h1>
                                    <p class="title"><a class="title_link" href="/post/{{post.getSlug}}">{{post.getTitle|title}}</a></p>
                                    <hr>
                                    <h4>{{post.getCreatedAt|date("d l, F Y G:i")}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                {#<div class="col-md-1"></div>#}
                                {# Start content #}
                                <div id="content">
                                    {% if admin %}<a class="pull-right" title="Edit Post" style="font-size: 32px;" href="/admin/{{post.getId}}"><span class="glyphicon glyphicon-edit"></span></a>{% endif %}
                                    {% set url = "/post/#{post.getSlug}" %}
                                    {% set url = '<p class="text-center post-cont-link"><a href="' ~ url ~ '"> <span class="glyphicon glyphicon-chevron-down"></span></a></p>'%}
                                    {{post.getBodyhtml|truncateHTML(2000, " ...")}}
                                    {{ url|raw }}
                                    <hr>
                                </div>
                                {# end content #}
                                {#<div class="col-md-1"></div>#}
                            </div>
                            {% endfor %}
                            {% if posts.haveToPaginate %}
                            <div class="row">
                                <div class="text-center">
                                    <ul class="pagination">
                                        <li {% if current_page == 1 %}class="disabled"{% endif %}><a href="/">&laquo;</a></li>
                                    {% for page in page_list %}
                                        <li {% if current_page == page %}class="active"{% endif %}><a href="/{{page}}">{{page}}</a></li>
                                    {% endfor %}
                                        <li {% if max_pages == current_page %}class="disabled"{% endif %}><a href="/{{max_pages}}">&raquo;</a></li>
                                    </ul>
                                </div>
                            </div>
                            {% endif %}
                        </div>
                        {# start right-nav #}
                        <div id="right-nav" class="col-md-3">
                        </div>
                        {# end right-nav #}

                    {% endblock article_cont %}
                    {% block additional_js %}
                        <script type="text/javascript">
                            $(document).ready(function(){
                                //$("#left-nav").mCustomScrollbar();
                            });
                        </script>
                    {% endblock additional_js %}
