<ol class="breadcrumb">
    <?php
    $lastItem = array_pop($breadcrumbs);
    foreach ($breadcrumbs as $key => $item) {
        ?>
        <li><a href="<?= $key; ?>"><?= $item; ?></a></li>
    <?php
    }
    ?>
    <li class="active"><?php echo $lastItem; ?></li>
</ol>


