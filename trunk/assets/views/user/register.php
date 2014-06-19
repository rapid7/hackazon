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

    <div class="row" style="width: 400px; padding-left: 30px;">

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