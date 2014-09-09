<form action="/install/db_settings" id="dbSettingsForm" method="POST">
    <div class="form-group">
        <label>Host:</label>
        <input class="form-control" type="text" name="host" value="<?php $_(isset($host) ? $host : ''); ?>" placeholder="Host" required />
    </div>

    <div class="form-group">
        <label>User:</label>
        <input class="form-control" type="text" name="user" value="<?php $_(isset($user) ? $user : ''); ?>" placeholder="User" required />
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input class="form-control" type="password" name="password" value="<?php $_(isset($password) ? $password : ''); ?>" placeholder="Password" />
    </div>

    <div class="form-group">
        <label>Database Name:</label>
        <input class="form-control" type="text" name="db" value="<?php $_(isset($db) ? $db : ''); ?>" placeholder="Database" required />
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary pull-right">Next Step</button>
    </div>
</form>

<script>
    $(function() {

        jQuery(function($) {
            $('#dbSettingsForm').bootstrapValidator({
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                container: 'tooltip',
                fields: {
                }
            });
        });
    });
</script>