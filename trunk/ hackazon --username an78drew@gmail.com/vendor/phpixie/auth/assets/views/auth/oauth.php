<!DOCTYPE html>
<html>
	<head>
		<script>
			<?php if(!empty($return_url)):?>
				window.opener.location = "<?php echo $return_url; ?>";
			<?php else:?>
				window.opener.location.reload(true);
			<?php endif;?>
			
			window.close();
		</script>
	</head>
</html>