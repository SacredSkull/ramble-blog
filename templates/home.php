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
                                {% for category in categories %}
                                <a href="{{baseUrl()}}/category/{{category.getSlug}}" class="category">
                                    <!-- <img height="20px" src="/include/img/skull.png" alt="..."> -->
                                    <p style="color: {{category.getColour}};">{{category.getSlug}}</p>
                                </a>
                                {% endfor %}
                            </div>
                        </div>
                        {# end left-nav #}
                        <div id="posts" class="col-md-6">
                            {% for post in posts %}
                            <div class="row">
                                <div id="whitespace">
                                    <h2 class="title"  id="category_name"><a style="color: {{post.getCategory.getColour}};" href="/category/{{post.getCategory.getSlug}}">{{post.getCategory.getName}} &#62;</a></h2>
                                    {% set slug = post.getSlug|split('_') %}
                                    <h1 class="title"><a class="title_link" href="{{baseUrl()}}/{{slug[0]|date('Y/m/d/')}}{{slug[1]}}">{{post.getTitle|title}}</a></h1>
                                    <hr>
                                    <h4>{{post.getCreatedAt|date("d l, F Y G:i")}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                {#<div class="col-md-1"></div>#}
                                {# Start content #}
                                <div id="content">
                                    {% if admin %}<form method="get" action="{{baseUrl()}}/admin/{{post.id}}"><button type="submit" class="pull-right post-button btn btn-link" href="/admin/{{post.getId}}"><span class="glyphicon glyphicon-edit"></span></button></form>
                                    <form method="post" action="{{baseUrl()}}/admin/{{post.id}}"><input type="hidden" name="_METHOD" value="DELETE"/><button type="submit" class="pull-right post-button btn btn-link" href="/admin/{{post.getId}}"><span class="glyphicon glyphicon-trash"></span></button></form>{% endif %}
                                    {{post.getBodyhtml|truncateHTML(2000, " ...")}}
                                    <p class="text-center post-cont-link"><a href="{{baseUrl()}}/{{slug[0]|date('Y/m/d/')}}{{slug[1]}}"> <span class="glyphicon glyphicon-chevron-down"></span></a></p>
                                    {% for tag in post.getTags  %}<a class="tag" href="{{baseUrl}}/tag/{{tag.getName}}">{{tag.getName}}</a> {%endfor%}
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
                                        <li {% if current_page == 1 %}class="disabled"{% endif %}><a href="{{pagination_url|default('/page/')}}1">&laquo;</a></li>
                                    {% for page in page_list %}
                                        <li {% if current_page == page %}class="active"{% endif %}><a href="{{pagination_url|default('/page/')}}{{page}}">{{page}}</a></li>
                                    {% endfor %}
                                        <li {% if max_pages == current_page %}class="disabled"{% endif %}><a href="{{pagination_url|default('/page/')}}{{max_pages}}">&raquo;</a></li>
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
