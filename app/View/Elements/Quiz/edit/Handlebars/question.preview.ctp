{{#if relatedClass}}
    {{#if showQuestionText}}
        <tr id="q{{id}}" class="others_type header_type">
    {{else}}
        <tr id="q{{id}}" class="others_type">
    {{/if}}
{{else}}
    <tr id="q{{id}}">
{{/if}}
    <td>   
        <div class="row">
            <div class="col-xs-12 col-md-6">         
                <p>
                    {{#if relatedClass}}
                        {{#if showQuestionText}}
                            <span class="h4 {{relatedClass}}">{{text}}</span>
                            <br />
                        {{/if}}
                    {{else}}
                        <span class="h4"><span class="question_number">{{question_number}}</span>. {{text}}</span>
                        <br />
                    {{/if}}
                    <span class="text-muted">{{explanation}}</span>
                    {{#if max_allowed}}
                        <br />
                        <span class="text-muted">
                            <strong>
                                <?php echo __('Maximum checks'); ?>
                            </strong>
                            {{max_allowed}}
                        </span>
                    {{/if}}
                </p>
                {{#if warn_message}}
                    <p class="alert alert-warning" style="margin-bottom: 0px;">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <strong><?php echo __('Notice: '); ?></strong> <?php echo __("you inserted 0 points in all of the choices."); ?>
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