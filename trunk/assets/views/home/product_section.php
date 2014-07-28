<?php
/**
 * @var callable $_
 */
?>
<!-- /.section-colored -->
<div class="col-lg-12 text-center">
    <h2><?php $_($sectionData['title']); ?></h2>
    <hr>
</div>

<div class="section">
    <div class="container product-list">
        <?php
        $productListData = $sectionData;
        include(__DIR__ . "/product_list.php");
        ?>
    </div>
    <!-- /.container -->
</div>
<!-- /.section -->