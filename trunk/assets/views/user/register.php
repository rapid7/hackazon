<!-- Page Content -->

<div class="container">

    <div class="row">
        <div class="col-lg-12">
        </div>
    </div>
    <!-- /.row -->

    <!-- Service Paragraphs -->

    <div class="row" >

        <form role="form" method="post" class="signin" action="/user/register" id="registerForm">
            <h2>Please Sign Up <small>It's free and always will be.</small></h2>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">Registration</li>
            </ol>
            <hr class="colorgraph">
            <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
            <div class="alert alert-danger">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>
                <?= $errorMessage; ?>
              </strong>
            </div>
            <?php endif; ?>
          
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="First Name" tabindex="1" value="<?php $_($first_name, 'first_name'); ?>">
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="text" name="last_name" id="last_name" class="form-control input-lg" placeholder="Last Name" tabindex="2" value="<?php $_($last_name, 'last_name'); ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="text" name="username" id="username" required class="form-control input-lg" placeholder="Username" tabindex="3" value="<?php $_($username, 'username'); ?>">
            </div>
            <div class="form-group">
                <input type="email" maxlength="100" required name="email" id="email" class="form-control input-lg" placeholder="Email Address" tabindex="4" value="<?php $_($email, 'email'); ?>">
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="password" maxlength="100" required name="password" id="password"
                               class="form-control input-lg" placeholder="Password" tabindex="5" value="<?php $_($password, 'password'); ?>">
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="password" maxlength="100" required name="password_confirmation"
                               id="password_confirmation" class="form-control input-lg" placeholder="Confirm Password"
                               tabindex="6" value="<?php $_($password_confirmation, 'password_confirmation'); ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    By clicking <strong class="label label-primary">Register</strong>, you agree to the <a href="/user/terms" >Terms and Conditions</a> set out by this site, including our Cookie Use.
                </div>
            </div>

            <hr class="colorgraph">
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" value="Register" class="btn btn-primary btn-block btn-lg" tabindex="7"></div>
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
            <br/>
            <div class="row">
                <div class="col-xs-6 col-md-6"><a href="/user/password" class="btn btn-info btn-lg" style="width:100%">Forgot your password?</a></div>
                <div class="col-xs-6 col-md-6"><a href="/user/login" class="btn btn-info btn-lg" style="width:100%">Existing User?</a></div>
            </div>
        </form>
    </div>
    <!-- /.row -->
</div>
<!-- /.container -->

<script>
    $(function() {

        jQuery(function($) {
            $('#registerForm').bootstrapValidator({
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                container: 'tooltip',
                fields: {
                    password: {
                        validators: {
                            identical: {
                                field: 'password_confirmation',
                                message: 'The password and its confirm are not the same'
                            }
                        }
                    },
                    password_confirmation: {
                        validators: {
                            identical: {
                                field: 'password',
                                message: 'The password and its confirm are not the same'
                            }
                        }
                    }
                }
            });
        });

       // $("#user_phone").inputmask("+1(999) 999-9999");
    });
</script>