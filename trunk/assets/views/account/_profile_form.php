<?php $baseImgPath = $this->pixie->getParameter('parameters.use_external_dir') ? '/upload/download.php?image=' : '/user_pictures/'; ?>
<form role="form" method="post" class="profile-edit-form" action="/account/profile/edit" id="editProfileForm" enctype="multipart/form-data">
    <?php $_token('profile'); ?>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="First Name" tabindex="1" value="<?php $_($first_name); ?>">
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="text" name="last_name" id="last_name" class="form-control input-lg" placeholder="Last Name" tabindex="2" value="<?php $_($last_name); ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <input type="text" name="user_phone" id="user_phone" class="form-control input-lg" placeholder="Phone" tabindex="3" value="<?php $_($user_phone); ?>">
    </div>

    <?php if (isset($photoUrl) && $photoUrl): ?>
        <div class="form-group">
            <img src="<?php echo $baseImgPath; $_($photoUrl); ?>" alt="" class="profile-picture" /> <br>
            <label><input type="checkbox" name="remove_photo" /> Remove photo</label>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <input type="file" name="photo" id="photo" class="file-input btn btn-default btn-primary btn-lg" title="Select avatar image" tabindex="4" value="<?php $_($photo); ?>">
    </div>

    <hr class="colorgraph">
    <div class="row">
        <div class="col-xs-6 col-md-6">
            <input type="submit" name="_submit_save" value="Save" class="btn btn-block btn-lg" tabindex="7">
        </div>
        <div class="col-xs-6 col-md-6">
            <input type="submit" name="_submit_save_and_exit" value="Save and Exit" class="btn btn-primary btn-block btn-lg" tabindex="8">
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