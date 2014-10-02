<div class="container profile-edit">

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">Edit Profile</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/account<?php echo $useRest ? '#!profile/edit' : '#profile'; ?>">My Account</a></li>
                <li class="active">Edit Profile</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            <?php include __DIR__ . '/_profile_form.php'; ?>
        </div>
    </div>
</div>