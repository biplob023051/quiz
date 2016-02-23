<?php
$this->Html->script('invoice', array(
    'inline' => false
));
$this->assign('title', __('My Quizzes'));
?>

<?php echo $this->Session->flash('notification'); ?>

<div class="row notice">
    <?php if (empty($userPermissions['upgraded'])) : ?>
        <?php
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
        ?>
    <?php endif; ?>
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

    <?php if (!empty($userPermissions['access'])) : ?>
        <div class="col-xa-12 col-md-4 pull-right">
            <form class="form" id="quiz-filter" method="post">
                <?php
                echo $this->Form->input('Quiz.status', array(
                    'options' => $quizTypes,
                    'div' => array('class' => 'form-group'),
                    'default' => $filter,
                    'class' => 'form-control',
                    'label' => false
                ));
                ?>
            </form>    
        </div>
    <?php endif; ?>
</div>
<?php if (!empty($userPermissions['access'])) : ?>
    <!-- Quiz list -->
    <div class="panel panel-default">
        <table class="table">
            <tbody>
                <!--nocache-->
                <?php foreach ($data['quizzes'] as $id => $quiz): ?> 
                    <?php $class = empty($quiz['Quiz']['status']) ? 'incativeQuiz' : 'activeQuiz'; ?>
                    <tr class="<?php echo $class; ?>">
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-quiz" quiz-id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Remove quiz'); ?>">
                                <i class="glyphicon trash"></i>
                            </button>
                            <?php if ($quiz['Quiz']['status']) : ?>
                                <button type="button" class="btn btn-default btn-sm active-quiz" status="<?php echo $quiz['Quiz']['status']; ?>" id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Archive quiz'); ?>">
                                    <i class="glyphicon archive"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-default btn-sm active-quiz" status="<?php echo $quiz['Quiz']['status']; ?>" id="<?php echo $quiz['Quiz']['id']; ?>" title="<?php echo __('Activate quiz'); ?>">
                                    <i class="glyphicon recycle"></i>
                                </button>
                            <?php endif; ?>
                            <?php echo $this->Html->link($quiz['Quiz']['name'], array('action' => 'edit', $quiz['Quiz']['id'])); ?>
                        </td>
                        <td>
                            <?php if ($quiz['Quiz']['status']) : ?>
                                <?php echo $this->Html->link(__("Give test!"), '/quiz/present/' . $quiz['Quiz']['id']); ?>
                            <?php endif; ?>
                            <mark><?php echo $this->Html->link(__("Answers (%s)", $quiz['Quiz']['student_count']), '/quiz/table/' . $quiz['Quiz']['id']); ?></mark>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <!--/nocache-->
        </table>
    </div>
<?php endif; ?>

<?php echo $this->element('Invoice/invoice_send_dialog'); ?>
<?php echo $this->element('Invoice/invoice_success_dialog'); ?>
<?php echo $this->element('Invoice/invoice_error_dialog'); ?>
<?php echo $this->element('Invoice/delete_confirm'); ?>


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