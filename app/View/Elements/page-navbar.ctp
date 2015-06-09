<?php
    $c_action = $this->request->action;
    $c_controller = $this->request->controller;
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
                <span class="sr-only"><?php echo __('Navigation'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $this->request->base; ?>"></a>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-left">
                <li <?php if ($c_controller == 'pages' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Overview'), '/'); ?></li>
                <li <?php if (isset($current_page) && ($current_page == 'prices')) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Prices'), array('controller' => 'pages', 'action' => 'prices')); ?></li>
                <li <?php if (isset($current_page) && ($current_page == 'contact')) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Contact'), array('controller' => 'pages', 'action' => 'contact')); ?></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo $this->request->base; ?>/user/create"><?php echo __('If you don’t have account?') . ' '; ?><span class="text-primary"><?php echo __('Register Now!'); ?></span></a></li>
                <li><a href="<?php echo $this->request->base; ?>/user/login" style="padding-top:8px; padding-bottom:0" ><button type="button" class="btn btn-success"><?php echo __('Login'); ?></button></a></li>
            </ul>
        </div>
    </div>
</nav>