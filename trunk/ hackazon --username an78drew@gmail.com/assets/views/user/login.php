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

            <?php if(isset($errorMessage) && !empty($errorMessage)):?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong><?=$errorMessage;?></strong>
                </div>
            <?php endif; ?>


            <div class="row" style="width: 400px; padding-left: 30px;">

                <form role="form" method="POST" action="/user/login" id="loginForm">
                    <div class="row">
                        <div class="form-group">
                            <label for="username">Username or email <span style="color: red">*</span></label>
                            <input type="text" name="username" class="form-control" id="username" value="<?=(isset($username)? $username:null)?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password <span style="color: red">*</span></label>
                            <input type="password" name="password" class="form-control" id="password">
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-lg-12">
                            <input type="hidden" name="save" value="contact">
                            <button type="submit" class="btn btn-primary">Sign in</button>
                            <a href="/facebook"><img src="/images/fb.png"></a>
                            <a href="/twitter"><img src="/images/tw.png"></a>
                        </div>
                    </div>
                </form>

            </div>

                <p><a href="/user/password">Forgot your password?</a></p>
                <p><a href="/user/register">New user?</a></p>

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