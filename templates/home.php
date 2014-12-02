{% extends 'skeleton.php' %}
					{% block additional_css%}
						.do-not-style{
							color: black;
						}
					{% endblock additional_css %}
					{% block article %}
						{% for post in posts %}
		                <div class="row">
		                	{# Start title #}
		                    <div class="col-md-3"></div>
		                    <div id="whitespace" class="col-md-6">
		                        <h1 class="title"  id="theme_name"><a style="color: {{post.getTheme.getColour}};" href="/{{post.getTheme.getName}}/">{{post.getTheme.getName}} &#62;</a></h1>
		                        <a href="/post/{{post.getSlug}}"><p class="title">{{post.getTitle|title}}</p></a>
		                        <hr>
		                        <h4>{{post.getCreatedAt|date("d l, F Y G:i")}}</h4>
		                    </div>
		                    <div class="col-md-3"></div>
		                    {# End title #}
		                </div>
		                <div class="row">
		                    <div class="col-md-1"></div>
		                    {# Start left-nav #}
		                    <div id="left-nav" class="col-md-2">
		                        <div class="row">
		                            <h3>{{post.pollquestion}}</h3>
		                        </div>
		                        <div class="row">
		                            <h2>Popular</h2>
		                            <div class="media">
		                                <a class="pull-left" href="#">
		                                    <img class="media-object" height="60px" src="/include/img/skull.png" alt="...">
		                                </a>
		                                <div class="media-body">
		                                    <h4 class="media-heading">Example Post</h4>
		                                    {{jsonThemes}}
		                                    <span title="int views per week" class='pull-right label label-danger'>4321 <span class="glyphicon glyphicon-fire"></span></span>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                    {# end left-nav #}
		                    {# Start content #}
		                    <div class="col-md-6" id="content">
		                        {% if admin %}<a class="pull-right" title="Edit Post" style="font-size: 32px;" href="/admin/{{post.getId}}"><span class="glyphicon glyphicon-edit"></span></a>{% endif %}
		                        <a class="do-not-style" href="/post/{{post.getSlug}}">{{post.getBody|truncate(650, true, ' ...')|markdown}}</a>
		                        <hr>
		                    </div>
		                    {# end content #}
		                    {# start right-nav #}
		                    <div id="right-nav" class="col-md-2">
		                        <div class="row">
		                            <h2>Recent</h2>
		                            <div class="media">
		                                <a class="pull-left" href="#">
		                                    <img class="media-object" height="60px" src="/include/img/skull.png" alt="...">
		                                </a>
		                                <div class="media-body">
		                                    <h4 class="media-heading">Media heading</h4>
		                                    ...
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                    {# end right-nav #}
		                    <div class="col-md-1"></div>
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
					{% endblock article %}
