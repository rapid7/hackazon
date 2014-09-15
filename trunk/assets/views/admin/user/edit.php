<div class="panel panel-default">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Return to list</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <?php
        /** @var \App\Admin\FieldFormatter $formatter */
        $formatter->renderFormStart();
        $formatter->renderFields(['id', 'username', 'first_name', 'last_name', 'email']);

        ?>
        <div class="form-group">
        <label for="field_user_phone">Roles</label><br><?php
        foreach ($roles as $roleId => $role) {?>
            <label><input type="checkbox" name="roles[]" value="<?php echo $roleId; ?>"
                 <?php if (array_key_exists($roleId, $userRoles)): ?>checked <?php endif; ?>/> <?php echo $role; ?></label>
            <?php
        } ?>
        </div><?php

        $formatter->renderFields();
        $formatter->renderSubmitButtons();
        $formatter->renderFormEnd();
        ?>
    </div>
    <!-- /.panel-body -->
</div>

