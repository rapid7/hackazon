<?php include __DIR__ . '/../common/start.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php echo $errorMessage; ?></strong>
                        </div>
                    <?php endif; ?>

                    <form role="form" method="post" action="/admin/user/login<?php echo isset($returnUrl) && $returnUrl
                        ? '?return_url=' . $_esc(rawurlencode($returnUrl)) : ''; ?>">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Username/E-mail" name="username" type="text" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <!--div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                </label>
                            </div-->
                            <!-- Change this to a button or input when using this as a form -->
                            <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../common/end.php'; ?>
