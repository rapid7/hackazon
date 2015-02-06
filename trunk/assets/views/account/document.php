<div class="container">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php echo $pageTitle; ?></h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/account/documents">Documents</a></li>
                <li class="active"><?php echo $pageTitle; ?></li>
            </ol>
        </div>

        <div class="col-lg-12">
            <?php echo $pageContent; ?>
        </div>
    </div>
</div>