var bPinned = false;

$("header").hover(function(){
	$("#first-word").css({color: "#fff"});
	$("#second-word").css({color: "#F26101"});
	$("#dotdotdot").animate({
		bottom: "75",
		opacity: "1.0"
	}, 300,function(){
		//Animation finished
	});
	$("header").animate({
	}, 300, function(){
		//Animation finished
	});
	$("header.span").animate({
		top: "10"
	}, 300);
},function(){
	$("#first-word").css({color: "#F26101"});
	$("#second-word").css({color: "#fff"});
	$("#dotdotdot").animate({
		bottom: "60",
		opacity: "0.0"
	}, 300,function(){
		//Animation finished
	});
	if(!bPinned){
		$("header").animate({

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

$('document').ready( function (){
	var iPhraseLength = $('#skull-bubble p').text().length + 1;
	var iFontSize = $('#skull-bubble p').css('font-size');
	$('#skull-bubble').css('opacity', '1');
	iFontSize = window.parseInt(iFontSize);
	iFontSize = iFontSize * 0.5;
	$('.bubble').animate({
		width: (iPhraseLength * 10) +  "px",
		opacity: 1
	}, 500);
	$("#left-nav").mCustomScrollbar();
});

$(window).resize( function () {
	if(document.body.clientWidth < 1000){
		$('#skull-bubble').fadeOut(100);
	} else {
		var iPhraseLength = $('#skull-bubble p').text().length + 1;
		var iFontSize = $('#skull-bubble p').css('font-size');
		iFontSize = window.parseInt(iFontSize);
		iFontSize = iFontSize * 0.5;
		$('#skull-bubble').fadeIn(100);
		$('.bubble').animate({
			width: (iPhraseLength * 10) +  "px",
			opacity: 1
		}, 500);
	}
});
