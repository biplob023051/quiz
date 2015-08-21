<tr id="q{{id}}">
    <td>   
        <div class="row">
            <div class="col-xs-12 col-md-6">         
                <p>
                    <span class="h4">{{text}}</span><br />
                    <span class="text-muted">{{explanation}}</span>
                </p>
                {{#if warn_message}}
                    <p class="alert alert-warning" style="margin-bottom: 0px;">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <strong><?php echo __('Warning!'); ?></strong> <?php echo __("You didn't put any point"); ?>
                    </p>
                {{/if}}
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="btn-group preview-btn">
                    <button type="button" class="btn btn-default btn-sm edit-question" id="edit-q{{id}}" title="<?php echo __('Edit question'); ?>">
                        <i class="glyphicon pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm delete-question" id="delete-q{{id}}" title="<?php echo __('Remove question'); ?>">
                        <i class="glyphicon trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="choices">
            {{#choice Choice}}
            {{choice_tpl}}
            {{/choice}}
        </div>
    </td>
</tr>