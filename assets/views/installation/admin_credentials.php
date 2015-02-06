<form action="/install/admin_credentials" id="adminCredentialsForm" method="POST">
    <div class="form-group">
        <label for="password_field">Password:</label>
        <input class="form-control" type="password" name="password" value="" placeholder="Password" id="password_field"
                required /><br>
    </div>

    <div class="form-group">
        <label for="password_field">Confirm Password:</label>
        <input class="form-control" type="password" name="password_confirmation" value="" placeholder="Confirmation"
               id="password_confirm_field" required/><br>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary pull-right">Next Step</button>
    </div>
</form>

<script>
    $(function() {

        jQuery(function($) {
            var $credentialsForm = $('#adminCredentialsForm');
            $credentialsForm.hzBootstrapValidator({
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

            var $passwordField = $('#password_field'),
                $useExistingPw = $('.js-use-existing-password');

            $useExistingPw.trigger('change');
        });
    });
</script>