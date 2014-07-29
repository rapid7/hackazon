<?php
/**
 * @var array $selectedReviews
 * @var \App\Model\Review $review
 */
$iconVariants = array(
    'refresh', 'download', 'pencil', 'camera', 'qrcode', 'tags', 'random'
);
?>
<div class="row">
    <?php foreach ($selectedReviews as $review): ?>
        <?php $iconKey = array_rand($iconVariants, 1); ?>
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-6">
            <div class="article">
                <article>
                    <div class="review-icon-box"><span class="review-icon glyphicon glyphicon-<?php echo $iconVariants[$iconKey]; ?>"></span></div>
                    <h4><?php echo $review->username; ?></h4>
                    <h5>about <a href="/product/view/<?php echo $review->product->productID; ?>"><?php echo $review->product->name; ?></a></h5>

                    <p><?php echo mb_substr($review->review, 0, 300, 'utf-8'); ?> <a href="/product/view/<?php echo $review->product->productID; ?>">More
                            <span class="glyphicon glyphicon-chevron-right"></span></a></p>
                </article>
            </div>
        </div>
    <?php endforeach; ?>
</div>