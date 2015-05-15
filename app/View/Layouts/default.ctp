<?php 
    if ($this->request->action != 'ajax_update') : 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <body>
        <div class="container">
            <?php echo $this->element('navbar');?>
            <div class="page-header">
                <h1><?php echo $this->fetch('title'); ?></h1>
            </div>
            <?php echo $this->fetch('content'); ?>
        </div>
        <!-- /container -->

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
    </body>
</html>
<?php else: ?>
    <?php echo $this->fetch('content'); ?>
<?php endif ?>