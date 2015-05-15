<?php $url = $this->Html->url(array('controller' => $id), true); ?>
<div class="row">
    <div class="col-md-6 col-xs-12">
        <ol>
            <li><?php echo __('Read the QR code with your mobile service') ?></li>
            <?php echo __('OR') ?>
            <li><?php echo __('Surf to this web address:') ?> 
                <p class="bg-info"><a href="<?php echo $url ?>"><?php echo $url ?></a></p>
            </li>
        </ol>
    </div>
    <div class="col-md-6 col-xs-12 qr-image" align="center">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=<?php echo $url ?>" />
    </div>
</div>
<br/>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-10">
        <?php
        echo $this->Html->link(__('Back'), '/', array('class' => 'btn btn-primary btn-block'));
        ?>
    </div>
</div>