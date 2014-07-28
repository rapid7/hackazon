<script>
    function update_qty(itemId, qty)
    {
        $.ajax({
            url:'/cart/update',
            type:"POST",
            data: {qty: qty, itemId: itemId},
            dataType:"json",
            success: function(data){
                if (qty == 0) {
                    $("#tr_item_" + itemId).remove();
                }
                $("#items_qty").html(data.items_qty);
                $("#total_price").html(data.total_price);
                alert('Your cart has been updated');//TODO: Do Bootstrap style, .alert() does not work
            },
            fail: function() {
                alert( "error" );
            }
        });
    }
</script>
<div class="container">

    <div class="row">
        <div class="col-md-9 col-sm-8">
            <h1 class="page-header">Shopping Cart</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">My Cart</li>
            </ol>
            <table class="table table-striped">
                <tr>
                    <th></th><th>Name</th><th>Price</th><th>Quantity</th>
                </tr>

            <?php
            foreach ($items as $item):?>
                <?php
                $product = $item->getProduct();
                ?>
                <tr id="tr_item_<?=$item->id?>">
                    <td>
                        <img class="img-responsive img-home-portfolio" style="width:100px" src="/products_pictures/<?=$product['picture']?>" alt="photo <?=$product['name']?>">
                    </td>
                    <td>
                        <h4><a href="/product/view/<?=$product['productID']?>"><?=$product['name']?></a></h4>
                    </td>
                    <td>
                        <h4>$<?=$product['price']?></h4>
                    </td>
                    <td>
                        <h4><select id="qty_selected" onchange="update_qty(<?=$item->id?>, this.value)">
                            <?php
                            for ($i = 0;$i <= 10;$i++) {
                                $selected = '';
                                if ($i == $item->qty) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
                            }?>
                        </select></h4>
                    </td>
                </tr>
            <?php endforeach;?>
            </table>
        </div>

        <div class="col-md-3 col-sm-4 sidebar">
            Subtotal (<span id="items_qty"><?=$itemQty?></span> items): $<span id="total_price"><?=$totalPrice?></span>

            <a class="btn btn-primary" id="add_to_cart" onclick="alert('Coming Soon');return false;" href="#">Proceed to checkout</a>
        </div>

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->
