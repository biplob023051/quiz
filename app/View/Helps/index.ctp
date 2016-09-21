<!-- Help list -->
<div class="well">
    <div role="tabpanel">
        <div class="row">
            <div class="col-sm-3">
                <ul class="nav nav-tabs nav-stacked" role="tablist">
                    <?php $count = 0; // Initialize count ?>
                    <?php foreach ($helps as $parent => $help) : ?>
                        <?php if ($count == 0) : ?>
                            <li class="active"><a href="#<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>" role="tab" data-toggle="tab"><?php echo $parent; ?></a></li>
                        <?php else : ?>
                            <li><a href="#<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>" role="tab" data-toggle="tab"><?php echo $parent; ?></a></li>
                        <?php endif; $count++; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-sm-9">
                <div class="tab-content">
                    <?php $count = 0; // Reset count ?>
                    <?php foreach ($helps as $parent => $help) : ?>
                        <?php if ($count == 0) : ?>
                            <div class="tab-pane fade in active" id="<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>">
                        <?php else : ?>
                            <div role="tabpanel" class="tab-pane" id="<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>">
                        <?php endif; $count++; ?>
                            <h3><?php echo $parent; ?></h3>
                            <div class="panel-group" id="accordion-<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>">
                                <?php foreach ($help as $key => $value) : ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion-<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>" href="#collapseOne-<?php echo $value['Help']['id']; ?>"><?php echo $value['Help']['title']; ?></a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne-<?php echo $value['Help']['id']; ?>" class="panel-collapse collapse <?php if ($key == 0) : ?>in<?php endif; ?>">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?php echo $value['Help']['body']; ?>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <iframe width="100%" height="315" src="https://www.youtube.com/embed/<?php echo $value['Help']['url_src']; ?>" frameborder="0" allowfullscreen></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
