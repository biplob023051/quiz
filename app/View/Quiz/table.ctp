<?php
$this->Html->script(array('answer-table', 'jquery.tablesorter.min'), array(
    'inline' => false
));

$this->Html->css('answer-table', array(
    'inline' => false
));

$this->assign('title', __('Answer Table'));
?>
<form class="form" id="answer-table-filter" method="post">
    <div class="row">
        <div class="alert" id="ajax-message" style="display: none"></div>
        <div class="col-md-3 col-xs-12">
            <?php
            echo $this->Form->input('Filter.daterange', array(
                'options' => array(
                    'all' => __('All Time'),
                    'today' => __('Today'),
                    'this_year' => __('This Year'),
                    'this_month' => __('This Month'),
                    'this_week' => __('This Week'),
                ),
                'div' => array('class' => 'form-group'),
                'default' => $filter['daterange'],
                'class' => 'form-control',
                'label' => false
            ));
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?php
            echo $this->Form->input('Filter.class', array(
                'options' => $classes,
                'div' => array('class' => 'form-group'),
                'default' => $filter['class'],
                'class' => 'form-control',
                'label' => false
            ));
            ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <div class="btn-group btn-group-justified">
                    <a href="#" class="btn btn-default" id="answer-table-overview"><?php echo __('Overview'); ?></a>
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
                    <th class="serial sortable"><?php echo __('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?></th>
                    <th class="sortable"><?php echo __('Timestamp'); ?></th>
                    <th class="sortable"><?php echo __('Name'); ?></th>
                    <th class="sortable"><?php echo __('Class'); ?></th>
                    <th class="sortable"><?php echo __('Total Points'); ?></th>
                    <?php $i = 1; foreach ($quizDetails['Question'] as $question): ?>
                        <th class="question-collapse">
                            <?php echo $i; ?>
                            . &nbsp;
                            <?php echo $question['text']; ?>
                        </th>
                    <?php ++$i; endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $sl = 0; if (!empty($quizDetails)) : ?>
                    <?php foreach ($quizDetails['Student'] as $key1 => $value1) : $sl++;  ?>
                        <tr id="student-<?php echo $value1['id']; ?>">
                            <td class="serial">
                                <?php echo $sl; ?>
                                <button type="button" class="btn btn-danger btn-sm delete-answer" id="<?php echo $value1['id']; ?>" title="<?php echo __('Remove answer'); ?>">
                                    <i class="glyphicon trash"></i>
                                </button>
                            </td>
                            <td><?php echo $value1['submitted'] ?></td>
                            <td>
                                <?php echo $value1['lname']; ?>
                                <?php echo $value1['fname']; ?> 
                            </td>
                            <td><?php echo $value1['class']; ?></td>
                            <?php foreach ($quizDetails['Ranking'] as $key2 => $value2) : ?>
                                <?php if ($value1['id'] == $value2['student_id']) : ?>
                                    <td>
                                        <span id="studentscr1-<?php echo $value1['id']; ?>"><?php echo ($value2['score']+0); ?></span>/<?php echo ($value2['total']+0); ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach ($quizDetails['Question'] as $key3 => $value3): ?>
                                <td class="question-collapse">
                                    <?php echo $this->element('Quiz/table', array('value3' => $value3, 'value1' => $value1)); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td><?php echo __('Quiz not taken yet!'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->element('Answer/confirm_delete'); ?>
<div id="prev_data" style="display : none;"><?php echo $studentIds; ?></div>
<div id="quizId" style="display : none;"><?php echo $quizId; ?></div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-8">
        <button type="button" class="btn btn-primary btn-block" id="print"><?php echo __('Print'); ?></button>
    </div>
    <div class="col-xs-12 col-md-2">
        <?php
        echo $this->Html->link(__('Back'), '/', array('class' => 'btn btn-primary btn-block'));
        ?>
    </div>
</div>
<div id="print_div" style="display: none;"></div>
<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>
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