{% extends 'skeleton.php' %}
					{% block title %}
						<h1 class="title" id="theme_name"><a href="/{{post.getTheme.getName}}/">{{post.getTheme.getName}} &#62;</a></h1>
						<p class="title">{{post.getTitle|title}}</p>
						<hr>
						<h4>{{post.getCreatedAt|date("d l, F Y G:i")}}</h4>
					{% endblock title %}
					{% block left_nav %}
						<div class="row">
							<h3>{{ post.pollquestion }}</h3>
						</div>
						<div class="row">
							<h2>Popular</h2>
							<div class="media">
								<a class="pull-left" href="#">
									<img class="media-object" height="60px" src="/include/img/skull.png" alt="...">
								</a>
								<div class="media-body">
									<h4 class="media-heading">Example Post</h4>
									{{ jsonThemes }}
									<span title="int views per week" class='pull-right label label-danger'>4321 <span class="glyphicon glyphicon-fire"></span></span>
								</div>
							</div>
						</div>
					{% endblock left_nav %}
					{% block img_title %}
							<div style="position: absolute; top: 120px; width: 100%; height: 260px;">
								<div id="post_img_container" style="text-align: center;">
									<div id="post_img" data-stellar-offset-parent="true" data-stellar-vertical-offset="80" data-stellar-background-ratio="0.5" style="display: inline-block; height: 180px; width: 100%; background-repeat: no-repeat; background-size: cover; background-image: url('/include/img/test.png'); background-attachment: fixed; display: block; margin-left: auto; margin-right: auto;"></div>
								</div>
							</div>
					{% endblock img_title %}
					{% block content %}
						{{ parent() }}
						{{ post.getBody|markdown }}
						<hr class="fin">
						<div id='end-poll-container'>
							<span id="end-poll-votes">
								<div>
									<span class="pull-left glyphicon glyphicon-thumbs-up"></span>
								</div>
								<div width="60%" class="progress">
									<div class="progress-bar progress-bar-success" role="progressbar" style="width: 60%;">
										<span class="sr-only">60% Upvoted</span>

									</div>
									<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 40%;">
										<span class="sr-only">40% Downvoted</span>
									</div>
								</div>
								<div>
									<span class="pull=right glyphicon glyphicon-thumbs-down"></span>
								</div>
							</span>
						</div>
					{% endblock content %}
					{% block right_nav %}
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
					{% endblock right_nav %}
					{% block additional_js %}
						<script type="text/javascript" src="/include/js/jquery.stellar.js"></script>
						<script type="text/javascript">
						$.stellar();
						</script>
					{% endblock %}
