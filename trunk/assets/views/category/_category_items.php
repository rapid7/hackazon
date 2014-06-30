<?php if(!empty($subCategories)):?>
    <?php foreach($subCategories as $subCategory):?>
        <div class="col-sm-4 col-lg-4 col-md-4">
            <div class="thumbnail">
                <img src="/products_pictures/<?=$subCategory['picture']?>" alt="">
                    <div class="caption">
                        <h4><a href="/category/view/<?=$subCategory['categoryID']?>"><?=$subCategory['name']?></a></h4>
                        <p><?=$subCategory['description']?></p>
                    </div>
            </div>
        </div>
    <?php endforeach;?>
<?php else:?>
    <span>No categories found.</span>
<?php endif;?>