<?php require './app/utils.php'; ?>
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

    <title>Admin - Tu Asiento Ya</title>

    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- uikit -->
    <link rel="stylesheet" href="<?= $PATH ?>bower_components/uikit/css/uikit.almost-flat.min.css"/>

    <!-- altair admin login page -->
    <link rel="stylesheet" href="<?= $PATH ?>assets/css/login_page.min.css" />

</head>
<body class="login_page">
    <div class="login_page_wrapper">
        <?php if (!empty($error)): ?>
            <div class="uk-alert uk-alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
                <div class="md-card" id="login_card">
            <div class="md-card-content large-padding" id="login_form">
                <div class="login_heading">
                    <div class="user_avatar"></div>
                </div>
                <form action="/admin/auth/login" method="POST">
                    <div class="uk-form-row">
                        <label for="login_username">Login</label>
                        <input class="md-input" type="text" id="login_username" name="correo" value="maknaoui.yassine@gmail.com" />
                    </div>
                    <div class="uk-form-row">
                        <label for="login_password">Contraseña</label>
                        <input class="md-input" type="password" id="login_password" name="clave" value="Continuum" />
                    </div>
                    <div class="uk-margin-medium-top">
                        <button type="submit" class="md-btn md-btn-primary md-btn-block md-btn-large">Conexión</button>
                    </div>
                    <div class="uk-margin-top">
                        <a href="#" id="login_help_show" class="uk-float-right">Necesitas ayuda?</a>
                    </div>
                </form>
            </div>
            <div class="md-card-content large-padding uk-position-relative" id="login_help" style="display: none">
                <button type="button" class="uk-position-top-right uk-close uk-margin-right uk-margin-top back_to_login"></button>
                <h2 class="heading_b uk-text-success">No puedo iniciar sesión?</h2>
                <p>Esta es la información para volver a su cuenta tan pronto como sea posible.</p>
                <p>En primer lugar, intente lo más fácil: si recuerda su contraseña pero no funciona, asegúrese de que Caps Lock está desactivado y de que su Login está escrito correctamente y vuelva a intentarlo.</p>
                <p>Si su contraseña aún no funciona, es hora de contactar a <a href="https://www.yassine.cl/">Yassine</a>.</p>
            </div>
        </div>
    </div>

    <!-- common functions -->
    <script src="<?= $PATH ?>assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="<?= $PATH ?>assets/js/uikit_custom.min.js"></script>
    <!-- altair core functions -->
    <script src="<?= $PATH ?>assets/js/altair_admin_common.min.js"></script>

    <!-- altair login page functions -->
    <script src="<?= $PATH ?>assets/js/pages/login.min.js"></script>

</body>
</html>
