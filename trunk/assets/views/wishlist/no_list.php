<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php echo $pageTitle; ?></h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li class="active">Wish List</li>
            </ol>
        </div>

    </div>
    <div class="row wishlist">
        <div class="col-lg-12 text-center">
            <div class="logo-container">
                <img src="/images/wishlist-lg.jpg" border="0" height="96" width="254">
            </div>
        </div>


        <div class="col-lg-1"></div>
        <div class="col-lg-4">
            <div class="">
                <h1 class="a-nowrap">Create a Wish List</h1>
                <?php if (isset($user)): ?>
                    <form action="/wishlist/new" role="search" class="form-inline" method="post">
                        <button class="btn btn-primary" type="submit">Get Started</button>
                    </form>
                <?php else: ?>
                    <a href="/user/login?return_url=/wishlist" class="btn btn-primary">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-7">
            <h1>Find a Wish List</h1>
            <?php include __DIR__ . '/_search_form.php'; ?>
        </div>
        <div class="clearfix"></div>
        <div class="products clearfix">
            <div class="clearfix product-list"></div>
        </div>
    </div>
	<div class="row">
		<div class="col-lg-3">
			<h3>Get <br>Organized</h3>
			<p>Create multiple lists for yourself and others</p> 
			<img alt="" src="/images/wl_cat_lists_200px._V359103046_.jpg" height="143" width="200">
		</div>
		<div class="col-lg-3">
			<h3>Save Ideas <br>and Products</h3>
			<p>Add ideas and products from any website</p> 
			<img alt="" src="/images/wl_cat_note_200px._V359103046_.jpg" height="143" width="200">
		</div>
		<div class="col-lg-3">
			<h3>Give &amp; Get<br> Great Gifts</h3>
			<p>Remember your friends' wish lists &amp; share yours</p> 
			<img alt="" src="/images/wl_cat_gift_200px._V359103041_.jpg" height="143" width="200">
		</div>
		<div class="col-lg-3">
			<h3>Never Forget a<br> Birthday</h3>
			<p>Get shopping reminders for special occasions</p> 
			<img alt="" src="/images/wl_cat_calendar_200px._V359103046_.jpg" height="143" width="200">
		</div>
	</div>
</div>