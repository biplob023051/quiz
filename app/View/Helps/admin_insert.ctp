<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('Main Title List'),array('controller'=>'helps','action'=>'titles'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('New Main Title'),array('controller'=>'helps','action'=>'add'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('Helps List'),array('controller'=>'helps','action'=>'index'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
            <li><?php echo $this->Html->link(__('New Help'),array('controller'=>'helps','action'=>'insert'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php echo $this->Form->create('Help', array(
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
                echo $this->Form->input('id');
                echo $this->Form->input('parent_id', array('label'=>array('text'=>__('Main Title')),'options' => $parentsOptions, 'empty' => __('Select One') ));
                echo $this->Form->input('title', array('label'=>array('text'=>__('Title')), 'placeholder' => __('Please insert help title')));
                echo $this->Form->input('sub_title', array('label'=>array('text'=>__('Sub Title')), 'placeholder' => __('Please insert help sub title')));
                echo $this->Form->input('url', array('label'=>array('text'=>__('Youtube Video Url')), 'placeholder' => __('Please insert youtube video url here')));
                
            ?>
            <div class="form-group">
                <div class="col col-sm-7 col-sm-offset-3">
                    <?php if(empty($this->params['url']['redirect_url'])) : ?>
                        <?php echo $this->Html->link(__('BACK'),array('controller'=>'helps','action'=>'index', 'admin' => true),array('class'=>'btn btn-danger'));?>
                    <?php else : ?>
                        <?php echo $this->Html->link(__('BACK'),urldecode($this->params['url']['redirect_url']),array('class'=>'btn btn-danger'));?>
                    <?php endif; ?>
                    <?php echo $this->Form->submit(__('SAVE'), array(
                        'div' => false,
                        'class' => 'btn btn-primary btn-xlarge'
                    )); ?>                
                </div>
            </div>
            
        <?php echo $this->Form->end(); ?>
    </div>
</div>