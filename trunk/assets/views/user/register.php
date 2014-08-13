<!-- Page Content -->

<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <h1 class="page-header">Registration
                <small>Join us</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.html">Home</a>
                </li>
                <li class="active">Registration</li>
            </ol>
        </div>

    </div>
    <!-- /.row -->

    <?php if(isset($errorMessage) && !empty($errorMessage)):?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong><?=$errorMessage;?></strong>
        </div>
    <?php endif; ?>

    <!-- Service Paragraphs -->

    <div class="row" >

                    <form role="form" method="post" class="signin" action="/user/login" id="loginForm">
                <h2>Please Sign Up <small>It's free and always will be.</small></h2>
                <hr class="colorgraph">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="First Name" tabindex="1">
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <input type="text" name="last_name" id="last_name" class="form-control input-lg" placeholder="Last Name" tabindex="2">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" name="display_name" id="display_name" class="form-control input-lg" placeholder="Display Name" tabindex="3">
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address" tabindex="4">
                </div>
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="5">
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg" placeholder="Confirm Password" tabindex="6">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <span class="button-checkbox">
                            <button type="button" class="btn" data-color="info" tabindex="7">I Agree</button>
                            <input type="checkbox" name="t_and_c" id="t_and_c" class="hidden" value="1">
                        </span>
                    </div>
                    <div class="col-xs-9 col-sm-9 col-md-9">
                        By clicking <strong class="label label-primary">Register</strong>, you agree to the <a href="#" data-toggle="modal" data-target="#t_and_c_m">Terms and Conditions</a> set out by this site, including our Cookie Use.
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
        
        
        <!--
        <form role="form" method="POST" action="/user/register" id="registerForm">
            <div class="row">
                <div class="form-group">
                    <label for="username">Name <span style="color: red">*</span></label>
                    <input type="text" name="username" class="form-control" id="username" value="<?=(isset($username)? $username:null)?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span style="color: red">*</span></label>
                    <input type="email" name="email" class="form-control" id="email" value="<?=(isset($email)? $email:null)?>">
                </div>
                <div class="form-group">
                    <label for="userphone">Phone Number</label>
                    <input name="user_phone" class="form-control" id="user_phone" value="<?=(isset($user_phone)? $user_phone:null)?>">
                </div>
                <div class="form-group">
                    <label for="password">Password <span style="color: red">*</span></label>
                    <input type="password" name="password" class="form-control" id="password">
                </div>
                <div class="form-group">
                    <label for="cpassword">Confirm Password <span style="color: red">*</span></label>
                    <input type="password" name="cpassword" class="form-control" id="cpassword">
                </div>
                
                <div class="clearfix"></div>
                <div class="form-group col-lg-12">
                    <input type="hidden" name="save" value="contact">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
-->
    </div>
    <!-- /.row -->


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
            email: {
                required: true,
                email: true
            },
            password: "required",
            cpassword: {
                required: true,
                equalTo : '#password'
            }
        },
        // Specify the validation error messages
        messages: {
            username: "Please enter a valid username.",
            email: "Please enter a valid email address"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });

      $("#user_phone").inputmask("+1(999) 999-9999");

  });
  
  </script>