<!-- Help list -->
<div class="well">
    <div role="tabpanel">
        <div class="row">
            <div class="col-sm-3">
                <ul class="nav nav-tabs nav-stacked" role="tablist">
                    <li class="active"><a href="#getting-started" role="tab" data-toggle="tab">Getting Started</a></li>
                    <?php foreach ($helps as $parent => $help) : ?>
                        <li><a href="#<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>" role="tab" data-toggle="tab"><?php echo $parent; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-sm-9">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="getting-started">
                        <h3><?php echo __('Getting Started'); ?></h3>
                        <div class="panel-group" id="accordion-gs">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-gs" href="#collapseOne">1. What is HTML?</a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. </p>
                                                <p>Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer 
tincidunt. Cras dapibus.</p>
                                            </div>
                                            <div class="col-sm-6">
                                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/lHfjvYzr-3g" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-gs" href="#collapseTwo">2. What is Bootstrap?</a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. </p>
                                                <p>Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer 
tincidunt. Cras dapibus.</p>
                                            </div>
                                            <div class="col-sm-6">
                                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/lHfjvYzr-3g" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($helps as $parent => $help) : ?>
                        <div role="tabpanel" class="tab-pane" id="<?php echo strtolower(preg_replace('/\s+/', '', $parent)) ; ?>">
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
