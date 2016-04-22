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
                if (empty($quizDetails['Quiz']['anonymous'])) {
                    echo $this->Form->input('Filter.class', array(
                        'options' => $classes,
                        'div' => array('class' => 'form-group'),
                        'default' => $filter['class'],
                        'class' => 'form-control',
                        'label' => false
                    ));
                }
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
    <?php echo $this->element('Quiz/table_extension', array('quizDetails' => $quizDetails)); ?>    
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
    var onlineStds = <?php echo json_encode($onlineStds) ?>;
</script>
