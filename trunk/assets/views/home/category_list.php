<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.07.2014
 * Time: 13:25
 */
/**
 * @var array $sidebar
 */
$categoryColumns = array();
$i = 0;
foreach ($sidebar as $footerCat) {
    $categoryColumns[$i % 6][] = $footerCat;
    $i++;
}
?>
<div class="container">
    <div class="footer">
        <div class="row hidden-print">
            <?php foreach ($categoryColumns as $i => $columnCategories): ?>
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                    <!-- START CONTENT ITEM -->
                    <?php /** @var \App\Model\Category $category */ ?>
                    <?php foreach ($columnCategories as $j => $category): ?>
                        <ul class="unstyled">
                            <li class="footer-title"><a href="/category/view?id=<?php echo $category->categoryID; ?>"><?php echo $category->name; ?></a></li>
                            <?php if (is_array($category->childs) && count($category->childs)): ?>
                                <?php /*foreach ($category->childs as $subCategory): ?>
                                    <li><a href="/category/view?id=<?php echo $subCategory->categoryID; ?>"><?php echo $subCategory->name; ?></a></li>
                                <?php endforeach;*/ ?>
                            <?php endif; ?>
                        </ul>
                    <?php endforeach; ?>
                    <!-- END CONTENT ITEM -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>