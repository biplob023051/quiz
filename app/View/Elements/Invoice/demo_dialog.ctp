<!-- Modal -->
<div class="modal fade" id="demo-dialog" tabindex="-1" role="dialog" aria-labelledby="demo-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="invoice-dialog-title"><?php echo __('Would you like to load the demo quizzes?') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo __('Choose Ok if you want to load a couple of demo quizzes to take a look at Verkkotesti features.') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="import"><?php echo __('OK'); ?></button>
            </div>
        </div>
    </div>
</div>