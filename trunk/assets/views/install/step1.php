<?php include __DIR__ . '/install_header.php'; ?>
<div class="tab-pane active" id="step1">
	<form role="form" action="/install/step1">
		<div class="form-group">
			<label for="inputUser">DB User</label>
			<input type="text" class="form-control" id="inputUser" name="inputUser" placeholder="Enter DB User" value="" autocomplete="off">
		</div>
		<div class="form-group">
			<label for="inputPassword">DB Password</label>
			<input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Enter DB Password" value="">
		</div>
		<div class="form-group">
			<label for="inputName">DB Name</label>
			<input type="text" class="form-control" id="inputName" id="inputName" placeholder="Enter DB Name" value="">
		</div>
		<div class="form-group">
			<label for="inputName">DB Host</label>
			<input type="text" class="form-control" id="inputHost" name="inputHost" placeholder="Enter DB Host" value="">
		</div>

		<div class="row">
			<div class="col-xs-8">
					<button type="submit" class="btn btn-success" name="checkConnection">Check DB connection</button>
			</div>
			<div class="col-xs-4">
				<button class="btn btn-primary pull-right ladda-button" data-target="#step2" data-toggle="tab" id="step1_next" data-style="expand-left"><span class="ladda-label">Next <span class="glyphicon glyphicon-chevron-right"></span></span></button>
			</div>
		</div>

	</form>

</div>
<?php include __DIR__ . '/install_footer.php';