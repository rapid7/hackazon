<h1><td><?php $_($user->first_name); ?></td> <td><?php $_($user->last_name); ?></td></h1>
<p class="text-right">
    <a href="/account/profile/edit">Edit Profile</a>
</p>
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