<!DOCTYPE html>
	<head>
		<title>SacredSkull</title>
		<link href='http://fonts.googleapis.com/css?family=Passion+One|Basic|Droid+Sans:400,700|Inika:700|Roboto+Slab|Contrail+One' rel='stylesheet' type='text/css'>
		<link href='./include/css/nanoscroller.css' rel='stylesheet' type='text/css'>
		{%if wireframe%}<link href='./include/css/wireframe.css' rel='stylesheet/css' type='text/css'>{%else%}<link href='./include/css/styles.css' rel='stylesheet' type='text/css'>{%endif%}
		<meta charset="UTF-8" />
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
			<div id='skull-bubble' class='bubble'><p>{{ skull_greeting }}</p></div>
		</header>
		<div id="wrapper">
			<div id="main" class="container-fluid">
				<div class="row">
					<div class="col-md-3"></div>
					<div id="whitespace" class="col-md-6">
						<h1>	{{newestpost.getTitle}}	</h1>
						<hr>
						<h4>	{{newestpost.getCreatedAt|date("d l, F Y G:i")}}		</h4>
					</div>
					<div class="col-md-3"></div>
				</div>
				<div class="row">
					<div class="col-md-1"></div>
					<div id="left-nav" class="col-md-2">
						<div class="row">
							<h3>{{newestpost.pollquestion}}</h3>
						</div>
						<div class="row">
							<h2>Popular</h2>
							<div class="media">
								<a class="pull-left" href="#">
								    <img class="media-object" height="60px" src="./include/img/skull.png" alt="...">
								</a>
								<div class="media-body">
								   	<h4 class="media-heading">Example Post</h4>
								    Something so awesome it got 4321 views, in some unknown timeframe we won't reveal to make it look better.
								    <span title="int views per week" class='pull-right label label-danger'>4321 <span class="glyphicon glyphicon-fire"></span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6" id="content">
	                    <p>		{{newestpost.getBody|bbcode}}		</p>
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
					</div>
					<div id="right-nav" class="col-md-2">
						<div class="row">
							<h2>Recent</h2>
							<div class="media">
								<a class="pull-left" href="#">
								    <img class="media-object" height="60px" src="./include/img/skull.png" alt="...">
								</a>
								<div class="media-body">
								   	<h4 class="media-heading">Media heading</h4>
								    ...
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
		  	</div>
		</div>
		<div class="footer">
			<span id='footer-white'>&nbsp;</span>
			<div class="container">
				<div class="row">
					<p></p>
				</div>
			</div>
		</div>
		 {{ your_name }}
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="./include/js/min/bootstrap.min.js"></script>
		<script type="text/javascript" src="./include/js/min/jquery.nanoscroller.min.js"></script>
		<script type="text/javascript" src="./include/js/min/custom.min.js"></script>
	</body>
</html>
