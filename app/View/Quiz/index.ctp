<?php
$this->Html->script(array('invoice', 'quiz-bank'), array(
    'inline' => false
));
$this->assign('title', __('My Quizzes'));
?>

<?php echo $this->Session->flash('notification'); ?>
<?php echo $this->Session->flash('success'); ?>
<?php echo $this->Session->flash('error'); ?>

<div class="row notice">
<?php 
    if (empty($userPermissions['upgraded'])) { 
        if (empty($userPermissions['request_sent'])) {
            if (!empty($userPermissions['canCreateQuiz'])) {
                echo '<div class="col-xa-12 col-md-8">';
                echo '<div class="form-group text-center">';
                echo '<span class="expire-notice">' . __('Your account will be expired in') . ' <span class="days_left">' . $userPermissions['days_left'] . '</span> ' . __('days.') . '</span>';
                echo '</div>';
                echo '</div>';

                echo '<div class="col-xa-12 col-md-4">';
                echo '<div class="form-group">';
                echo $this->element('Invoice/invoice_button', array('btn_text' => __('Upgrade Account')));
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="col-xa-12 col-md-8">';
                echo '<div class="form-group text-center">';
                echo '<span class="expire-notice">' . __('Your account has expired.') . '</span>';
                echo '</div>';
                echo '</div>';

                echo '<div class="col-xa-12 col-md-4">';
                echo '<div class="form-group">';
                echo $this->element('Invoice/invoice_button', array('btn_text' => __('Upgrade to Create More Quiz')));
                echo '</div>';
                echo '</div>';
            }
        } else {
            if (!empty($userPermissions['canCreateQuiz'])) {
                echo '<div class="col-xa-12 col-md-8">';
                echo '<div class="form-group text-center">';
                echo '<span class="expire-notice">' . __('Your account will be expired in') . ' <span class="days_left">' . $userPermissions['days_left'] . '</span> ' . __('days.') . '</span>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="col-xa-12 col-md-8">';
                echo '<div class="form-group text-center">';
                echo '<span class="expire-notice">' . __('Your account has expired.') . '</span>';
                echo '</div>';
                echo '</div>';
            }
            echo '<div class="col-xa-12 col-md-4">';
            echo '<div class="form-group">';
            echo '<button class="btn btn-primary btn-block" disabled="true"  id="upgrade_account"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . __('Upgrade Pending') . '</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        if (($userPermissions['days_left'] < '31') && (AuthComponent::user('account_level') == 1)) { // if expire date soon for previous paid users
            echo '<div class="col-xa-12 col-md-8">';
            echo '<div class="form-group text-center">';
            echo '<span class="expire-notice">' . __('Your account will be expired in') . ' <span class="days_left">' . $userPermissions['days_left'] . '</span> ' . __('days.') . '</span>';
            echo '</div>';
            echo '</div>';

            echo '<div class="col-xa-12 col-md-4">';
            echo '<div class="form-group">';
            echo $this->element('Invoice/invoice_button', array('btn_text' => __('Upgrade Account')));
            echo '</div>';
            echo '</div>';
        }
    }
?>
</div>

<div class="row">
    <?php if (!empty($userPermissions['canCreateQuiz'])): ?>
        <div class="col-xa-12 col-md-4">
            <div class="form-group">
                <a href="<?php echo $this->Html->url('/quiz/add'); ?>" class="btn btn-primary btn-block">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    <?php echo __('Create New Quiz'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($userPermissions['access']) && !empty($quiz_created)) : ?>
        <div class="col-xa-12 col-md-4 pull-right">
            <form class="form" id="quiz-filter" method="post">
                <div class="form-group">
                    <div class="input select">
                        <select name="data[Quiz][status]" class="form-control" id="QuizStatus">
                            <optgroup label="<?php echo __('QUIZ ARCHIVING'); ?>">
                                <?php 
                                    foreach ($quizTypes as $key => $value) {
                                        if ($key == $filter) {
                                            echo '<option selected="selected" value="'. $key . '">'. $value .'</option>';
                                        } else {
                                            echo '<option value="'. $key .'">'. $value .'</option>';
                                        }
                                    } 
                                ?>
                            </optgroup>
                            <optgroup label="<?php echo __('QUIZ SHARING'); ?>">
                                <?php 
                                    foreach ($quizSharedType as $key => $value) {
                                        if ($key == $filter) {
                                            echo '<option selected="selected" value="'. $key . '">'. $value .'</option>';
                                        } else {
                                            echo '<option value="'. $key .'">'. $value .'</option>';
                                        }
                                    } 
                                ?>
                            </optgroup>
                        </select>
                    </div>                
                </div>
            </form>    
        </div>

    <?php endif; ?>
</div>
<?php if (!empty($userPermissions['access'])) : ?>
    <!-- Quiz list -->
    <div class="panel panel-default" id="user-quizzes">
        <?php if (!empty($data['quizzes'])) : ?>
            <!-- show quiz list -->
            <table class="table">
                <tbody id="quiz-list">
                    <?php foreach ($data['quizzes'] as $id => $quiz): ?> 
                        <?php $class = empty($quiz['Quiz']['status']) ? 'incativeQuiz' : 'activeQuiz'; ?>
                        <tr class="<?php echo $class; ?>">
                            <td style="vertical-align:middle">
                                
                                <div style="width: 40%; float: left">
                                    <?php 
                                        if (empty($quiz['Quiz']['shared'])) {
                                            echo $this->Html->link($quiz['Quiz']['name'], array('action' => 'edit', $quiz['Quiz']['id']) ,array('escape'=>false,'class'=>'quiz-name'));
                                        } else {
                                            if ($quiz['Quiz']['is_approve'] == 2) {
                                                echo $this->Html->link('<i class="glyphicon glyphicon-ban-circle text-danger"></i>&nbsp;' . $quiz['Quiz']['name'], array('action' => 'edit', $quiz['Quiz']['id']) ,array('escape'=>false, 'title' => __('Share declined') ,'class'=>'quiz-name'));
                                            } elseif ($quiz['Quiz']['is_approve'] == 1) {
                                                echo $this->Html->link('<i class="glyphicon glyphicon-share-alt text-success"></i>&nbsp;' . $quiz['Quiz']['name'], array('action' => 'edit', $quiz['Quiz']['id']) ,array('escape'=>false, 'title' => __('Publicly shared') ,'class'=>'quiz-name'));
                                            } else {
                                                echo $this->Html->link('<i class="glyphicon glyphicon-warning-sign text-warning"></i>&nbsp;' . $quiz['Quiz']['name'], array('action' => 'edit', $quiz['Quiz']['id']) ,array('escape'=>false, 'title' => __('Share pending, waiting for admin approval') ,'class'=>'quiz-name'));
                                            } 
                                        }
                                    ?>
                                </div>
                                <div style="width: 60%; float: left">
                                    <?php if ($quiz['Quiz']['status']) : ?>
                                        <?php echo $this->Html->link(__("Give test!"), '/quiz/present/' . $quiz['Quiz']['id']); ?>
                                    <?php endif; ?>
                                    <mark><?php echo $this->Html->link(__("Answers (%s)", $quiz['Quiz']['student_count']), '/quiz/table/' . $quiz['Quiz']['id']); ?></mark>
                                </div>
                               
                            </td>
                            <td align="right">
                                <ul class="nav navbar-nav navbar-right no-margin">
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">
                                            <?php echo __('Actions'); ?> 
                                            <b class="caret"></b>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if (!empty($quiz['Quiz']['question_count'])) : ?>
                                                <li>
                                                    <?php if (empty($quiz['Quiz']['shared'])) : ?>
                                                        <button type="button" class="btn btn-success btn-sm share-quiz same-width-btn" quiz-id="<?php echo $quiz['Quiz']['id']; ?>" quiz-name="<?php echo $quiz['Quiz']['name']; ?>" title="<?php echo __('Share quiz'); ?>"><i class="glyphicon share"></i> <?php echo __('Share quiz'); ?></button>
                                                    <?php else : ?>
                                                        <button type="button" class="btn btn-success btn-sm remove-share same-width-btn" quiz-id="<?php echo $quiz['Quiz']['id']; ?>" quiz-name="<?php echo $quiz['Quiz']['name']; ?>" title="<?php echo __('Cancel share'); ?>"><i class="glyphicon share"></i> <?php echo __('Cancel share'); ?></button>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <button type="button" class="btn btn-danger btn-sm delete-quiz same-width-btn" quiz-id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Remove quiz'); ?>"><i class="glyphicon trash"></i> <?php echo __('Remove quiz'); ?></button>
                                            </li>
                                            <li>
                                                <?php if ($quiz['Quiz']['status']) : ?>
                                                    <button type="button" class="btn btn-default btn-sm active-quiz same-width-btn" status="<?php echo $quiz['Quiz']['status']; ?>" id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Archive quiz'); ?>"><i class="glyphicon archive"></i> <?php echo __('Archive quiz'); ?></button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-default btn-sm active-quiz same-width-btn" status="<?php echo $quiz['Quiz']['status']; ?>" id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Activate quiz'); ?>"><i class="glyphicon recycle"></i> <?php echo __('Activate quiz'); ?></button>
                                                <?php endif; ?>
                                            </li>
                                             <li>
                                                <button type="button" class="btn btn-success btn-sm duplicate-quiz same-width-btn" quiz-id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Duplicate quiz'); ?>"><i class="glyphicon duplicate"></i> <?php echo __('Duplicate quiz'); ?></button>
                                            </li>
                                            <?php if (($quiz['Quiz']['shared'] == 1) && ($quiz['Quiz']['is_approve'] == 2)) : ?>
                                                <li>
                                                    <button type="button" class="btn btn-danger btn-sm view-reason same-width-btn" quiz-id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('View decline reason'); ?>"><i class="glyphicon glyphicon-ban-circle"></i> <?php echo __('View Decline Reason'); ?></button>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <?php echo $this->element('Quiz/decline_reason', array('quiz' => $quiz)); ?>
                    <?php endforeach; ?>
                    <!--nocache-->
                </tbody>
                <!--/nocache-->
            </table>
        <?php else : ?>
            <?php if (empty($quiz_created)) : ?>
                <!-- show dummy data installation module -->
                <div class="row">
                    <div id="demo-data">
                        <div class="col-md-10 col-md-offset-1">
                            <p class="text-center"><?php echo __('Welcome to Verkkotesti!') ?></p>
                            <p class="text-center"><?php echo __('If you want to start by looking at demo quizzes, click the gray button ') . '<b>"' . __('Load demo quizzes') . '"</b>.'; ?></p>
                            <p class="text-center"><?php echo __('(You can delete demo quizzes when you don\'t need them anymore.)'); ?></p>
                            <p class="text-center"><?php echo __('If you want to dive straight in, click the blue button') . '<b> "' . __('Create a New test') . '"</b>.'; ?></p>
                        </div>
                        <div class="col-md-4 col-md-offset-4"><button type="button" class="btn btn-gray btn-block" data-toggle="modal" data-target="#demo-dialog" id="upgrade_account"><span class="glyphicon glyphicon-import" aria-hidden="true"></span><span> <?php echo __('Load demo quizzes'); ?></span></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>
    <?php if (!empty($userPermissions['quiz_bank_access'])) : ?>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-success btn-sm pull-right quiz-bank"  title="<?php echo __('Explore quiz bank'); ?>"><i class="glyphicon glyphicon-briefcase"></i> <?php echo __('Quiz Bank'); ?></button>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php echo $this->element('Invoice/invoice_send_dialog'); ?>
<?php echo $this->element('Invoice/invoice_success_dialog'); ?>
<?php echo $this->element('Invoice/invoice_error_dialog'); ?>
<?php echo $this->element('Invoice/delete_confirm'); ?>
<?php echo $this->element('Invoice/demo_dialog'); ?>

<div class="modal fade" id="public-quiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<div class="modal fade" id="preview-quiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>


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
    @media (min-width: 992px) {
        .modal-v-lg {
            width: 1100px;
            height: 900px; /* control height here */
        }
    }

    #quiz-bank {
        padding: 20px;
    }
    .pbutton {
        max-width: 25px !important;
        width: auto;
    }

    .action-box {
        max-width: 100px !important;
        width: auto;
        min-width: 90px !important;
    } 

    .same-width-btn {
        width: 160px !important;
    } 

    .same-width-btn i {
        top: 3px;
    } 
</style>