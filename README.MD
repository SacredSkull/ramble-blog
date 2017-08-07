#ramble [![Build Status](https://travis-ci.com/SacredSkull/ramble-blog.svg?token=QKzv71dggVPNcG9JsNYR&branch=master)](https://travis-ci.com/SacredSkull/ramble-blog) [![Code Climate](https://codeclimate.com/repos/596a3d4596054a0274001265/badges/9dd2158c3bc7d4762f65/gpa.svg)](https://codeclimate.com/repos/596a3d4596054a0274001265/feed) [![Test Coverage](https://codeclimate.com/repos/596a3d4596054a0274001265/badges/9dd2158c3bc7d4762f65/coverage.svg)](https://codeclimate.com/repos/596a3d4596054a0274001265/coverage)
####ramb(ə)l

    1. talk or write at length in a confused or inconsequential way.

_A blog provider written in PHP - a hero no one needs or deserves or cares about because it's not 2000 anymore._

##About
Ramble is a blog that took too long, written in PHP (coincidence?).

Like most of my projects it uses [Slim 3](https://github.com/slimphp/Slim), [Propel2](https://github.com/propelorm/Propel2) & [Twig](https://github.com/twigphp/Twig) for the heavy lifting.

[Ciconia](https://github.com/kzykhys/Ciconia) (a "new markdown parser for PHP 5.4" _giggles_) handles the markdown rendering and [phpxmlrpc](https://github.com/gggeek/phpxmlrpc/) deals with the XMLRPC (or is it the other way around?).

It's not very generic right now and is really _my_ blog instead of a blog package or anything. Hopefully that won't be
the case for much longer. I hear this PHP and internet thing is really taking off now, so it needs to be ready.

##Why should I use this?
You shouldn't. I'm not even sure I can recommend reading [my actual blog's content](https://sacredskull.net).

Use [Jekyll](https://github.com/jekyll/jekyll), or one of the millions of static generators now available, or WordPress.

I'm a particularly perverse developer that wanted a project, so here we are, I guess. PLEASE SOMEONE STOP ME I BEG YO-

##RE: Laziness
Implements the important bits of the typical blogging APIs - Blogger, MetaWeblog, MovingType & WordPress.

So, yes, it works with [Open Live Writer](https://github.com/OpenLiveWriter/OpenLiveWriter) or the original, if you're strange. It also works with a little known commandline tool called [Charm](http://ljcharm.sourceforge.net/), which I am currently forking.

##Google's Pagespeed module
I like squeezing throughput out of sites that will never see any traffic. So I tried using the PageSpeed module for
Nginx and found that you should disable it for the XMLRPC endpoint (i.e. _/xmlrpc_). 

It screws up the output for clients. My code is flawless and beyond criticism or responsibility, so don't go looking for
a reason there.

`</sarcasm>`
_Error Line 38, Column 10_: **end tag for element "sarcasm" which is not open**