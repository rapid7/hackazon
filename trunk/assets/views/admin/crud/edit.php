<div class="panel panel-default">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Return to list</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <?php
        /** @var \App\Admin\FieldFormatter $formatter */
        $formatter->renderForm();
        ?>
    </div>
    <!-- /.panel-body -->
</div>

