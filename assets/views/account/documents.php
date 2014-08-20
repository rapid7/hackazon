<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <h1 class="page-header">Documents</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li class="active">Documents</li>
            </ol>
        </div>

        <div class="col-lg-12">
            <ul>
            <?php foreach ($files as $file => $fileName): ?>
                <li><a href="/account/show?page=<?php echo $_($fileName); ?>"><?php echo $_(ucwords($file)); ?></a></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>