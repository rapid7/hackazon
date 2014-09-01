<div class="container">
    <!-- /.row -->
    <div class="row">
        <form role="form" class="signin" method="POST" action="/user/login<?php echo $returnUrl ? '?return_url=' . rawurlencode($returnUrl) : ''; ?> " id="loginPageForm">
            <h1>Please login <small></small></h1>
            <ol class="breadcrumb">
				      <li><a href="/">Home</a></li>
				      <li class="active">Login</li>
			      </ol>
            <hr class="colorgraph" />
            <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>
                <?= $errorMessage; ?>
              </strong>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="text" maxlength="100" required name="username" class="form-control input-lg" id="username" placeholder="Username or Email" value="<?= (isset($username) ? $_($username, 'username') : null) ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="password" maxlength="100" required name="password" class="form-control input-lg" placeholder="Password" id="password">
                    </div>
                </div>
            </div>
            <hr class="colorgraph">
            <div class="row">
                <div class="col-xs-6 col-md-6"><button id="loginbtn"  type="submit" class="btn btn-success btn-block btn-lg">Sign In</button></div>
                <div class="col-xs-6 col-md-6">
                    <div>
                        <span class="login-social-span">Or login via</span>
                        <ul class="list-unstyled list-inline list-social-icons">

                            <li class="tooltip-social facebook-link"><a href="/facebook" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fa fa-facebook-square fa-4x"></i></a></li>
                            <li class="tooltip-social twitter-link"><a href="/twitter" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter-square fa-4x"></i></a></li>
                        </ul>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><a href="/user/password" class="btn btn-info btn-lg" style="width:100%">Forgot your password?</a></div>
                <div class="col-xs-6 col-md-6"><a href="/user/register" class="btn btn-info btn-lg" style="width:100%">New user?</a></div>
            </div>
        </form>
    </div>
</div>
<!-- /.container -->
<script>
    jQuery(function($) {
        $('#loginPageForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            container: 'tooltip'
        });
    });
</script>
<!-- /.container -->