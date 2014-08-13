
<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">New password</li>
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
            <form role="form" method="post" action="/user/newpassw" id="recoverForm">
                <div class="form-group">
                    <label for="password">Password <span style="color: red">*</span></label>
                    <input type="password" name="password" class="form-control" id="password">
                </div>
                <div class="form-group">
                    <label for="cpassword">Confirm Password <span style="color: red">*</span></label>
                    <input type="password" name="cpassword" class="form-control" id="cpassword">
                </div>
                <input type="hidden" name="username" value="<?=$username?>">
                <input type="hidden" name="recover" value="<?=$recover_passw?>">
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<script>

    $(function() {
        $("#recoverForm").validate({
            rules: {
                password: "required",
                cpassword: {
                    required: true,
                    equalTo : '#password'
                }
            },

            submitHandler: function(form) {
                form.submit();
            }
        });

        $("#user_phone").inputmask("+1(999) 999-9999");

    });

</script>