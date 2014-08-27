<form role="form" method="post" class="profile-edit-form" action="/account/profile/edit" id="editProfileForm">
    <?php $_token('profile'); ?>
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
        <input type="text" name="user_phone" id="user_phone" class="form-control input-lg" placeholder="Phone" tabindex="3" value="<?php $_($user_phone, 'user_phone'); ?>">
    </div>

    <?php /* <div class="form-group">
        <input type="text" name="username" id="username" required class="form-control input-lg" placeholder="Username" tabindex="3" value="<?php $_($username, 'username'); ?>">
    </div>

    <div class="form-group">
        <input type="email" maxlength="100" required name="email" id="email" class="form-control input-lg" placeholder="Email Address" tabindex="4" value="<?php $_($email, 'email'); ?>">
    </div>

    <hr class="colorgraph">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <p>Update password:</p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="password" maxlength="100" name="password" id="password"
                       class="form-control input-lg" placeholder="Password" tabindex="5" value="<?php $_($password, 'password'); ?>">
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="password" maxlength="100" name="password_confirmation"
                       id="password_confirmation" class="form-control input-lg" placeholder="Confirm Password"
                       tabindex="6" value="<?php $_($password_confirmation, 'password_confirmation'); ?>">
            </div>
        </div>
    </div>
              */ ?>
    <hr class="colorgraph">
    <div class="row">
        <div class="col-xs-6 col-md-6">
            <input type="submit" name="_submit" value="Save" class="btn btn-block btn-lg" tabindex="7">
        </div>
        <div class="col-xs-6 col-md-6">
            <input type="submit" name="_submit" value="Save and Exit" class="btn btn-primary btn-block btn-lg" tabindex="8">
        </div>
    </div>
</form>

<script>
    $(function() {

        jQuery(function($) {
            $('#editProfileForm').bootstrapValidator({
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
    });
</script>