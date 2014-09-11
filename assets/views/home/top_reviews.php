<?php
/**
 * @var array $selectedReviews
 * @var \App\Model\Review $review
 */

// Bootstrap glyph icons (several examples)
$iconVariants = array(
    'refresh', 'download', 'pencil', 'camera', 'qrcode', 'tags', 'random'
);

// Number of review columns
$reviewColumns = isset($reviewColumns) && is_numeric($reviewColumns) ? $reviewColumns : 2;

// Bootstrap styles for the given column count:
$columnStyles = "col-xs-12 col-sm-12 col-md-6 col-md-6";
if ($reviewColumns == 3) {
    $columnStyles = "col-xs-12 col-sm-12 col-md-4 col-md-4";
}

?>
<div class="row">
    <?php foreach ($selectedReviews as $review): ?>
        <?php $iconKey = array_rand($iconVariants, 1); ?>
        <div class="<?php echo $columnStyles; ?>">
            <div class="article">
                <article>
                    <?php if (!isset($showReviewIcons) || $showReviewIcons !== false): ?>
                        <div class="review-icon-box"><span class="review-icon glyphicon glyphicon-<?php echo $iconVariants[$iconKey]; ?>"></span></div>
                    <?php endif; ?>
                    <h4><?php echo $review->username; ?></h4>
                    <h5>about <a href="/product/view?id=<?php echo $review->product->productID; ?>"><?php echo $review->product->name; ?></a></h5>

                    <p><?php echo mb_substr($review->review, 0, 300, 'utf-8'); ?> <a href="/product/view?id=<?php echo $review->product->productID; ?>">More
                            <span class="glyphicon glyphicon-chevron-right"></span></a></p>
                </article>
            </div>
        </div>
    <?php endforeach; ?>
</div>