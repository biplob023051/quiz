<!-- Modal -->
<div class="modal fade" id="invoice-dialog" tabindex="-1" role="dialog" aria-labelledby="invoice-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="invoice-dialog-title"><?php echo __('Upgrade Account') ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo __('Upgrade your account. You’ll get access to your upgraded account immediately. You’ll shortly receive an invoice of your purchase in your account’s emaill address.') ?></p>
                <br>
                <div class="row">
                    <div class="col-md-9">
                        <strong><?php echo __('Basic:'); ?></strong> <?php echo __('Create and use quizzes freely without limits.'); ?>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-yellow btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span id="29_package"><?php echo __('29 E/Y'); ?></span></button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-9">
                        <strong><?php echo __('Quiz Bank:'); ?></strong> <?php echo __('In addition to previous, share you own quizzes with other users and save time by using ready-made quizzes in the Quiz Bank.'); ?>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-yellow btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span id="49_package"><?php echo __('49 E/Y'); ?></span></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
            </div>
        </div>
    </div>
</div>