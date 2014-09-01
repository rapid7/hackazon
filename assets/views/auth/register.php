<!-- Page Content -->

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Registration
                <small>Join us</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">Registration</li>
            </ol>
        </div>
    </div>
    <!-- /.row -->
    <!-- Service Paragraphs -->

    <div class="row" style="width: 400px; padding-left: 30px;">

        <form role="form" method="POST" action="/auth/register" id="registerForm">
            <div class="row">
                <div class="form-group">
                    <label for="username">Name</label>
                    <input type="text" name="User[username]" class="form-control" id="username">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="User[email]" class="form-control" id="email">
                </div>
                <div class="form-group">
                    <label for="userphone">Phone Number</label>
                    <input type="text" name="User[userphone]" class="form-control" id="userphone">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="User[password]" class="form-control" id="password">
                </div>
                <div class="form-group">
                    <label for="cpassword">Confirm Password</label>
                    <input type="password" name="cpassworcontact_emaild" class="form-control" id="cpassword">
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
            phone: {
                phone: true
            },
            password: "required",
            cpassword: "required",
            
        },
        // Specify the validation error messages
        messages: {
            username: "Please enter a valid username.",
            email: "Please enter a valid email address",
            
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });

  });
  
  </script>