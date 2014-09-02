    <div class="container">
        <div class="row">
            <div class="col-lg-12">
				<h1>Forgot password</h1>
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

			<div class="row">
			<div class="col-lg-12">
                <form role="form" method="post" action="/user/password" id="passwordForm">
                    <div class="form-group">
                        <input type="email" required="required" class="form-control" name="email" id="email" placeholder="Enter email" />
						            <span class="input-group-btn">
							            
						            </span>
                    </div>
                    <div class="form-group">
                      <button id="loginbtn" type="submit" class="btn btn-success">Restore</button>
                    </div>
					</form>
				</div>
			</div>

        <?php endif; ?>
    </div>
  <script>
    $('#passwordForm').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        container: 'tooltip'
    });
  </script>