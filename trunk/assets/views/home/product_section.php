<?php
/**
 * @var callable $_
 * @var array $sectionData
 */
?>
<!-- /.section-colored -->
<div class="col-lg-12 text-center">
    <div class="container section-title">
        <h2><?php echo $sectionData['title']; ?></h2>
    </div>
    <hr>
</div>

<div class="home-product-sections">
<?php
$productListData = $sectionData;
include(__DIR__ . "/product_list.php");
?>
</div>
<?php

if (isset($sectionData['reviews']) && is_array($sectionData['reviews']) && count($sectionData['reviews'])) {
    $selectedReviews = $sectionData['reviews'];
    $reviewColumns = 3;
    $showReviewIcons = false;
    ?><div class="container"><div class="col-xs-12"><?php
    include __DIR__ . '/top_reviews.php';
    ?></div></div><?php
}
