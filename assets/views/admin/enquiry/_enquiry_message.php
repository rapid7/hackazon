<?php
/** @var \App\Model\EnquiryMessage $eMessage */
$authorIsAdmin = $eMessage->author->hasRole('admin');
?>
<li class="<?php echo $authorIsAdmin ? 'right' : 'left'; ?> clearfix">
    <span class="chat-img pull-<?php echo $authorIsAdmin ? 'right' : 'left'; ?>">
        <img class="img-circle" alt="User Avatar" src="http://placehold.it/50/<?php echo $authorIsAdmin ? 'FA6F57' : '55C1E7'; ?>/fff">
    </span>

    <div class="chat-body clearfix">
        <div class="header">
            <strong class="<?php echo $authorIsAdmin ? 'pull-right' : ''; ?> primary-font"><?php $_($eMessage->author->username); ?></strong>
            <small class="<?php echo $authorIsAdmin ? '' : 'pull-right'; ?> text-muted">
                <i class="fa fa-clock-o fa-fw"></i> <?php echo date('m/d/Y H:i', strtotime($eMessage->created_on)); ?>
            </small>
        </div>
        <p><?php echo str_replace("\n", "<br>", $_esc($eMessage->message)); ?></p>
    </div>
</li>