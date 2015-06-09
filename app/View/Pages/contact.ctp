<?php
    $this->Html->script('contact', array('inline' => false));
?>
<!-- How it works tabs content -->
<div class="container" id="body-content">
<h2><?php echo __('Contact'); ?></h2>
<p class="text-muted"><?php echo __('MeSTRADA Oy / Pietu Halonen'); ?><br /><?php echo __('040-5866 105'); ?><br /></p>
<?php echo $this->Session->flash('notification'); ?>
<?php echo $this->Session->flash('error'); ?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <h3><?php echo __('Get in Touch'); ?></h3>
        <form class="form-horizontal" style="margin-top:30px;"  method="post" action="<?php echo $this->request->base; ?>/user/contact" id="contactForm" novalidate="novalidate" accept-charset="utf-8">
            <div class="alert alert-danger" id="error-message" style="display: none;"></div>
            <div class="form-group">
                <label for="" class="col-sm-4 control-label"><?php echo __('Your Email'); ?></label>
                <div class="col-sm-7">
                    <input type="email" name='data[email]' class="form-control" id="email" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-4 control-label"><?php echo __('Message'); ?></label>
                <div class="col-sm-7">
                    <textarea name='data[message]' id="message" rows="5" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-7">
                    <button type="submit" id="submitForm" class="btn btn-success btn-lg"><?php echo __('Send Message'); ?></button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <img src="<?php echo $this->request->webroot; ?>img/bg-girl.png" class="img-responsive" />
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>