<?php echo $this->element('Quiz/table_extension', array('quizDetails' => $quizDetails)); ?>
<?php if ($currentTab != 'answer-table-overview') : ?>
    <script type="text/javascript">
        $('.question-collapse').show();
    </script>
<?php endif; ?>