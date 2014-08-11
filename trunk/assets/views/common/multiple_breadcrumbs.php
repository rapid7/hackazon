<?php
foreach ($breadcrumbs as $category) {
    ?>
    <ol class="breadcrumb">
        <?php
        $lastItem = array_pop($category);
        foreach ($category as $key => $item) {
            ?>
            <li><a href="<?= $key; ?>"><?= $item; ?></a></li>
        <?php
        }
        ?>
        <li class="active"><?= $lastItem; ?></li>
    </ol>
<?php
}


