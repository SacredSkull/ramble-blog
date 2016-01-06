<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="A layout example that shows off a blog page with a list of posts.">
		<title>{% block page_title %}{% endblock page_title %}SacredSkull &#9760;</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pure/0.6.0/pure-min.css">

		{% block head %}

			<!-- <link href='//fonts.googleapis.com/css?family=Passion+One|Basic|Droid+Sans:400,700|Inika:700|Roboto+Slab|Contrail+One' rel='stylesheet' type='text/css'> -->
		<link href='/include/css/nanoscroller.css' rel='stylesheet' type='text/css'>
		<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="wlwmanifest.xml" />
		<link rel="stylesheet" type="text/css" href="/include/css/jquery.mCustomScrollbar.min.css">
		{% endblock head %}
		<style type="text/css">
		{% block additional_css %}
		{% endblock additional_css%}
		</style>
		<!--[if lte IE 8]>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pure/0.6.0/grids-responsive-old-ie-min.css">
		<![endif]-->
		<!--[if gt IE 8]><!-->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pure/0.6.0/grids-responsive-min.css">
		<!--<![endif]-->

		<!--[if lte IE 8]>
			<link rel="stylesheet" href="css/layouts/minimal-styles-old-ie.css">
		<![endif]-->
		<!--[if gt IE 8]><!-->
			{%if wireframe%}<link href='/include/css/minimal-wireframe.css' rel='stylesheet/css' type='text/css'>{%else%}<link href='/include/css/minimal-styles.css' rel='stylesheet' type='text/css'>{%endif%}
		<!--<![endif]-->

	</head>
	<body>
		<div id="layout" class="pure-g">
			<div class="sidebar pure-u-1 pure-u-md-1-4">
				<div class="header">
					<img id="skull" class="pure-img-responsive" src="/include/img/skull.svg" onerror="this.src='/include/img/skull.png'"></img>
					<h1 class="brand-title"><a href="{{ baseUrl() }}">SacredSkull<span>.</span></a></h1>
					<h2 class="brand-tagline"><i>{{ skull_greeting|default('Take note of your nearest emergency exit') }}</i></h2>

					<nav class="nav">
						<ul class="nav-list">
							{% for category in categories %}
							<li class="nav-item nav-item-tight">
								<a style="//color: {{category.getColour}};" href="{{baseUrl()}}/category/{{category.getSlug}}" class="pure-button category">{{category.getName}}</a>
							</li>
							{% endfor %}
						</ul>
					</nav>
				</div>
			</div>

			<div class="content pure-u-1 pure-u-md-3-4">
				<div>
					<!-- A wrapper for all the blog posts -->
					<div class="posts">
						<h1 class="content-subhead">Posts</h1>

						<!-- A single blog post -->
						{% for post in posts %}
						<section class="post">
							<header class="post-header">
								<img class="post-avatar" alt="Post's image" src="include/img/edit.png" height="48" width="48">
								{% set slug = post.getSlug|split('_') %}
								<h2 class="post-title"><a class="title_link" href="{{baseUrl()}}/{{slug[0]|date('Y/m/d/')}}{{slug[1]}}">{{post.title}}</a></h2>
								<p class="post-meta">
									<a href="/category/{{post.getCategory.getSlug}}" style="//color: {{post.getCategory.getColour}};" class="post-category">{{post.getCategory.getName}}</a>
									{% for tag in post.getTags  %}
										relating to
										<a class="tag post-category post-category-design" href="{{baseUrl}}/tag/{{tag.getName}}">{{tag.getName}}</a>
									{% endfor %}
									&nbsp;{{post.getCreatedAt|date('jS F Y, G:i')}}
								</p>
							</header>

							<div class="post-description">
								<p>{{post.getBodyhtml|truncateHTML(2000, " ...")}}</p>
							</div>
						</section>
						{% endfor %}
					</div>

					<div class="footer">
						{% if posts.haveToPaginate %}
                            <div class="pure-menu pure-menu-horizontal">
                                <ul>
                                    <li class="pure-menu-item{% if current_page == 1 %} disabled{% endif %}"><a class="pure-menu-link" href="{{pagination_url|default('/page/')}}1">&laquo;</a></li>
                                {% for page in page_list %}
                                    <li class="pure-menu-item{% if current_page == page %} active{% endif %}"><a class="pure-menu-link" href="{{pagination_url|default('/page/')}}{{page}}">{{page}}</a></li>
                                {% endfor %}
                                    <li class="pure-menu-item{% if max_pages == current_page %} disabled{% endif %}"><a class="pure-menu-link" href="{{pagination_url|default('/page/')}}{{max_pages}}">&raquo;</a></li>
                                </ul>
                            </div>
                            {% endif %}
						<div class="pure-menu pure-menu-horizontal">
							<ul>
								<li class="pure-menu-item"><a title="Read: how I wasted my time this week" href="//www.stumbleupon.com/stumbler/SacredSkull" class="pure-menu-link">My StumbleUpon</a></li>
								<li class="pure-menu-item"><a href="//www.last.fm/user/Cl0ttERS" class="pure-menu-link">Last.fm</a></li>
								<li class="pure-menu-item"><a title="Open-source projects I have tinkered on" href="//github.com/sacredskull" class="pure-menu-link">GitHub</a></li>
								<li class="pure-menu-item"><a class="pure-menu-link">{{ executeTime() }}</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="/include/js/minimal/out/all.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js"></script>
		<script>
		  WebFont.load({
		    google: {
		      families: ['Passion One', 'Fugaz One', 'Droid Sans:400,700', 'Droid Serif', 'Roboto Slab', 'Oswald', 'Archivo Narrow', 'Open Sans:400'{% for font in additionalFonts %}, '{{font}}'{% endfor %}]
		    }
		  });
		</script>
		{% block additional_js %}
		{% endblock additional_js %}
	</body>
</html>
