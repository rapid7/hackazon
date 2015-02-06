<div class="context panel panel-red">
    <div class="panel-heading">
        Context (<?php $_($type); ?>) <?php $contextName ? $_(' - ' . $contextName) : null; ?>
    </div>
    <div class="panel-body">
        <?php if ($vulnerabilities): ?>
            <div class="context-vulnerabilities context-subsection">
                <?php echo $vulnerabilities; ?>
            </div>
        <?php endif; ?>

        <?php if ($fields): ?>
            <h3>Fields:</h3>
            <div class="context-fields context-subsection">
                <?php echo $fields; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($children) && $children): ?>
            <h3>Children:</h3>
            <div class="context-children context-subsection">
                <?php echo $children; ?>
            </div>
        <?php endif; ?>
    </div>
</div>