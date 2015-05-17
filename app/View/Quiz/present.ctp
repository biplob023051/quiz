<?php 
    $url = $this->Html->url(array('controller' => $id), true); 
    $this->assign('title', $quiz['Quiz']['name']);
?>
<div class="row">
    <div class="col-md-6 col-xs-12">
        <ul class="present">
            <li><?php echo '1. ' . __('Read the QR code with your mobile service') ?></li>
            <li class="qr-image"><img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=<?php echo $url ?>" /></li>
            <li><?php echo __('OR') ?></li>
            <li><?php echo '2. ' . __('Surf to this web address:') ?></li>
            <li><p class="bg-info"><a href="<?php echo $url ?>"><?php echo $url ?></a></p></li>
        </ul>
    </div>
    <div class="col-md-6 col-xs-12 qr-image" align="center">
        
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