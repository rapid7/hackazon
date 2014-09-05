<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <h1 class="page-header">Help</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/account">Account</a></li>
                <li class="active">Help</li>
            </ol>
        </div>
        <div class="col-lg-6 ">
			<h3>Help Articles List</h3>
			<div class="list-group">
				<?php foreach ($files as $file => $fileName): ?>
                <a class="list-group-item " href="/account/help_articles?page=<?php echo $_($fileName); ?>"><span class="glyphicon glyphicon-file"></span> <?php echo $_(ucwords($file)); ?></a>
	            <?php endforeach; ?>
			</div>
        </div>
    </div>
</div>