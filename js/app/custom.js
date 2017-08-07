// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating

// requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel

// MIT license

(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']  ||
        window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); },
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());

document.addEventListener("DOMContentLoaded", function() {
    var hotlineHandler = function(index){
        var letter = $(this);
        setTimeout(function(){
            $(letter).css({'animation': 'hotline-hover 0.6s infinite', 'animation-direction': 'alternate'});
        }, 100 * index);
    };

	$('.title-top span').each(hotlineHandler);

	$('.title-shadow span').each(hotlineHandler);

    $('.post-description > pre > code').each(function(){
        $(this).css({ 'overflow-x': 'hidden' });
        $(this).mouseenter(function () {
            original = $(this).parent().width();
            ideal = this.scrollWidth;
            console.log(original, ideal);
            if(original < ideal) {
                $(this).stop().animate({
                    width: ideal,
                });
            }
        }).mouseleave(function () {
            if(original < ideal) {
                $(this).stop().animate({
                    width: original,
                });
            }
        });
    });

    hljs.initHighlightingOnLoad();
});
