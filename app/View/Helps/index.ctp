<?php
$this->Html->script('jquery.colorbox-min', array(
    'inline' => false
));
?>
<!-- Help list -->
<div class="panel panel-default">

    <table class="table">
        <tbody>
            <!--nocache-->
            <tr>
                <td><a href="javascript:void jQuery.colorbox({html:'<iframe width=420 height=315 src=https://www.youtube.com/embed/9bZkp7q19f0?autoplay=1 frameborder=0 allowfullscreen></iframe>'})"><?php echo __('Getting started'); ?></a></td>
            </tr>
            <tr>
                <td><a href="javascript:void jQuery.colorbox({html:'<iframe width=420 height=315 src=https://www.youtube.com/embed/_3_WgAMXAv4?autoplay=1 frameborder=0 allowfullscreen></iframe>'})"><?php echo __('Use scenarios'); ?></a></td>
            </tr>
            <?php $count = 0; foreach ($helps as $parent => $help): $count++; ?> 
                <tr>
                    <td>
                        <?php echo $count . '. ' . $parent ?>
                        <?php foreach ($help as $key => $value) : ?>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void jQuery.colorbox({html:'<iframe width=420 height=315 src=https://www.youtube.com/embed/<?php echo $value['Help']['url_src']; ?>?autoplay=1 frameborder=0 allowfullscreen></iframe>'})"><?php echo $value['Help']['title']; ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <!--/nocache-->
    </table>
</div>
