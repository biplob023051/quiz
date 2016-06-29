<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google" value="notranslate">
        <?php echo $this->fetch('meta'); ?>

        <title><?php echo $this->fetch('title'); ?></title>
        <?php echo $this->Html->meta('favicon.ico', '/img/favicon.ico', array('type' => 'icon')); ?>

        <!-- Google Fonts -->
        <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css' />
        <?php
        echo $this->Html->css(array(
            /* production */
            //'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css',
            'bootstrap.min',
            'style',
        ));
        echo $this->fetch('css');
        ?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body <?php if ($this->request->action == 'login') : ?>style="padding-top:50px;" class="bg-cover"<?php else : ?>style="background:#ffffff; padding-top:50px;"<?php endif; ?>>
        <?php 
            if (!empty($setting['visible']) && empty($setting['offline_status'])) {
                echo $this->element('Maintenance/alert');
            }
        ?>
        <?php if ($this->request->controller != 'pages') : ?>
            <div class="container" id="maintenance-alert">
                <?php if ($this->Session->check('Auth.User.name')): ?>
                    <?php echo $this->element('navbar');?>
                <?php else : ?>
                    <?php echo $this->element('page-navbar');?>
                <?php endif; ?>
                <div class="page-header">
                    <h1><?php echo $this->fetch('title'); ?></h1>
                </div>
                <?php echo $this->fetch('content'); ?>
            </div>
            <!-- /container -->
        <?php else : ?>
            <?php echo $this->element('page-navbar');?>
            <?php echo $this->fetch('content'); ?>
        <?php endif; ?>
        <div id="footer">
            <div class="container"></div>
        </div>

        <?php
        echo $this->Html->script(array(
            /* production */
            //'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
            //'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'
            'jquery.min.js',
            'bootstrap.min.js'
        ));
        ?>
        <?php echo $this->fetch('script'); ?>
        <?php echo $this->element('google-analytics'); ?>
    </body>
</html>