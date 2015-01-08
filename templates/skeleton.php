<!DOCTYPE html>
	<head>
			<meta charset="UTF-8" />
		{% block head %}
			<title>SacredSkull</title>
			<link href='//fonts.googleapis.com/css?family=Passion+One|Basic|Droid+Sans:400,700|Inika:700|Roboto+Slab|Contrail+One' rel='stylesheet' type='text/css'>
			<link href='/include/css/nanoscroller.css' rel='stylesheet' type='text/css'>
			{%if wireframe%}<link href='/include/css/wireframe.css' rel='stylesheet/css' type='text/css'>{%else%}<link href='/include/css/styles.css' rel='stylesheet' type='text/css'>{%endif%}
            <link rel="stylesheet" type="text/css" href="/include/css/jquery.mCustomScrollbar.min.css">
		{% endblock head %}
			<style type="text/css">
		{% block additional_css %}
		{% endblock additional_css%}
			</style>
	</head>
	<body class="">
		<header class="navbar navbar-fixed-top">
			<a href="#" id="head-logo">
				<span id="first-word">Sacred<span id="second-word">Skull</span></span>
				<span id="dotdotdot">. . .</span>
			</a>
			<span id='skull'></span>
			<span id='head-nav-bg'>
			</span>
			<a href="/" class="head-nav-link">
				<span>HOME</span>
			</a>
			<a href="/contact" class="head-nav-link">
				<span>CONTACT</span>
			</a>
			<a href="/about" class="head-nav-link">
				<span>ABOUT</span>
			</a>
			{% if admin %}
			<a href="/admin" class="head-nav-link">
				<span>NEW POST</span>
			</a>
			{% endif %}
			<div id='skull-bubble' class='bubble'>
				{% spaceless %}
					{% if flash.denied %}
						<style type="text/css">
							.bubble{
								background-color: red;
							}
							.bubble:after{
								border-color: transparent red;
							}
							#skull{
								background-image: url('/include/img/error.png'), url('/include/img/skull-small.png');
							}
						</style>
						<p style="color: white;">{{ flash.denied}}</p>
					{% else %}
						<p>{{ skull_greeting|default('*Speechless*') }}</p>
						{% if mode == 'edit' %}
						<style type="text/css">
							#skull{
								background-image: url('/include/img/edit.png'), url('/include/img/skull-small.png');
							}
						</style>
						{% endif %}
					{% endif %}
				{% endspaceless %}
			</div>
		</header>

		<div id="main" class="container-fluid">
			{% block article_cont %}
			<div style="height: 200px;" class="row">
				{% block img_title %}
				{% endblock img_title %}
			</div>
			<div class="row">
				<div class="col-md-3"></div>
				<div id="whitespace" class="col-md-6">
					{% block title %}
					{% endblock title %}
				</div>
				<div class="col-md-3"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div id="left-side" class="col-md-2">
					{% block left_side %}
					{% endblock left_side %}
				</div>
				<div class="col-md-6" id="content">
					{% block content %}
						{% if admin %}<a class="pull-right" title="Edit Post" style="font-size: 32px;" href="/admin/{{post.getId}}"><span class="glyphicon glyphicon-edit"></span></a>{% endif %}
					{% endblock content %}
				</div>
				<div id="right-nav" class="col-md-2">
					{% block right_nav %}
					{% endblock right_nav %}
				</div>
				<div class="col-md-1"></div>
			</div>
			{% endblock article_cont %}
			<div class="footer">
				<span id='footer-white'>&nbsp;</span>
				<div class="container">
					<br>
					<div class="row">
						<p class="pull-left">Coding this blog from scratch was great fun. Why not try yourself?</p>
						<p class="pull-right"><!--sse-->Contact me at clotters@gmail.com<!--/sse--></p>
					</div>
					<br>
					<br>
					<div class="row">
						<p><b>Made with </b><span class="glyphicon glyphicon-hand-down"></span></p>
						<p><a href="http://www.slimframework.com/">The Slim Framework</a></p>
						<p><a href="http://propelorm.org/">Propel2 ORM</a></p>
						<p>...and lots of tea.</p>
					</div>
				</div>
			</div>
		</div>
		{% block additional_column %}
		{% endblock additional_column %}

		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="/include/js/min/bootstrap.min.js"></script>
		<script type="text/javascript" src="/include/js/min/jquery.nanoscroller.min.js"></script>
        <script type="text/javascript" src="/include/js/min/jquery.mCustomScrollbar.min.js"></script>
		{% block additional_js %}
		{% endblock additional_js %}
		<script type="text/javascript" src="/include/js/custom.js"></script>
	</body>
</html>
