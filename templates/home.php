<!DOCTYPE html>
	<head>
		<title>SacredSkull</title>
		<link href='http://fonts.googleapis.com/css?family=Passion+One|Basic|Droid+Sans:400,700|Inika:700' rel='stylesheet' type='text/css'>
		<link href='include/css/nanoscroller.css' rel='stylesheet' type='text/css'>
		{%if wireframe%}<link href='include/css/wireframe.less' rel='stylesheet/less' type='text/css'>{%elseif debug%}<link href='include/css/styles.less' rel='stylesheet/less' type='text/css'>{%else%}<link href='include/css/styles.css' rel='stylesheet' type='text/css'>{%endif%}
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
			<a href="/forums" class="head-nav-link">
				<span>FORUMS</span>
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
				<div class="col-md-1"></div>
				<div id="left-nav" class="col-md-2">
					<div class="row">
						<h3>{{newestpost.pollquestion}}</h3>
						<span class="pull-left glyphicon glyphicon-thumbs-up"></span>
						<span class="pull=right glyphicon glyphicon-thumbs-down"></span>
                    	<h3>	{{newestpost.date}}		</h3>
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
					<h1>	{{newestpost.title}}	</h1>
                    <p>		{{newestpost.body}}		</p>
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
		<div class="footer">
			<span id='footer-white'>&nbsp;</span>
			<div class="container">
				<div class="row">
					<p></p>
				</div>
			</div>
		</div>
		 {{ your_name }}
		{%if debug%}<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/1.7.0/less.min.js"></script>{%endif%}
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="/include/js/bootstrap-min.js"></script>
		<script type="text/javascript" src="/include/js/jquery.nanoscroller.js"></script>
		<script type="text/javascript" src="/include/js/custom.js"></script>
	</body>
</html>