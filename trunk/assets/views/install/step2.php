<?php include __DIR__ . '/install_header.php'; ?>
<div class="tab-pane active" id="step2">
	<form role="form" action="/install/step2">
		<div class="form-group">
			<label for="inputUser">Email Type</label>
			<select class="form-control" name="emailType">
				<option>sendmail</option>
				<option>smtp</option>
				<option>native</option>
			</select>
		</div>
		<div class="form-group">
			<label for="inputName">Email Host</label>
			<input type="text" class="form-control" id="emailHost" name="emailHost" placeholder="Enter Email Host" value="">
		</div>
		<div class="form-group">
			<label for="inputUser">Email Port</label>
			<input type="text" class="form-control" id="emailPort" name="emailPort" placeholder="Enter Email Port" value="" autocomplete="off">
		</div>
		<div class="form-group">
			<label for="inputUser">Email User</label>
			<input type="text" class="form-control" id="emailUser" name="emailUser" placeholder="Enter Email User" value="" autocomplete="off">
		</div>
		<div class="form-group">
			<label for="inputUser">Email Password</label>
			<input type="password" class="form-control" id="emailPassword" name="emailPassword" placeholder="Enter Email User Password" value="" autocomplete="off">
		</div>
		<div class="form-group">
			<label for="inputUser">Email Encryption</label>
			<select class="form-control" name="emailEncryption">
				<option>SSL</option>
				<option>TLS</option>
			</select>
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