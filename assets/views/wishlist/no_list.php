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
    </div>

</div>