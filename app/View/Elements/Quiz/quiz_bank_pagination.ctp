<?php
    // // Inside the view
    // // If necessary, set the jQuery object for noconflict
    // $this->Js->JqueryEngine->jQueryObject = 'jQuery';

    // // Paginator options
    // $this->Paginator->options(array(
    //   'update' => '#pagination_content',
    //   'evalScripts' => true, 
    // )); 

    // $this->Paginator->options['url'] = array('controller' => 'quiz', 'action' => 'quiz_bank_pagination');
?>

<div class="table-responsive">
    <table cellpadding="0" cellspacing="0"  class="table table-bordered">
        <thead>
            <tr>
                <th class="pbutton text-center"><?php echo $this->Form->checkbox('checkbox', array('value'=>'deleteall','name'=>'selectAll','label'=>false,'id'=>'selectAll','hiddenField'=>false));?></th>
                <th class="text-center" id="name-sort">
                    <?php if (!empty($order_field) && ($order_field == 'name') && !empty($order_type)) : ?>
                        <a href="javascript:void(0)" data-rel="<?php echo $order_type; ?>"><?php echo __('Name'); ?></a>
                    <?php else : ?>
                        <a href="javascript:void(0)" data-rel="asc"><?php echo __('Name'); ?></a>
                    <?php endif; ?>
                </th>
                <th class="text-center"><?php echo __('Subjects'); ?></th>
                <th class="text-center"><?php echo __('Classes'); ?></th>
                <th class="text-center" id="created-sort">
                    <?php if (!empty($order_field) && ($order_field == 'created') && !empty($order_type)) : ?>
                        <a href="javascript:void(0)" data-rel="<?php echo $order_type; ?>"><?php echo __('Created'); ?></a>
                    <?php else : ?>
                        <a href="javascript:void(0)" data-rel="asc"><?php echo __('Created'); ?></a>
                    <?php endif; ?>
                    
                </th>
                <th class="text-center action-box"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quizzes)) : ?>
                <tr>
                    <td colspan="5"><?php echo __('Public quiz has not found. You can filter the list by choosing subjects and classes.'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td class="pbutton"><?php echo $this->Form->checkbox(false, array('value' => $quiz['Quiz']['random_id'],'name'=>'data[Quiz][id][]', 'class'=>'chkselect'));?></td>
                        <td class="text-center"><?php echo h($quiz['Quiz']['name']); ?></td>
                        <?php
                            $related_subjects = '';
                            if ($quiz['Quiz']['subjects']) {
                                $subjects = json_decode($quiz['Quiz']['subjects'], true);
                                foreach ($subjects as $key => $subject) {
                                    $related_subjects .= !empty($subjectOptions[$subject]) ? $subjectOptions[$subject] . ', ' : '';
                                }
                            }
                        ?>
                        <td class="text-center"><?php echo !empty($related_subjects) ? rtrim($related_subjects, ', ') : __('Undefined!'); ?></td>
                        <?php
                            $related_classes = '';
                            if ($quiz['Quiz']['classes']) {
                                $classes = json_decode($quiz['Quiz']['classes'], true);
                                foreach ($classes as $key => $class) {
                                    $related_classes .= !empty($classOptions[$class]) ? $classOptions[$class]  . ', ' : '';
                                }
                            }
                        ?>
                        <td class="text-center"><?php echo !empty($related_classes) ? rtrim($related_classes, ', ') : __('Undefined'); ?></td>
                        <td class="text-center"><?php echo $quiz['Quiz']['created']; ?></td>
                        <td class="text-center action-box">
                            <button type="button" class="btn btn-success btn-sm import-quiz" random-id="<?php echo $quiz['Quiz']['random_id']; ?>" title="<?php echo __('Import Quiz'); ?>"><i class="glyphicon glyphicon-save"></i></button>
                            <button type="button" class="btn btn-success btn-sm view-quiz" random-id="<?php echo $quiz['Quiz']['random_id']; ?>" title="<?php echo __('View Quiz'); ?>"><i class="glyphicon glyphicon-fullscreen"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if (!empty($quizzes)) : ?>
    <div class="row">
        <div class="col-md-10 col-offset-md-2">
            <button type="button" class="btn btn-success btn-sm multiple-import-quiz" title="<?php echo __('Import'); ?>"><i class="glyphicon glyphicon-save"></i><?php echo __('Import Selcted'); ?></button>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-md-12 text-center">
        <ul class="pagination pagination-sm">
            <?php echo $this->Paginator->prev('&larr; ' . __('Previous'),array('tag'=>'li','escape'=>false),'<a>&larr; '. __('Previous') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
            echo $this->Paginator->numbers(array('tag'=>'li','separator'=>null,'currentClass'=>'active','currentTag'=>'a','modulus'=>'4','first' => 2, 'last' => 2,'ellipsis'=>'<li><a>...</a></li>'));
            echo $this->Paginator->next(__('Next') . ' &rarr;',array('tag'=>'li','escape'=>false),'<a>&rarr; '. __('Next') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));?>
        </ul>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>