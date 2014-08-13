
    <div class="container">

        <div class="row">

            <div class="col-lg-12">
                <h1 class="page-header">
                    
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/">Home</a>
                    </li>
                    <li class="active">Restore password</li>
                </ol>
            </div>

        </div>

        <?php if(isset($successMessage) && !empty($successMessage)):?>
            <div class="alert alert-success">
                <strong><?=$successMessage;?></strong>
            </div>
        <?php else:?>
            <?php if(isset($errorMessage) && !empty($errorMessage)):?>
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong><?=$errorMessage;?></strong>
                </div>
            <?php endif; ?>

            <div class="section">
                <form role="form" method="post" action="/user/password" id="passwordForm">
                    <div class="form-group">
                        <label for="userEmail">Email address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-6"><button id="loginbtn"  type="submit" class="btn btn-success btn-block btn-lg">Restore</button></div>
                    </div>
                </form>
            </div>

        <?php endif; ?>
    </div>
  <script>

  // When the browser is ready...
  $(function() {

    // Setup form validation on the #register-form element
    $("#passwordForm").validate({
        // Specify the validation rules
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        // Specify the validation error messages
        messages: {
            email: "Please enter a valid email address"
        },

        submitHandler: function(form) {
            form.submit();
        }
    });

  });

  </script>
