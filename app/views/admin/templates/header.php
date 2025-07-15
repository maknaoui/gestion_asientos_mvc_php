<?php 
$PATH = '/';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="es"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="es"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>

    <link rel="icon" type="image/png" href="<?= $PATH ?>assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="<?= $PATH ?>assets/img/favicon-32x32.png" sizes="32x32">
    <title>ButacaYa</title>
    <link rel="stylesheet" href="<?= $PATH ?>bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="<?= $PATH ?>bower_components/kendo-ui/styles/kendo.material.min.css" id="kendoCSS"/>
    <link rel="stylesheet" href="<?= $PATH ?>bower_components/uikit/css/uikit.almost-flat.min.css" media="all">
    <link rel="stylesheet" href="<?= $PATH ?>assets/css/main.min.css?1" media="all">
    <link rel="stylesheet" href="<?= $PATH ?>assets/skins/jquery-ui/material/jquery-ui.min.css">
    <link rel="stylesheet" href="<?= $PATH ?>assets/skins/jtable/jtable.min.css">
    <link rel="stylesheet" href="<?= $PATH ?>assets/css/style.css" media="all">

    <!--[if lte IE 9]>
        <script type="text/javascript" src="bower_components/matchMedia/matchMedia.js"></script>
        <script type="text/javascript" src="bower_components/matchMedia/matchMedia.addListener.js"></script>
        <link rel="stylesheet" href="assets/css/ie.css" media="all">
    <![endif]-->
</head>
<body class="back sidebar_main_open sidebar_main_swipe">
    <!-- main header -->
    <header id="header_main">
        <div class="header_main_content">
            <nav class="uk-navbar">
                                
                <!-- main sidebar switch -->
                <a href="#" id="sidebar_main_toggle" class="sSwitch sSwitch_left">
                    <span class="sSwitchIcon"></span>
                </a>
                
                <div class="uk-navbar-flip">
                    <ul class="uk-navbar-nav user_actions">
                        <li><a href="#" id="full_screen_toggle" class="user_action_icon uk-visible-large"><i class="material-icons md-24 md-light">&#xE5D0;</i></a></li>
                        <li data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
                            <a href="#" class="user_action_image"><img class="md-user-image" src="<?= $PATH ?>assets/img/avatars/avatar_11_tn.png" alt=""/></a>
                            <div class="uk-dropdown uk-dropdown-small">
                                <ul class="uk-nav js-uk-prevent">
                                    <li><a href="<?= $PATH ?>admin/auth/logout">Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="header_main_search_form">
            <i class="md-icon header_main_search_close material-icons">&#xE5CD;</i>
            <form class="uk-form">
                <input type="text" class="header_main_search_input" />
                <button class="header_main_search_btn uk-button-link"><i class="md-icon material-icons">&#xE8B6;</i></button>
            </form>
        </div>
    </header><!-- main header end -->
    <!-- main sidebar -->
    <aside id="sidebar_main">
        
        <div class="sidebar_main_header">
            <div class="sidebar_logo">
                <a href="index.html" class="sSidebar_hide"><img src="<?= $PATH ?>assets/img/logo_main.png?1" alt="" height="50" width="210"/></a>
                <a href="index.html" class="sSidebar_show"><img src="<?= $PATH ?>assets/img/logo_main.png?2" alt="" height="50" width="210"/></a>
            </div>
        </div>
        
        <div class="menu_section">
            <ul>

                <li class="<?= (strpos($currentPath, $PATH.'admin/reserva') === 0) ? 'current_section' : '' ?>" title="Reservas">
                    <a href="<?= $PATH ?>admin/reserva">
                        <span class="menu_icon"><i class="material-icons">&#xE158;</i></span>
                        <span class="menu_title">Reservas</span>
                    </a>
                </li>

                <li class="<?= (strpos($currentPath, $PATH.'admin/evento') === 0) ? 'current_section' : '' ?>" title="Eventos">
                    <a href="<?= $PATH ?>admin/evento">
                        <span class="menu_icon"><i class="material-icons">&#xE53E;</i></span>
                        <span class="menu_title">Eventos</span>
                    </a>
                </li>

                <li class="<?= (strpos($currentPath, $PATH.'admin/tipoevento') === 0) ? 'current_section' : '' ?>" title="Tipos de Eventos">
                    <a href="<?= $PATH ?>admin/tipoevento">
                        <span class="menu_icon"><i class="material-icons">&#xE53E;</i></span>
                        <span class="menu_title">Tipos de Eventos</span>
                    </a>
                </li>

            </ul>
        </div>
    </aside><!-- main sidebar end -->

    <div id="page_content">
        <div id="page_content_inner">