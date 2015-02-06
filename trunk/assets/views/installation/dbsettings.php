<form action="/install/db_settings" id="dbSettingsForm" method="POST">
    <div class="form-group">
        <label>Host:</label>
        <input class="form-control" type="text" name="host" value="<?php $_(isset($host) ? $host : ''); ?>" placeholder="Host" required />
    </div>

    <div class="form-group">
        <label>Port:</label>
        <input class="form-control" type="text" name="port" value="<?php $_(isset($port) ? $port : ''); ?>" placeholder="Port" required />
    </div>

    <div class="form-group">
        <label>User:</label>
        <input class="form-control" type="text" name="user" value="<?php $_(isset($user) ? $user : ''); ?>" placeholder="User" required />
    </div>

    <div class="form-group">
        <?php $useExistingPassword = isset($use_existing_password) && $use_existing_password; ?>
        <label for="password_field">Password:</label>
        <input class="form-control<?php $_(isset($password) && $password ? ' js-has-existing' : ''); ?>"
               type="password" name="password" value="" placeholder="Password" id="password_field"
                <?php echo $useExistingPassword ? "disabled" : ""; ?> /><br>
        <label><input type="checkbox" name="use_existing_password" class="js-use-existing-password"
                <?php echo $useExistingPassword ? "checked" : ""; ?> />
            Use existing password</label><br>
    </div>

    <div class="form-group">
        <label>Database Name:</label>
        <input class="form-control" type="text" name="db" value="<?php $_(isset($db) ? $db : ''); ?>" placeholder="Database" required /><br>
        <label><input type="checkbox" name="create_if_not_exists"
                <?php if (isset($create_if_not_exists) && $create_if_not_exists) { echo "checked"; } ?> />
            Create if DB does not exist</label>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary pull-right">Next Step</button>
    </div>
</form>

<script>
    $(function() {

        jQuery(function($) {
            var $settingsForm = $('#dbSettingsForm');
            $settingsForm.hzBootstrapValidator({});

            var $passwordField = $('#password_field'),
                $useExistingPw = $('.js-use-existing-password');

            $useExistingPw.on('change', function (ev) {
                if ($useExistingPw.is(':checked')) {
                    $passwordField.attr('disabled', 'disabled');
                } else {
                    $passwordField.removeAttr('disabled');
                }
                $settingsForm.data('bootstrapValidator').resetForm();
            });
            $useExistingPw.trigger('change');
        });
    });
</script>