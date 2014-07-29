<?php
/**
 * @var callable $_
 * @var array $sectionData
 */
?>
<!-- /.section-colored -->
<div class="col-lg-12 text-center">
    <h2><?php echo $sectionData['title']; ?></h2>
    <hr>
</div>

<div class="home-product-sections">
<?php
$productListData = $sectionData;
include(__DIR__ . "/product_list.php");
?>
</div>