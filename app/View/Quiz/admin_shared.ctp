<?php
$this->Html->script(array('admin-shared'), array(
    'inline' => false
));
$this->assign('title', __('Shared Quizzes'));
?>

<?php echo $this->Session->flash('notification'); ?>
<?php echo $this->Session->flash('success'); ?>
<?php echo $this->Session->flash('error'); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xa-12 col-md-4 pull-right">
                <form class="form" id="quiz-filter" method="post">
                    <?php
                    echo $this->Form->input('Quiz.is_approve', array(
                        'options' => array('all' => __('All'), '3' => __('Pending Quizzes'), '1' => __('Approved Quizzes'), '2' => __('Decline Quizzes')),
                        'div' => array('class' => 'form-group'),
                        'default' => $filter,
                        'class' => 'form-control',
                        'label' => false
                    ));
                    ?>
                </form>    
            </div>
        </div>
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
        <!-- Quiz list -->
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="sl-no"><?php echo __('Sl No');?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('name', __('Name')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('User.name', __('Created By')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('created', __('Created')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('is_approve', __('Status')); ?></th>                
                        <th class="text-center action-box"><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody id="quiz-list">
                    <?php $sr_no=(($this->request->params['paging']['Quiz']['page']-1) * $this->request->params['paging']['Quiz']['limit']+1); ?>
                    <?php if (!empty($quizzes)) : ?>
                        <?php foreach ($quizzes as $id => $quiz): ?> 
                            <tr>
                                <td class="valign-center"><?php echo $sr_no++;?></td>
                                <td class="text-center valign-center">
                                   <?php 
                                        if (empty($quiz['Quiz']['shared'])) {
                                            echo h($quiz['Quiz']['name']);
                                        } else {
                                            if ($quiz['Quiz']['is_approve'] == 2) {
                                                echo '<i class="glyphicon glyphicon-ban-circle text-danger"></i>&nbsp;' . h($quiz['Quiz']['name']);
                                            } elseif ($quiz['Quiz']['is_approve'] == 1) {
                                                echo '<i class="glyphicon glyphicon-share-alt text-success"></i>&nbsp;' . h($quiz['Quiz']['name']);
                                            } else {
                                                echo '<i class="glyphicon glyphicon-warning-sign text-warning"></i>&nbsp;' . h($quiz['Quiz']['name']);
                                            } 
                                        }
                                    ?>
                                </td>
                                <td class="text-center valign-center">
                                    <?php 
                                        echo $quiz['User']['name'];
                                    ?>
                                </td>
                                <td class="text-center valign-center"><?php echo h($quiz['Quiz']['created']); ?></td>
                                <td class="text-center valign-center">
                                    <?php
                                        if (empty($quiz['Quiz']['is_approve'])) {
                                            echo '<span class="">' . __('Pending') . '</span>';
                                        } else if ($quiz['Quiz']['is_approve'] == 1) {
                                            echo '<span class="text-success">' . __('Approved') . '</span>';
                                        } else if ($quiz['Quiz']['is_approve'] == 2) {
                                            echo '<span class="text-danger">' . __('Declined') . '</span>';
                                        } else {
                                            // Do nothing
                                        }
                                    ?>
                                </td>
                                <td class="text-center action-box">
                                    <button type="button"<?php echo ($quiz['Quiz']['is_approve'] == 1) ? 'disabled=true' : ''; ?> class="btn btn-success btn-sm approve-quiz" quiz-name="<?php echo h($quiz['Quiz']['name']); ?>" random-id="<?php echo $quiz['Quiz']['random_id']; ?>" title="<?php echo __('Approve Quiz'); ?>"><i class="glyphicon glyphicon-ok"></i></button>
                                    <button type="button"<?php echo ($quiz['Quiz']['is_approve'] == 2) ? 'disabled=true' : ''; ?> class="btn btn-danger btn-sm decline-quiz" quiz-name="<?php echo h($quiz['Quiz']['name']); ?>" random-id="<?php echo $quiz['Quiz']['random_id']; ?>" title="<?php echo __('Decline Quiz'); ?>"><i class="glyphicon glyphicon-remove"></i></button>
                                    <?php echo $this->Html->link('<i class="glyphicon glyphicon-fullscreen"></i>', array('action' => 'preview', $quiz['Quiz']['id'], '?' => array('redirect_url' => $this->request->here)) ,array('escape'=>false,'class'=>'btn btn-success btn-sm', 'target' => 'blank', 'title' => __('Preview Quiz'))); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6"><?php echo __('No one shared any quiz yet!'); ?></td></tr>
                    <?php endif; ?>
                    <!--nocache-->
                </tbody>
                <!--/nocache-->
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

<?php echo $this->element('common-modal'); ?>

<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>
<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>
<style type="text/css">
    .sl-no {
        max-width: 32px !important;
        min-width: 32px !important;
        width: auto;
    }

    .action-box {
        max-width: 150px !important;
        width: auto;
        min-width: 120px !important;
    } 

    .table>tbody>tr>td.valign-center {
        vertical-align: middle;
    }
</style>