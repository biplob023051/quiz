<?php
$this->Html->script(array(
    'webquiz',
    'live'
        ), array('inline' => false)
);

$this->assign('title', __('Quiz: ') . $data['Quiz']['name']);
?>

<?php echo $this->Session->flash('error'); ?>

<?php
echo $this->Form->create('Student', array(
    'inputDefaults' => array(
        'label' => array('class' => 'sr-only'),
        'div' => array('class' => 'form-group'),
        'class' => 'form-control input-lg',
    ),
    'novalidate' => true,
    'url' => array('controller' => 'student', 'action' => 'submit', $data['Quiz']['id'])
));
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="alert alert-danger" id="error-message" style="display: none;"></div>
        <div class="row">
            <div class="col-xs-12 col-md-4">
                <?php
                echo $this->Form->input('fname', array(
                    'placeholder' => __('First Name')
                ));
                ?>
            </div>
            <div class="col-xs-12 col-md-4">
                <?php
                echo $this->Form->input('lname', array(
                    'placeholder' => __('Last Name')
                ));
                ?>
            </div>
            <div class="col-xs-12 col-md-4">
                <?php
                echo $this->Form->input('class', array(
                    'placeholder' => __('Class')
                ));
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p><?php echo $data['Quiz']['description']; ?></p>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-condensed" id="questions">
            <tbody>
                <?php
                $i = 1;
                foreach ($data['Question'] as $question) {

                    $choices_number = count($question['Choice']);
                    if (!$question['QuestionType']['multiple_choices'] && $choices_number > 1) {
                        for ($i = 1; $i < $choices_number; ++$i) {
                            unset($question['Choice'][$i]);
                        }
                    }

                    $question['number'] = $i;
                    echo $this->element('Quiz/live/question', $question);
                    ++$i;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->element('Quiz/confirm_submit'); ?>
<div class="row">
    <div class="col-xs-12 col-md-3 pull-right">
        <button type="submit" class="btn btn-primary btn-lg btn-block"><?php echo __('Turn in your quiz') ?></button>
    </div>
</div>

<?php echo $this->Form->end(); ?>

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