<script>
    $(function() {
        $("#add_to_cart").click(function(){
            $("#cart_form").submit();
            return false;
        });
    });
</script>

<div class="row">

    <div class="col-lg-12">
        <h1 class="page-header"><?=$pageTitle?></h1>
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li class="active">Product Item</li>
        </ol>
    </div>

</div>

<div class="section">
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="thumbnail">
                <a data-toggle="lightbox" data-title="<?=$product['name']?>" href="/products_pictures/<?=$product['picture']?>">
                    <img class="img-responsive preview-image" src="/products_pictures/<?=$product['picture']?>" alt="">
                </a>
                <div class="caption-full">
                    <h4 class="pull-right">$<?=$product['price']?></h4>
                    <h4><a href="#"><?=$product['name']?></a></h4>
                    <p><?=$product['description']?></p>
                    <form id="cart_form" action="/cart/add" method="post">
                        <div class="text-right">
                            <label for="qty">Qty</label>
                            <select id="qty" name="qty">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <input type="hidden" name="product_id" value="<?=$product['productID']?>">
                            <a class="btn btn-primary" id="add_to_cart" href="#">Add to cart</a>
                        </div>
                    </form>
                </div>
                <div class="ratings">
                    <p class="pull-right"><?=$product['customers_votes']?> reviews</p>
                    <p>
                        <?php include($common_path."rating_stars.php")?>
                        <?=$product['customers_rating']?> stars
                    </p>
                </div>
            </div>

            <div class="well">
                <div class="text-right">
                    <?php include($common_path."review_form.php")?>
                    <button class="btn btn-success" data-toggle="modal" data-target="#reviewForm">Leave a Review</button>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star-empty"></span>
                        Anonymous
                        <span class="pull-right">10 days ago</span>
                        <p>This product was great in terms of quality. I would definitely buy another!</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star-empty"></span>
                        Anonymous
                        <span class="pull-right">12 days ago</span>
                        <p>I've alredy ordered another one!</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star"></span>
                        <span class="glyphicon glyphicon-star-empty"></span>
                        Anonymous
                        <span class="pull-right">15 days ago</span>
                        <p>I've seen some better than this, but not at this price. I definitely recommend this item.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<div class="row">