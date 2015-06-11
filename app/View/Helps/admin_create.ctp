<?php
$this->Html->script(array('tinymce/tinymce.min', 'jquery.fineuploader', 'admin-insert-help'), array(
    'inline' => false
));
$this->Html->css('fineuploader', array(
    'inline' => false
));
?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('Site Videos'),array('controller'=>'helps','action'=>'videos'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('Create Videos'),array('controller'=>'helps','action'=>'create'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
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
                echo $this->Form->hidden('photo');
                echo $this->Form->input('type', array('label'=>array('text'=>__('Display Page')),'options' => $siteOptions, 'empty' => __('Select One') ));
                echo $this->Form->input('title', array('label'=>array('text'=>__('Title')), 'placeholder' => __('Please insert video title')));
                echo $this->Form->input('sub_title', array('label'=>array('text'=>__('Sub Title')), 'placeholder' => __('Please insert video sub title')));
                echo $this->Form->input('url', array('label'=>array('text'=>__('Youtube Video Url')), 'placeholder' => __('Please insert youtube video url here')));
                echo $this->Form->input('body', array('label'=>array('text'=>__('Description')), 'placeholder' => __('Description here'), 'type' => 'textarea'));
                
            ?>
            <div class="form-group">
                <label><?php echo __('Photo'); ?></label>
                <img src="<?php echo $this->Quiz->getHelpPicture($this->request->data, 'videos'); ?>" id="item-avatar" class="img_wrapper">
                <div id="select-0" style="margin: 10px 0px 0px 210px;"></div>
            </li>
            <div class="form-group">
                <div class="col col-sm-7 col-sm-offset-3">
                    <?php if(empty($this->params['url']['redirect_url'])) : ?>
                        <?php echo $this->Html->link(__('BACK'),array('controller' => 'helps','action' => 'videos', 'admin' => true),array('class'=>'btn btn-danger'));?>
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

<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>

<script type="text/javascript">
    <?php if (!empty($this->request->data['Help']['id'])) : ?>
        var video_id = <?php echo $this->request->data['Help']['id'] ?>;
    <?php else: ?>
        var video_id = '';
    <?php endif; ?>
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>
