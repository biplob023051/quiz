<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#"></a>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-right">
                <!--nocache-->
                <?php if ($this->Session->check('Auth.User.name')): ?>
                    <li>
                        <div class="user-image"></div>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <?php echo h($this->Session->read('Auth.User.name')); ?> 
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <?php
                                echo $this->Html->link(__('Settings'), array(
                                    'controller' => 'user',
                                    'action' => 'settings'
                                ));
                                ?>
                            </li>
                        </ul>
                    </li>
                    <li>                            
                        <?php
                        echo $this->Html->link(__('Logout'), array(
                            'controller' => 'user',
                            'action' => 'logout'
                        ));
                        ?>
                    </li>
                <?php endif; ?>
                <!--/nocache-->
            </ul>
        </div>
    </div>
</nav>
