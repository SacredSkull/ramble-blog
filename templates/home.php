<!DOCTYPE html>
	<head>
		<title>SacredSkull</title>
		<link href='http://fonts.googleapis.com/css?family=Passion+One|Basic|Droid+Sans:400,700|Inika:700' rel='stylesheet' type='text/css'>
		<style>
			{{ css_output|raw }}
		</style>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<meta charset="UTF-8" />
	</head>
	<body class="nano">
		<div id="container">
			<header>
				<span id='skull'></span>
				<span id='head-nav-bg'>
					<a href='Pin Navigation Links' onclick='return false;' id='head-nav-pin'></a>
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
				<span id="head-logo">
					<a href="#" onclick="return false;">
						<h1>Sacred<span id='secondword'>Skull</span><h2>. . .</h2></h1>
					</a>
				</span>
				<div id='skull-bubble' class='bubble'><p>{{ skull_greeting }}</p></div>
			</header>
			<div id="maincontainer">
				<div id="content">
                    <h3>AWESOME CONTENT</h3>
				</div>
			</div>
		</div>
		<footer>
			You should prolly get rid of this.
		</footer>
		 {{ your_name }}
		</div>
		<script type="text/javascript" src="/include/js/jquery.nanoscroller.js"></script>
		<script>
			//$(".nano").nanoScroller();
			
			var bPinned = false;
			
			$("header").hover(function(){
				$("h1").css({color: "#fff"});
				$("#secondword").css({color: "#F26101"});
				$("#bubble path").css({fill: "#D9CB9E"});
				$("h2").animate({
					bottom: "75",
					opacity: "1.0"
				}, 300,function(){
					//Animation finished
				});
				$("header").animate({
					height: "110"
				}, 300, function(){
					//Animation finished
				});
				$("header.span").animate({
					top: "10"
				}, 300);
			},function(){
				$("h1").css({color: "#F26101"});
				$("#secondword").css({color: "#fff"});
				$("#bubble path").css({fill: "#fff"});
				$("h2").animate({
					bottom: "60",
					opacity: "0.0"
				}, 300,function(){
					//Animation finished
				});
				if(!bPinned){
					$("header").animate({
						height: "70"
					}, 300, function(){
						//Animation finished
					});
					$("header.span").animate({
						top: "-10"
					}, 300);
				}
			});
			$("h1").click(function(){
				
			});
			
			$('#head-nav-pin').hover(
				//mouse over
				function(){
					$(this).css('background-image','url("/include/img/pinned.png")');
				},
				//mouse out
				function() {
					if(!bPinned){
						$(this).css('background-image','url("/include/img/unpinned.png")');
					}
				}
			);
			
			$('#head-nav-pin').click(
				function(){
					if(!bPinned){
						bPinned = true;
					} else {
						bPinned = false;
					}
				}
			);
			
			$('document').ready( function (){
				var iPhraseLength = $('#skull-bubble').text().length;
				var iFontSize = $('#skull-bubble p').css('font-size');
				iFontSize = window.parseInt(iFontSize);
				iFontSize = iFontSize * 0.5;
				$('.bubble').animate({
					width: iPhraseLength * iFontSize + 25 + "px",
					opacity: 1
				}, 500);
			});
			
			$(window).resize( function () {
				if(document.body.clientWidth < 1000){
					$('#skull-bubble').fadeOut(100);
				} else {
					var iPhraseLength = $('#skull-bubble').text().length;
					var iFontSize = $('#skull-bubble p').css('font-size');
					iFontSize = window.parseInt(iFontSize);
					iFontSize = iFontSize * 0.5;
					$('.bubble').animate({
						width: iPhraseLength * iFontSize + 25 + "px",
						opacity: 1
					}, 500);
				}
			});
			
		</script>
	</body>
</html>