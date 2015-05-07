<?php
$this->Html->script('answer-table', array(
    'inline' => false
));

$this->Html->css('answer-table', array(
    'inline' => false
));

$this->assign('title', __('Answer Table'));
?>
<form class="form" id="answer-table-filter" method="post">
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?php
            echo $this->Form->input('Filter.daterange', array(
                'options' => array(
                    'all' => 'All',
                    'today' => 'Today',
                    'this_year' => 'This Year',
                    'this_month' => 'This Month',
                    'this_week' => 'This Week',
                ),
                'div' => array('class' => 'form-group'),
                'default' => $data['filter']['daterange'],
                'class' => 'form-control',
                'label' => false
            ));
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?php
            echo $this->Form->input('Filter.class', array(
                'options' => $data['classes'],
                'div' => array('class' => 'form-group'),
                'default' => $data['filter']['class'],
                'class' => 'form-control',
                'label' => false
            ));
            ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <div class="btn-group btn-group-justified">
                    <a href="#" class="btn btn-default active" id="answer-table-overview"><?php echo __('Overview'); ?></a>
                    <a href="#" class="btn btn-primary" id="answer-table-show"><?php echo __('Answers'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="panel panel-default">
    <div id="answer-table">
        <table class="table table-hover table-responsive table-striped">
            <thead>
                <tr>
                    <th><?php echo __('Timestamp'); ?></th>
                    <th><?php echo __('Name'); ?></th>
                    <th><?php echo __('Class'); ?></th>
                    <th><?php echo __('Total Points'); ?></th>
                    <?php $i = 1; foreach ($data['questions'] as $question): ?>
                        <th class="question-collapse">
                            <?php echo $i; ?>
                            . &nbsp;
                            <?php echo $question['Question']['text'] ?>
                        </th>
                        <?php ++$i; endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $question_number = count($data['questions']); ?>

                <?php if (count($data['answers_table']) !== 0): ?>
                    <?php foreach ($data['answers_table'] as $sid => $row): ?>
                        <tr id="student-<?php echo $sid ?>">
                            <td><?php echo $data['students'][$sid]['submitted'] ?></td>
                            <td>
                                <?php echo $data['students'][$sid]['fname'] ?> 
                                <?php echo $data['students'][$sid]['lname'] ?>
                            </td>
                            <td><?php echo $data['students'][$sid]['class'] ?></td>
                            <td>
                                <span id="studentscr-<?php echo $sid; ?>"><?php echo $data['scores'][$sid]; ?></span>/<?php echo $data['max_score'] ?>
                            </td>

                            <?php foreach ($data['questions'] as $question): ?>
                                <?php if (!isset($row[$question['Question']['id']])): ?>            
                                    <td class="question-collapse">
                                        <p class="text-danger">
                                            <span class="label">Not Answered</span>
                                        </p>
                                    </td>
                                <?php else: ?>
                                    <td class="question-collapse"> 
                                        <?php if (empty($row[$question['Question']['id']]['answer'])): ?>
                                            <p class="text-danger">
                                                <span class="label">Not Answered</span>
                                            </p>
                                        <?php else: ?>
                                            <?php if (!$row[$question['Question']['id']]['manual']): ?>
                                                <?php if ($question['Question']['question_type_id'] != 3) : ?>
                                                    <?php if($row[$question['Question']['id']]['answer'] == $data['choices'][$question['Question']['id']]['text']): ?>
                                                        <p class="text-success">
                                                            <?php echo $row[$question['Question']['id']]['answer'] ?>
                                                            &nbsp;
                                                            <?php echo $data['choices'][$question['Question']['id']]['points'] ?>
                                                        </p>                                                      
                                                    <?php else : ?>                                                
                                                        <p class="text-danger">
                                                            <?php echo $row[$question['Question']['id']]['answer'] ?>
                                                        </p>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php
                                                    foreach ($getMutiAnswer as $key => $value) {
                                                        if ($sid == $value[0]) {
                                                            echo '<p>' . $value[1] . '&nbsp;&nbsp;1</p>';
                                                        } 
                                                    } 
                                                    ?>

                                                <?php endif; ?>
                                                <?php else : ?>                                
                                                    <?php echo $row[$question['Question']['id']]['answer'] ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                            
                                        <?php if ($row[$question['Question']['id']]['manual']): ?>
                                            <div class="manual-scoring row <?php echo ($row[$question['Question']['id']]['unmarked'] ? "has-warning" : "") ?>">
                                                <div class="col-md-7 col-xs-7">
                                                    <input 
                                                        placeholder="Rate!" 
                                                        type="number" 
                                                        class="form-control update-score" 
                                                        name="input-score-<?php echo $question['Question']['id'] ?>-student-<?php echo $sid; ?>" 
                                                        <?php
                                                        echo (
                                                        $row[$question['Question']['id']]['unmarked'] ?
                                                                '' :
                                                                "value=\"{$row[$question['Question']['id']]['score']}\"")
                                                        ?>
                                                        max="<?php echo $row[$question['Question']['id']]['points'] ?>"
                                                        />
                                                </div>
                                                <div class="col-md-5 col-xs-2">
                                                    <p class="form-control-static">
                                                        / <?php echo $row[$question['Question']['id']]['points'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo($question_number + 3) ?>" align="center"><?php echo __('No Submission'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-10">
        <?php
        echo $this->Html->link(__('Close'), array('controller' => 'quiz'), array('class' => 'btn btn-primary btn-block'));
        ?>
    </div>
</div>
<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>