<?php
if (is_array($message)):
    foreach ($message as $m):
        ?>
        <div class="alert alert-info" role="alert"><?php echo h($m); ?></div>
    <?php endforeach;
else: ?>
    <div class="alert alert-info" role="alert"><?php echo h($message); ?></div>
<?php endif ?>