<?php
$this->Html->script(array(
    'webquiz',
    'live',
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
    'url' => array('controller' => 'student', 'action' => 'submit', $data['Quiz']['random_id'])
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
                $othersQuestionType = array(6, 7, 8); // this categories for others type questions
                // If value exist
                if (!empty($this->request->data['Answer'])) { 
                    // if answer found
                    $temp = array();
                    $question_count = array();
                    foreach ($this->request->data['Answer'] as $key => $value) {
                        $temp[] = $value['question_id'];
                    }
                    $question_count = array_count_values($temp);
                    
                    foreach ($this->request->data['Answer'] as $key => $value) {
                        if ($question_count[$value['question_id']] < 2) { // Not multiple choice
                            $answered[$value['question_id']] = $value['text'];
                        } else {
                            $answered[$value['question_id']][] = $value['text'];
                        }
                    }

                } elseif ($this->Session->check($this->request->query['runningFor'])) { 
                    // if session found
                    $answered = $this->Session->read($this->request->query['runningFor']); 
                } else {
                    $answered = array();
                }


                foreach ($data['Question'] as $question) {
                    //pr($question);
                    // if answered previuosly and stored on session
                    if (isset($answered[$question['id']])) {
                        $question['given_answer'] = $answered[$question['id']];
                    } else {
                        $question['given_answer'] = '';
                    }

                    $choices_number = count($question['Choice']);
                    if (!$question['QuestionType']['multiple_choices'] && $choices_number > 1) {
                        for ($i = 1; $i < $choices_number; ++$i) {
                            unset($question['Choice'][$i]);
                        }
                    }

                    $question['number'] = $i;
                    echo $this->element('Quiz/live/question', $question);
                    if (!in_array($question['question_type_id'], $othersQuestionType)) { 
                        // only considered main question for numbering
                        // not others type questions
                        ++$i;
                    }
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

<input type="hidden" name="data[Student][id]" id="studentId" value="<?php echo !empty($this->request->data['Student']['id']) ? $this->request->data['Student']['id'] : 0; ?>">

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
    var random_id = <?php echo $quizRandomId ?>;
    // // Browser tab navigation
    // var vis = (function(){
    //     var stateKey, eventKey, keys = {
    //         hidden: "visibilitychange",
    //         webkitHidden: "webkitvisibilitychange",
    //         mozHidden: "mozvisibilitychange",
    //         msHidden: "msvisibilitychange"
    //     };
    //     for (stateKey in keys) {
    //         if (stateKey in document) {
    //             eventKey = keys[stateKey];
    //             break;
    //         }
    //     }
    //     return function(c) {
    //         if (c) document.addEventListener(eventKey, c);
    //         return !document[stateKey];
    //     }
    // })();

    // vis(function(){
    //   if (!vis()) {
    //     alert(lang_strings['browser_switch']);
    //     // return;
    //   } else {
    //     window.btn_clicked = true;
    //     window.location.reload();
    //   }
    // });
    // // Leave page alert
    // window.onbeforeunload = function(){
    //     if(!window.btn_clicked){
    //         return lang_strings['leave_quiz'];
    //     }
    // };
</script>

<style type="text/css">
.modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px; /* Adjusts for spacing */
}

.modal-dialog {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
}
</style>