<?php
$this->Html->script(array(
    'jquery.zoomooz.min',
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
                foreach ($data['Question'] as $question) {

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
    // document.onmousedown=disableclick;
    // function disableclick(event)
    // {
    //   if(event.button==2)
    //    {
    //      alert(lang_strings['right_click_disabled']);
    //      return false;    
    //    }
    // }
    //biplob
    var vis = (function(){
        var stateKey, eventKey, keys = {
            hidden: "visibilitychange",
            webkitHidden: "webkitvisibilitychange",
            mozHidden: "mozvisibilitychange",
            msHidden: "msvisibilitychange"
        };
        for (stateKey in keys) {
            if (stateKey in document) {
                eventKey = keys[stateKey];
                break;
            }
        }
        return function(c) {
            if (c) document.addEventListener(eventKey, c);
            return !document[stateKey];
        }
    })();

    vis(function(){
      if (!vis()) {
        alert(lang_strings['browser_switch']);
        // return;
      } else {
        window.btn_clicked = true;
        window.location.reload();
      }
    });

    window.onbeforeunload = function(){
        if(!window.btn_clicked){
            return lang_strings['leave_quiz'];
        }
    };
</script>