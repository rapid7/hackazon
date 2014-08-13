<!-- Page Content -->

<div class="container">

    <!-- Page Content -->

    <div class="container">

        <div class="row">

            <div class="col-lg-12">
                <h1 class="page-header">Login
                    <small>Sign in</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a>
                    </li>
                    <li class="active">Login</li>
                </ol>
            </div>

        </div>
        <!-- /.row -->

        <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong><?= $errorMessage; ?></strong>
            </div>
        <?php endif; ?>


        <div class="row">
            <form role="form" method="POST" action="/user/login<?php echo $returnUrl ? '?return_url=' . rawurlencode($returnUrl) : ''; ?> " id="loginForm">
                <h2>Please login <small></small></h2>
                <hr class="colorgraph">

                <div class="form-group">
                    <label for="username">Username or email <span style="color: red">*</span></label>
                    <input type="text" name="username" class="form-control" id="username" value="<?= (isset($username) ? $username : null) ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password <span style="color: red">*</span></label>
                    <input type="password" name="password" class="form-control" id="password">
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

        // When the browser is ready...
        $(function() {

            // Setup form validation on the #register-form element
            $("#registerForm").validate({
                // Specify the validation rules
                rules: {
                    username: "required",
                    password: "required"
                },
                // Specify the validation error messages
                messages: {
                    username: "Please enter a password or email.",
                    password: "Please enter a password"
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });

    </script>
    <!-- /.container -->