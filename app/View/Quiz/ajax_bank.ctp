<div class="modal-dialog modal-v-lg">
    <div class="modal-content">
        <div class="modal-header">
            <?php echo __('Public Quizzes'); ?>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <?php 
                        echo $this->Form->input('subjects', array(
                            'options' => $subjectOptions,
                            'div' => array('class' => 'form-group'),
                            'class' => 'subjects no-border',
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'selected' => empty($selectedSubjects) ? array_keys($subjectOptions) : $selectedSubjects,
                            'label' => false
                        ));
                    ?>
                </div>
                <div class="col-md-6">
                    <?php 
                        echo $this->Form->input('classes', array(
                            'options' => $classOptions,
                            'div' => array('class' => 'form-group'),
                            'class' => 'classes no-border',
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'selected' => array_keys($classOptions),
                            'label' => false
                        ));
                    ?>
                </div>
            </div>

            <div class="row" id="alert-box" style="display: none;">
                <div class="alert alert-success">
                    <span class="close" onclick="this.parentElement.style.display='none';">&times;</span> 
                    Quiz imported successfully
                </div>
            </div>
        
            <div class="row" id="pagination_content">
                <?php echo $this->element('Quiz/quiz_bank_pagination'); ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
        </div>
    </div>
</div>