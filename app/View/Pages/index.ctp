<?php
    $this->Html->script('index', array('inline' => false));
?>
<div id="hero-unit">
    <div class="container">
        <h2 class="text-center"><?php echo __('Web test enables a teacher'); ?></h2>
        <h3 class="text-center"><?php echo __('to give a quiz to students quickly and easily'); ?><br /><?php echo __('with their '); ?><span class="text-black"><?php echo __('mobile phones'); ?></span></h3>
        <hr class="invisible" />
        <p class="text-center"><a href="<?php echo $this->request->base; ?>/user/create" class="btn btn-success btn-lg"><?php echo __('Create Account'); ?></a></p>
    </div>
</div>

<!-- How it works tabs content -->
<div class="container" id="body-content">
    <h1 class="text-center"><?php echo __('This is how it works!'); ?></h1>
    <div class="tabpanel">
        <ul class="nav nav-tabs nav-justified">
            <li>
                <h4 class="text-center"><?php echo __('Create a Test'); ?></h4>
                <p class="text-center"><?php echo __('Create a test with one or more questions.'); ?></p>
            </li>
            <li>
                <h4 class="text-center"><?php echo __('Give a Test'); ?></h4>
                <p class="text-center"><?php echo __('Let the students attend the test in the classroom or at home.'); ?></p>
            </li>
            <li>
                <h4 class="text-center"><?php echo __('Check the Result'); ?></h4>
                <p class="text-center"><?php echo __('Check the results as soon as the stundents have attended.'); ?></p>
            </li>
        </ul>


        <div class="tab-content">
            <!-- Create test tab information goes here -->
            <div class="tab-pane active" id="create-test"></div>
            <!-- Create test tab information goes here -->
            <div class="tab-pane" id="give-test"></div>
            <!-- Create test tab information goes here -->
            <div class="tab-pane" id="check-result"></div>
        </div>
    </div>
</div>

<!-- Video -->
<?php if (!empty($home_video)) : ?>
    <div id="home-video">
        <a href="javascript:void(0)" id="play_video"><img src="<?php echo $this->Quiz->getHelpPicture($home_video, 'videos'); ?>" class="img-responsive"></a>
    </div>
    <?php echo $this->element('Page/video_modal', array('home_video' => $home_video)); ?>
<?php else: ?>
    <div id="bg-video"></div>
<?php endif; ?>

<script type="text/javascript">
<?php if (!empty($home_video)) : ?>
    var url_src = <?php echo json_encode($home_video['Help']['url_src']) ?>;
<?php else: ?>
    var url_src = '';
<?php endif; ?>
</script>