<?php
    $c_action = $this->request->action;
    $c_controller = $this->request->controller;
?>
<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo $this->request->base; ?>"></a>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-left">
                <?php if ($this->Session->check('Auth.User.name')): ?>
                    <li <?php if ($c_controller == 'quiz' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('My Quizzes'), '/'); ?></li>
                <?php endif; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if ($this->Session->check('Auth.User.name')): ?>
                    <!--nocache-->
                    <?php if ($this->Session->read('Auth.User.account_level') == 51): ?>
                        <?php $admin_actions = array('admin_titles', 'admin_add', 'admin_insert', 'admin_index'); ?>
                        <li <?php if (in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Create Help'), array('controller' => 'helps', 'action' => 'titles', 'admin' => true)); ?></li>
                    <?php endif ?>
                    <?php if ($this->Session->read('Auth.User.account_level') == 51): ?>
                        <?php $admin_actions = array('admin_videos', 'admin_create'); ?>
                        <li <?php if (in_array($c_action, $admin_actions)) : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Site Videos'), array('controller' => 'helps', 'action' => 'videos', 'admin' => true)); ?></li>
                    <?php endif ?>
                    <li <?php if ($c_controller == 'helps' && $c_action == 'index') : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__('Help'), '/helps'); ?></li>
                    <li>
                        <div class="user-image"></div>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <?php echo h($this->Session->read('Auth.User.name')); ?> 
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('Settings'), '/user/settings'); ?></li>
                        </ul>
                    </li>
                    <li><?php echo $this->Html->link(__('Logout'), '/user/logout'); ?></li>
                    <!--/nocache-->
                <?php else: ?>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>