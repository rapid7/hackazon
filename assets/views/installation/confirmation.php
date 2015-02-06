<p>Please check all parameters.</p>

<h4>Database:</h4>
<table class="table table-striped">
    <tbody>
    <tr><td>Host:</td><td><?php $_($database['host']); ?></td></tr>
    <tr><td>Port:</td><td><?php $_($database['port']); ?></td></tr>
    <tr><td>User:</td><td><?php $_($database['user']); ?></td></tr>
    <tr><td>Password:</td><td>***************</td></tr>
    <tr><td>Database:</td><td><?php $_($database['db']); ?></td></tr>
    </tbody>
</table>

<h4>Email:</h4>
<table class="table table-striped">
    <tbody>
    <tr><td>Type</td><td><?php $_($email['type']); ?></td></tr>
    <?php if ($email['type'] == 'native') { ?>
        <tr><td>Mail Parameters</td><td><?php $_($email['mail_parameters']); ?></td></tr>
    <?php } else if ($email['type'] == 'sendmail') { ?>
        <tr><td>Sendmail Command</td><td><?php $_($email['sendmail_command']); ?></td></tr>
    <?php } else if ($email['type'] == 'smtp') { ?>
        <tr><td>Hostname</td><td><?php $_($email['hostname']); ?></td></tr>
        <tr><td>Port</td><td><?php $_($email['port']); ?></td></tr>
        <tr><td>Username</td><td><?php $_($email['username']); ?></td></tr>
        <tr><td>Password</td><td>***************</td></tr>
        <tr><td>Encryption</td><td><?php $_($email['encryption']); ?></td></tr>
        <tr><td>Timeout</td><td><?php $_($email['timeout']); ?></td></tr>
    <?php } ?>
    </tbody>
</table>

<?php if (isset($configsToAdd) && count($configsToAdd)): ?>
    <div class="alert alert-info">Hackazon can't create all necessary files, so you have to create the following
        files manually and copy indicated content inside of it.
    </div>
    <?php $counter = 0; ?>
    <?php foreach ($configsToAdd as $fileName => $configContent): ?>
        <div>
            <h5><?php $_($fileName); ?></h5>
            <textarea class="form-control" name="config_content" id="config_content_<?php echo ++$counter; ?>" cols="30" rows="10"><?php echo $configContent; ?></textarea>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<form action="/install/confirmation" id="dbSettingsForm" method="POST">
    <a href="/install/email_settings" class="btn btn-primary pull-left">Prev Step</a>
    <div class="form-group">
        <button type="submit" name="confirm" class="btn btn-primary pull-right">Install</button>
    </div>
</form>
