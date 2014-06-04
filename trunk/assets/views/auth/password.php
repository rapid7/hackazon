
    <div class="container">

        <div class="row">

            <div class="col-lg-12">
                <h1 class="page-header">Restore password
                    
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/">Home</a>
                    </li>
                    <li class="active">Restore password</li>
                </ol>
            </div>

        </div>

        
        <?php if (isset($this->errorMessage)):  ?>
            <div class="panel panel-default">
                <?php echo $this->errorMessage; ?>
            </div>
        <?php endif; ?>

        <div class="section">
                <form role="form" method="post" action="/auth/password" id="passwordForm">
                  <div class="form-group">
                    <label for="userEmail">Email address</label>
                    <input type="email" class="form-control" name="userEmail" id="userEmail" placeholder="Enter email">
                  </div>
                  <button type="submit" class="btn btn-default">Submit</button>
                </form>
        </div>
    </div>
  <script>
  
  // When the browser is ready...
  $(function() {

    // Setup form validation on the #register-form element
    $("#passwordForm").validate({
        // Specify the validation rules
        rules: {
            userEmail: {
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
