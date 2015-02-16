<?php $baseImgPath = $this->pixie->getParameter('parameters.use_external_dir') ? '/upload/download.php?image=' : '/user_pictures/'; ?>
<div class="row">
	<div class="col-xs-8">
		<table class="table profile-table table-striped">
		<thead>
			<tr>
				<td>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Username:</td>
				<td><?php $_($userData['username']); ?></td>
			</tr>
			<tr>
				<td>E-mail:</td>
				<td><?php $_($userData['email']); ?></td>
			</tr>
			<tr>
				<td>First Name:</td>
				<td><?php $_($userData['first_name']); ?></td>
			</tr>
			<tr>
				<td>Last Name:</td>
				<td><?php $_($userData['last_name']); ?></td>
			</tr>
			<tr>
				<td>Phone:</td>
				<td><?php $_($userData['user_phone']); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-4">
		<?php if (isset($user->photo) && $user->photo): ?>
			<img src="<?php echo $baseImgPath; $_($user->getPhotoPath()); ?>" alt="" class="profile-picture img-responsive img-bordered img-thumbnail" />
		<?php endif; ?>
	</div>
</div>

<div class="row">
    <div class="col-xs-12">
        <p class="text-right buttons-row">
            <a href="/account/profile/edit" id="profile_link" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label">Edit Profile</span></a>
        </p>
    </div>
</div>

<script>
    $(function() {
        Ladda.bind( '#profile_link' );
        
        $('#profile_link').on('click', function(e) {
            var l = Ladda.create(document.querySelector( '#profile_link' ));
            l.start();
            window.location.href = "/account/profile/edit";
            return false; // Will stop the submission of the form
        });
    });
</script>