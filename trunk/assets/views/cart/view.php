<script>
    function update_qty(itemId, qty)
    {
        var priceItem = parseInt($("#row_span_item_" + itemId).html());
        var priceTotal = priceItem * qty;
        $("#row_span_total_" + itemId).empty().append(priceTotal);

        $.ajax({
            url:'/cart/update',
            type:"POST",
            data: {qty: qty, itemId: itemId},
            dataType:"json",
            success: function(data){
                if (qty <= 0) {
                    $("#tr_item_" + itemId).remove();
                }
                $("#items_qty").html(data.items_qty);
                $("#total_price").html(data.total_price);
                $("#checkout-alert-info").empty().append('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Your cart has been updated</strong></div>');
                setTimeout('$(".alert-info").alert("close");', 3000);

            },
            fail: function() {
                alert( "error" );
            }
        });
    }

    $(function () {
        $("#empty_cart").click(function(){
            $.ajax({
                url:'/cart/empty',
                type:"POST",
                dataType:"json",
                success: function(data){
                    $(".tr_items").remove();
                    $("#items_qty").html(0);
                    $("#total_price").html(0);
                    $("#checkout-alert-info").empty().append('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Your cart has been deleted</strong></div>');
                    setTimeout('$(".alert-info").alert("close");', 3000);
                },
                fail: function() {
                    alert( "error" );
                }
            });
        });
        $(".close").click(function(){
            $(".alert-info").alert('close');
        });
        $(".plus_btn").click(function() {
            var qty = parseInt($('#input_' + $(this).attr('data-id')).val()) + 1;
            $('#input_' + $(this).attr('data-id')).val(qty);
            update_qty($(this).attr('data-id'), qty);
        });
        $(".minus_btn").click(function() {
            var qty = parseInt($('#input_' + $(this).attr('data-id')).val()) - 1;
            if (qty < 0) return;
            $('#input_' + $(this).attr('data-id')).val(qty);
            update_qty($(this).attr('data-id'), qty);
        });
    });

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
            <div id="checkout-alert-info"></div>
        </div>


    </div>
    <!-- /.row -->

    <div class="row">
    <div class="col-xs-12">

    <!-- START CONTENT ITEM -->
    <ul class="nav nav-tabs">
        <li class="active"><a href="#step1" data-toggle="tab"><b>step 1:</b> overview</a></li>
        <li class=""><a href="#step2" data-toggle="tab"><b>step 2:</b> personal info</a></li>
        <li><a href="#step3" data-toggle="tab"><b>step 3:</b> confirmation</a></li>
        <li><a href="#step4" data-toggle="tab"><b>step 4:</b> payment</a></li>
    </ul>

    <div class="tab-content">

    <div class="tab-pane active" id="step1">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered table-order">
                    <thead>
                    <tr>
                        <th class="hidden-xs">&nbsp;</th>
                        <th></th>
                        <th>count</th>
                        <th>each (incl. tax)</th>
                        <th>total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($items as $item):?>
                    <?php
                    $product = $item->getProduct();
                    ?>
                    <tr class="tr_items" id="tr_item_<?php echo $item->id?>">
                        <td class="hidden-xs"><img class="img-responsive img-home-portfolio" style="width:100px" src="/products_pictures/<?=$product['picture']?>" alt="photo <?=$product['name']?>"></td>
                        <td>
                            <h4><a href="/product/view/<?=$product['productID']?>"><?=$product['name']?></a></h4>
                        </td>
                        <td>

                            <div class="input-group">
														<span class="input-group-btn">
															<button data-id="<?php echo $item->id?>" class="btn btn-default minus_btn" type="button">
																<span class="glyphicon glyphicon-minus">
															</span></button>
														</span>
                                <input type="text" id="input_<?php echo $item->id?>" onchange="update_qty(<?php echo $item->id?>, this.value)" class="form-control" value="<?php echo $item->qty?>">
														<span class="input-group-btn">
															<button data-id="<?php echo $item->id?>" class="btn btn-default plus_btn" type="button">
																<span class="glyphicon glyphicon-plus">
															</span></button>
														</span>
                            </div>

                        </td>
                        <td align="right">
                            <span>$ <span id="row_span_item_<?php echo $item->id?>"><?php echo $item->price?></span>,- </span>
                        </td>
                        <td align="right">
                            <span>$ <span id="row_span_total_<?php echo $item->id?>"><?php echo $item->price*$item->qty?></span>,- </span>
                        </td>
                    </tr>
                    <?php endforeach;?>
                    <tr>
                        <td align="right" colspan="3">
                            <form class="form-horizontal">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="col-xs-4 control-label">shipping: </label>
                                        <div class="col-xs-8">
                                            <select class="form-control">
                                                <option>mail</option>
                                                <option>collect</option>
                                                <option>express</option>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </td>
                        <td align="right" colspan="2">
                            <span class="label label-success">free</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" colspan="3">
                            <form class="form-horizontal">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="col-xs-4 control-label">payment: </label>
                                        <div class="col-xs-8">
                                            <select class="form-control">
                                                <option>wire transfer</option>
                                                <option>paypal</option>
                                                <option>creditcard</option>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </td>
                        <td align="right" colspan="2">
                            <span class="label label-success">free</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" colspan="3">Quantity items:<br>total: </td>
                        <td align="right" colspan="2">
                            $ <span id="items_qty"><?=$itemQty?></span>,-<br>
                            $ <span id="total_price"><?=$totalPrice?>,-<br>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <a href="/" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Continue shopping</a>
            </div>
            <div class="col-xs-4">
                <button class="btn btn-default" id="empty_cart"><span class="glyphicon glyphicon-trash"></span> Empty cart</button>
            </div>
            <div class="col-xs-4">
                <button class="btn btn-primary pull-right" data-target="#step2" data-toggle="tab">Next <span class="glyphicon glyphicon-chevron-right"></span></button>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="step2">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <form class="form-horizontal well">
                    <fieldset>
                        <legend>Personal info</legend>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companyname">Company name</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companyname" type="text" placeholder="Companyname">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companytradingregister">Register</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companytradingregister" type="text" placeholder="Register">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companytaxregister">Tax number</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companytaxregister" type="text" placeholder="Tax number">
                                <p class="help-block">Tax number required</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companycontact">Contactperson</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companycontact" type="text" placeholder="Contactperson">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companyemail">E-mail address</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companyemail" type="text" placeholder="naam@domein.nl">
                            </div>
                        </div>
                        <br>
                        <legend>Invoice address</legend>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companyaddress">Address</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companyaddress" type="text" placeholder="Adres">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companyzip">Zipcode</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companyzip" type="text" placeholder="Zipcode">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companycity">City</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companycity" type="text" placeholder="City">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companycountry">Country</label>
                            <div class="col-xs-8">
                                <select class="form-control" id="companycountry">
                                    <option>Country 01</option>
                                    <option>Country 02</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col-xs-12 col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>Postal address</legend>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companypostaladdress">Address</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companypostaladdress" type="text" placeholder="Address">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companypostalzip">Zipcode</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companypostalzip" type="text" placeholder="Zipcode">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companypostalcity">City</label>
                            <div class="col-xs-8">
                                <input class="form-control" id="companypostalcity" type="text" placeholder="City">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-4 control-label" for="companypostalcountry">Country</label>
                            <div class="col-xs-8">
                                <select class="form-control" id="companypostalcountry">
                                    <option>Country 01</option>
                                    <option>Country 02</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <legend>Comments</legend>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <textarea class="form-control" id="comments" rows="12" maxlength="255"></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="yes" id="termsandconditions">Yes, I agree to the <a href="#">terms &amp; conditions</a>
                            </label>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <button class="btn btn-default" data-target="#step1" data-toggle="tab"><span class="glyphicon glyphicon-chevron-left"></span> Overview</button>
            </div>
            <div class="col-xs-6">
                <button class="btn btn-primary pull-right" data-target="#step3" data-toggle="tab">Next <span class="glyphicon glyphicon-chevron-right icon-white"></span></button>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="step3">
        <div class="row">

            <div class="col-xs-12 col-sm-5">

                <div class="well">

                    <table class="table">
                        <thead>
                        <tr>
                            <th>Peronsal info</th>
                        </tr>
                        <tr>
                            <td>
                                company name<br>
                                contactperson name<br>
                                register: register number<br>
                                tax number: tax number<br>
                                <br>
                                <b>invoice address</b><br>
                                address 123<br>
                                zipcode city nl<br>
                                email@example.com<br>
                                <br>
                                <b>Comments</b>
                                <br>
                                <i>Comments are here</i>
                            </td>
                        </tr>
                        </thead>
                    </table>

                </div>

            </div>

            <div class="col-xs-12 col-sm-7">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Overview</th>
                        <th width="50">count</th>
                        <th width="70">total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Product A</td>
                        <td align="center">1</td>
                        <td align="right">€ 123,-</td>
                    </tr>
                    <tr>
                        <td>Product B</td>
                        <td align="center">2</td>
                        <td align="right">€ 2,-</td>
                    </tr>
                    <tr>
                        <td>Shipping: express</td>
                        <td align="right" colspan="2">€ 0,-</td>
                    </tr>
                    <tr>
                        <td>Payment: wiretransfer</td>
                        <td align="right" colspan="2">€ 0,-</td>
                    </tr>
                    <tr>
                        <td align="right" colspan="3"><strong>€ 125,-</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-6">
                <button class="btn btn-default" data-target="#step2" data-toggle="tab"><span class="glyphicon glyphicon-chevron-left"></span> personal info</button>
            </div>
            <div class="col-xs-6">
                <button class="btn btn-primary pull-right" data-target="#step4" data-toggle="tab">Next <span class="glyphicon glyphicon-chevron-right"></span></button>
            </div>
        </div>

    </div>

    <div class="tab-pane" id="step4">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <h2>Payment success</h2>
                <p>Your order will be processed as soon as possible.</p>
            </div>
            <div class="col-xs-12 col-sm-6">
                <h2>Delivery</h2>
                <p>With express shipping, your order will arrive within 24 hours.</p>
            </div>
        </div>
        <br><br><br>
        <div class="row">
            <div class="col-xs-6">
            </div>
            <div class="col-xs-6">
                <a href="index.html" class="btn btn-primary pull-right">Go to home <span class="glyphicon glyphicon-chevron-right"></span></a>
            </div>
        </div>
    </div>

    </div>

    <br>

    </div>
    <!-- END CONTENT ITEM -->

    </div>


</div>
<!-- /.container -->
