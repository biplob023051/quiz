<?php $no_access = $this->Quiz->downloadCount(); ?>
<div class="table-responsive">
    <?php if(!empty($no_access)) : ?>
        <div class="col-md-12" id="import-error">
            <h5 class="alert alert-danger"><?php echo __('Sorry, you have exceeded maximum limit of import quiz. Please upgrade your account to get unlimited access on quiz bank!'); ?></h5>
        </div>
    <?php endif; ?>
   
    <table cellpadding="0" cellspacing="0"  class="table table-bordered">
        <thead>
            <tr>
                <th class="pbutton text-center"><?php echo !empty($no_access) ? $this->Form->checkbox('checkbox', array('value'=>'deleteall','name'=>'selectAll','label'=>false,'id'=>'selectAll','hiddenField'=>false, 'disabled' => true)) : $this->Form->checkbox('checkbox', array('value'=>'deleteall','name'=>'selectAll','label'=>false,'id'=>'selectAll','hiddenField'=>false));?></th>
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
                    <td colspan="5"><?php echo __('Shared quizzes not found. Filter the list by choosing subjects and classes.'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td class="pbutton"><?php echo !empty($no_access) ? $this->Form->checkbox(false, array('value' => $quiz['Quiz']['random_id'],'name'=>'data[Quiz][id][]', 'class'=>'chkselect', 'disabled' => true)) : $this->Form->checkbox(false, array('value' => $quiz['Quiz']['random_id'],'name'=>'data[Quiz][id][]', 'class'=>'chkselect'));?></td>
                        <td class="text-center"><?php echo h($quiz['Quiz']['name']); ?></td>
                        <?php
                            $related_subjects = '';
                            if ($quiz['Quiz']['subjects']) {
                                $subjects = json_decode($quiz['Quiz']['subjects'], true);
                                foreach ($subjects as $key => $subject) {
                                    if ($subject == 0) {
                                        $related_subjects .= !empty($subjectOptions[$subject]) ? $subjectOptions[$subject] : '';
                                        break;
                                    } else {
                                        $related_subjects .= !empty($subjectOptions[$subject]) ? $subjectOptions[$subject] . ', ' : '';
                                    }
                                }
                            }
                        ?>
                        <td class="text-center"><?php echo !empty($related_subjects) ? rtrim($related_subjects, ', ') : __('Undefined'); ?></td>
                        <?php
                            $related_classes = '';
                            if ($quiz['Quiz']['classes']) {
                                $classes = json_decode($quiz['Quiz']['classes'], true);
                                foreach ($classes as $key => $class) {
                                    if ($class == 0) {
                                        $related_classes .= !empty($classOptions[$class]) ? $classOptions[$class] : '';
                                        break;
                                    } else {
                                        $related_classes .= !empty($classOptions[$class]) ? $classOptions[$class]  . ', ' : '';
                                    }
                                }
                            }
                        ?>
                        <td class="text-center"><?php echo !empty($related_classes) ? rtrim($related_classes, ', ') : __('Undefined'); ?></td>
                        <td class="text-center"><?php echo $quiz['Quiz']['created']; ?></td>
                        <td class="text-center action-box">
                            <button type="button" class="btn btn-success btn-sm import-quiz"<?php echo !empty($no_access) ? '  disabled' : ''; ?> random-id="<?php echo $quiz['Quiz']['random_id']; ?>" title="<?php echo __('Import Quiz'); ?>"><i class="glyphicon glyphicon-save"></i></button>
                            <button type="button" class="btn btn-success btn-sm view-quiz" random-id="<?php echo $quiz['Quiz']['random_id']; ?>" title="<?php echo __('Preview Quiz'); ?>"><i class="glyphicon glyphicon-search"></i></button>
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
            <button type="button" class="btn btn-success btn-sm multiple-import-quiz"<?php echo !empty($no_access) ? '  disabled' : ''; ?> title="<?php echo __('Import Selected'); ?>"><i class="glyphicon glyphicon-save"></i><?php echo __('Import Selected'); ?></button>
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