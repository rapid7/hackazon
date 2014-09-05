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
				<td><?php $_($user->username); ?></td>
			</tr>
			<tr>
				<td>E-mail:</td>
				<td><?php $_($user->email); ?></td>
			</tr>
			<tr>
				<td>First Name:</td>
				<td><?php $_($user->first_name); ?></td>
			</tr>
			<tr>
				<td>Last Name:</td>
				<td><?php $_($user->last_name); ?></td>
			</tr>
			<tr>
				<td>Phone:</td>
				<td><?php $_($user->user_phone); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-4">
		<?php if (isset($user->photo) && $user->photo): ?>
			<img src="/user_pictures/<?php $_($user->photo); ?>" alt="" class="profile-picture img-responsive img-bordered img-thumbnail" />
		<?php endif; ?>
	</div>
</div>

<p class="text-right">
    <a href="/account/profile/edit" id="profile_link" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label">Edit Profile</span></a>
</p>
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