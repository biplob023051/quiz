<?php
$this->Html->script(array(
    /* production */
    //'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0/handlebars.min.js',
    'handlebars.min',
    'jquery.serializejson.min',
    'webquiz',
    'edit',
    //'qunit-1.17.1',
    //'tests/edit'
        ), array('inline' => false)
);
// $this->Html->css(array(
//     'qunit-1.17.1'
//         ), array('inline' => false)
// );
$this->assign('title', __('Edit Quiz'));
?>

<?php echo $this->Session->flash('error'); ?>

<?php
echo $this->Form->create('Quiz', array(
    'inputDefaults' => array(
        'label' => array('class' => 'sr-only'),
        'div' => array('class' => 'form-group'),
        'class' => 'form-control',
    )
));
?>
<!--<div id="qunit"></div>
<div id="qunit-fixture"></div>-->
<div class="row" id="settings">
    <div class="col-xs-12 col-md-12">
        <a href="javascript:void(0)" class="btn btn-default btn-block" id="show-settings">
            <b class="caret"></b>
            <?php echo __('Quiz Settings'); ?>
        </a>
    </div>
    <div class="col-xs-12 col-md-12" id="settings-options" style="display: none;">
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('show_result', array('default' => $data['Quiz']['show_result'])); 
                echo $this->Form->label('show_result', __('Show results to the student after finishing the quiz.'));
            ?>
        </div>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?php
                if (isset($initial)) {
                    echo $this->Form->input('Quiz.name', array(
                        'placeholder' => __('Name the quiz'),
                        'class' => 'form-control input-lg'
                    ));
                } else {
                    echo $this->Form->input('Quiz.name', array(
                        'default' => $data['Quiz']['name'],
                        'placeholder' => __('Name the quiz'),
                        'class' => 'form-control input-lg'
                    ));
                }
                ?>
            </div>
            <div class="col-xs-12 col-md-6">
                <?php
                echo $this->Form->input('Quiz.description', array(
                    'default' => $data['Quiz']['description'],
                    'placeholder' => __('Describe the quiz to respondents'),
                    'class' => 'form-control input-lg'
                ));
                ?>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
    <table class="table table-striped" id="questions">
        <tbody>
            <!--nocache-->
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
                echo $this->element('Quiz/edit/question', $question);
                ++$i;
            }
            ?>
            <!--/nocache-->
        </tbody>
    </table>

</div>

<div class="row">
    <div class="col-xs-12 col-md-3 col-md-offset-6">
        <div class="form-group">
            <button type="button" class="btn btn-primary btn-lg btn-block" id="add-question"><?php echo __('Add New Question') ?></button>

        </div>
    </div>
    <div class="col-xs-12 col-md-3">
        <div class="form-group">
            <input type="submit" class="btn btn-default btn-lg btn-block" id="submit-quiz" value="<?php echo __('Finish'); ?>" />
        </div>
    </div>
</div>

<script id="app-data" type="application/json">
<?php
echo json_encode(array(
    'baseUrl' => $this->Html->url('/', true),
    'questionTypes' => $data['QuestionTypes'],
    'quizId' => $data['Quiz']['id']
));
?>
</script>

<script id="question-preview-template" type="text/x-handlebars-template">
<?php echo $this->element('Quiz/edit/Handlebars/question.preview'); ?>
</script>

<script id="question-edit-template" type="text/x-handlebars-template">
<?php echo $this->element("Quiz/edit/Handlebars/question.edit", $data); ?>
</script>


<?php foreach ($data['QuestionTypes'] as $qt): ?>

    <script id="choice-<?php echo $qt['QuestionType']['template_name'] ?>-edit-template" type="text/x-handlebars-template">
    <?php echo $this->element("Quiz/edit/Handlebars/choice.{$qt['QuestionType']['template_name']}.edit"); ?>
    </script>

    <script id="choice-<?php echo $qt['QuestionType']['template_name'] ?>-preview-template" type="text/x-handlebars-template">
        <?php echo $this->element("Quiz/edit/Handlebars/choice.{$qt['QuestionType']['template_name']}.preview"); ?>
    </script>

<?php endforeach; ?>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

<style type="text/css">
#settings {
    margin: 5px 0px;
}
#show-settings {
    text-align: left;
}
#settings .col-md-12 {
    padding: 0;
    margin: 0;
} 
#settings-options {
    padding: 0px 25px !important;
}
#settings-options label {
    padding: 0 5px !important;
}
</style>