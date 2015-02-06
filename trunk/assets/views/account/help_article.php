<div class="container">
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Help</h1>
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li><a href="/account">Account</a></li>
            <li><a href="/account/help_articles">Help Articles</a></li>
            <li class="active"><?php $_($pageTitle); ?></li>
        </ol>
    </div>

    <div class="col-lg-12">
        <?php
        $currentCwd = getcwd();
        chdir(__DIR__ . '/../content_pages/help_articles');
        $path = preg_split("/[\\n\\r\\0]/", $page . '.php');
        try {
            include trim($path[0]);
        } catch (\Exception $e) {
            while(ob_get_clean()) {};
            throw new \App\Exception\NotFoundException();
        }
        chdir($currentCwd);
        ?>
    </div>
</div>
</div>