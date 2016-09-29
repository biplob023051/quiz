<?php echo $this->Session->flash('error'); ?>
<?php echo $this->Session->flash('success'); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('Class List'),array('controller'=>'standards','action'=>'index'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
            <li><?php echo $this->Html->link(__('New Class'),array('controller'=>'standards','action'=>'insert'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 text-right font-10 font-bold">
                <?php 
                    echo $this->Paginator->counter(
                        'Page {:page} of {:pages}, showing {:current} records out of
                         {:count} total'
                    ); 
                ?>
            </div>
        </div> 
        <br>
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-1"><?php echo $this->Paginator->sort('id', __('Id')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('title', __('Title')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('created', __('Created')); ?></th>
                        <th class="text-center col-md-1"><?php echo $this->Paginator->sort('isactive', __('Status')); ?></th>
                        <th class="text-center col-md-1"><?php echo __('Action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($standards)) : ?>
                        <tr><td colspan="5"><?php echo __('Class not found'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($standards as $standard): ?>
                            <tr>
                                <td class="text-center"><?php echo h($standard['Standard']['id']); ?></td>
                                <td class="text-center"><?php echo h($standard['Standard']['title']); ?></td>
                                <td class="text-center"><?php echo h($standard['Standard']['created']); ?></td>
                                 <td class="text-center" nowrap="nowrap">
                                    <?php if($standard['Standard']['isactive']):?>
                                        <?php echo $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs active">'.__('On').'</button><button type="button" class="btn btn-default btn-xs inactive">'.__('Off').'</button></div>', array('action' => 'active', $standard['Standard']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('escape'=>false), __('Confirm inactive class %s?', h($standard['Standard']['title']))); ?>
                                    <?php else :?>
                                        <?php echo $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs inactive">'.__('On').'</button><button type="button" class="btn btn-default btn-xs active">'.__('Off').'</button></div>', array('action' => 'active', $standard['Standard']['id'],1,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('escape'=>false), __('Confirm active class %s?', h($standard['Standard']['title']))); ?>
                                    <?php endif;?>
                                </td>
                                <td class="text-center" nowrap="nowrap">
                                    <?php echo $this->Html->link(__('Edit'), array('action' => 'insert', $standard['Standard']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                    <?php if(!$standard['Standard']['isactive']):?>
                                        <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $standard['Standard']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-danger btn-xs','escape'=>false), __('Confirm delete of class %s?', trim($standard['Standard']['id']))); ?>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="row">
            <div class="col-md-12 text-center">
                <ul class="pagination pagination-sm">
                    <?php echo $this->Paginator->prev('&larr; ' . __('Previous'),array('tag'=>'li','escape'=>false),'<a>&larr; '. __('Previous') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
                    echo $this->Paginator->numbers(array('tag'=>'li','separator'=>null,'currentClass'=>'active','currentTag'=>'a','modulus'=>'4','first' => 2, 'last' => 2,'ellipsis'=>'<li><a>...</a></li>'));
                    echo $this->Paginator->next(__('Next') . ' &rarr;',array('tag'=>'li','escape'=>false),'<a>&rarr; '. __('Next') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));?>
                </ul>
            </div>
        </div>

        
    </div>
</div>