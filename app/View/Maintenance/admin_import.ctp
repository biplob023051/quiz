<?php echo $this->Session->flash('error'); ?>
<?php echo $this->Session->flash('notification'); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php echo $this->Form->create('Maintenance', array(
            'inputDefaults' => array(
                'div' => 'form-group',
                'label' => array(
                    'class' => 'col col-sm-3 control-label'
                ),
                'wrapInput' => 'col col-sm-7',
                'class' => 'form-control'
            ),
            'type' => 'file',
            'novalidate'=>'novalidate'
        )); ?>
    
            <?php
                echo $this->Form->input('user_id', array('type' => 'text', 'label'=>array('text'=>__('User Id')), 'placeholder' => __('Please insert user id')));
                
            ?>
            <div class="form-group">
                <div class="col col-sm-7">
                    <?php echo $this->Form->submit(__('Import'), array(
                        'div' => false,
                        'class' => 'btn btn-primary btn-xlarge'
                    )); ?>                
                </div>
            </div>
            
        <?php echo $this->Form->end(); ?>
    </div>
</div>