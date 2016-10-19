<?php
$this->Html->script(array('preview',), array('inline' => false));
$this->assign('title', __('Preview Quiz'));

if (!empty($data['Quiz']['subjects'])) {
    $selectedSubjects = json_decode($data['Quiz']['subjects'], true);
} else {
    $selectedSubjects = array_keys($subjectOptions); // By default all subjects
}

if (!empty($data['Quiz']['classes'])) {
    $selectedClasses = json_decode($data['Quiz']['classes'], true);
} else {
    $selectedClasses = array_keys($classOptions); // By default all classes
}
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
    <div class="col-xs-4 col-md-6 settings-options" style="display: none;">
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('show_result', array('default' => $data['Quiz']['show_result'])); 
                echo $this->Form->label('show_result', __('Show results to the student after finishing the quiz.'));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('anonymous', array('default' => $data['Quiz']['anonymous'])); 
                echo $this->Form->label('anonymous', __('Anonymous participation?'));
            ?>
        </div>
    </div>
    <div class="col-md-3 settings-options" style="display: none;">
        <?php
            echo $this->Form->input('subjects', array(
                'options' => $subjectOptions,
                'div' => array('class' => 'form-group'),
                'class' => 'form-control subjects no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'selected' => $selectedSubjects,
                'label' => false
            ));
        ?>
    </div>
    <div class="col-md-3 settings-options" style="display: none;">
        <?php
            echo $this->Form->input('classes', array(
                'options' => $classOptions,
                'div' => array('class' => 'form-group'),
                'class' => 'form-control classes no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'selected' => $selectedClasses,
                'label' => false
            ));
        ?>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?php
                    echo $this->Form->input('Quiz.name', array(
                        'default' => $data['Quiz']['name'],
                        'placeholder' => __('Name the quiz'),
                        'class' => 'form-control input-lg'
                    ));
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
            $othersQuestionType = array(6, 7, 8); // this categories for others type questions
            foreach ($data['Question'] as $question) {
                
                $choices_number = count($question['Choice']);
                if (!$question['QuestionType']['multiple_choices'] && $choices_number > 1) {
                    for ($i = 1; $i < $choices_number; ++$i) {
                        unset($question['Choice'][$i]);
                    }
                }

                $question['number'] = $i;
                echo $this->element('Quiz/preview/question', $question);
                if (!in_array($question['question_type_id'], $othersQuestionType)) { 
                    // only considered main question for numbering
                    // not others type questions
                    ++$i;
                }
            }
            ?>
            <!--/nocache-->
        </tbody>
    </table>

</div>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="form-group">
            <?php echo $this->Html->link(__('Close'), 'javascript:close_window();' ,array('escape'=>false, 'id' => 'close-window', 'class'=>'btn btn-danger')); ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-6 text-right">
        <div class="form-group">
            <?php if (!empty($this->request->query['redirect_url'])) : ?>
                <a href="<?php echo $this->request->query['redirect_url']; ?>" class="btn btn-success">Back</a>
            <?php else : ?>
                <?php echo $this->Html->link(__('Back'), array('controller' => 'quiz', 'action' => 'shared', 'admin' => true), array('escape'=>false, 'class'=>'btn btn-success')); ?>
            <?php endif; ?>
        </div>
    </div>
</div>


<style type="text/css">
.placeholder {
    border: 1px solid green;
    background-color: white;
    -webkit-box-shadow: 0px 0px 10px #888;
    -moz-box-shadow: 0px 0px 10px #888;
    box-shadow: 0px 0px 10px #888;
    width: 50%;
}
.tile {
    height: 70px;
}
.grid {
    margin-top: 1em;
}
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
.settings-options {
    padding: 0px 50px !important;
}
.settings-options label {
    padding: 0 5px !important;
}
.settings-options .form-group {
    margin-bottom: 0px;
}
</style>