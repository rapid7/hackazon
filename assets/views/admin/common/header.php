<div id="wrapper" class="admin">

<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="/admin/">Hackazon Admin<?='';//(isset($pageTitle) ? " &mdash; " . $pageTitle : "") ?></a>
</div>
<!-- /.navbar-header -->

<ul class="nav navbar-top-links navbar-right">
<?php //include __DIR__.'/_top_dropdowns.php'; ?>
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">
            <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
            </li>
            <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
            </li>
            <li class="divider"></li>
            <li><a href="/admin/user/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </li>
        </ul>
        <!-- /.dropdown-user -->
    </li>
    <!-- /.dropdown -->
</ul>
<!-- /.navbar-top-links -->

<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <?php //include __DIR__.'/_search_field.php'; ?>
            <?php $baseLen = strlen('/admin'); ?>
            <?php if (isset($sidebarLinks)): ?>
            <?php foreach ($sidebarLinks as $sbLink => $sbLinkData): ?>
                <?php $isLinkActive = strlen($sbLink) <= $baseLen && $_SERVER['REQUEST_URI'] == $sbLink
                    || strlen($sbLink) > $baseLen && strpos($_SERVER['REQUEST_URI'], $sbLink) === 0; ?>
            <li>
                <a href="<?php echo $sbLink; ?>" class="<?php echo $isLinkActive ? 'active' : ''; ?>"
                    ><i class="<?php $_($sbLinkData['link_class']); ?>"></i><?php $_($sbLinkData['label']); ?></a>
            </li>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php //include __DIR__.'/_sidebar_extra_items.php'; ?>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->
</nav>

<div id="page-wrapper">
    <?php if (isset($pageHeader)): ?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php echo $pageHeader; ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <?php endif; ?>