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
            <div id='skull-bubble' class='bubble'>
                <p>{{ skull_greeting|default('*Speechless*') }}</p>
            </div>
        </header>
