<script>
    $(function () {
        $("#btn_shipping").click(function(){
            $.ajax({
                url:'/checkout/shipping',
                type:"POST",
                dataType:"json",
                data: $("#shippingForm").serialize(),
                success: function(data){

                    $("#checkout-alert-info").empty().append('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Your cart has been deleted</strong></div>');
                    setTimeout('$(".alert-info").alert("close");', 3000);
                },
                fail: function() {
                    alert( "error" );
                }
            });
        });
    });

</script>
<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active" id="step2">

    <?php foreach ($this->customerAddresses as $address) {
        echo '<b>' . $address->full_name . '</b><br />';
        echo '' . $address->address_line_1 . '<br />';
        echo '' . $address->address_line_2 . '<br />';
        echo '' . $address->city . ', ' . $address->region . ' ' . $address->zip . '<br />';
        echo '' . $address->country_id;
        echo '' . $address->phone;
        };
    ?>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <form id="shippingForm" class="form-horizontal well">
                <fieldset>
                    <legend>Add a new address</legend>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="fullName">Full name:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="fullName" name="fullName" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine1">Address line 1:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="addressLine1" name="addressLine1" type="text" placeholder="Street address, P.O. box, company name, c/o">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine2">Address line 2:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="addressLine2" name="addressLine2" type="text" placeholder="Apartment, suite, unit, building, floor, etc. ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="city">City:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="city" name="city" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="state">State/Province/Region:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="state" name="state" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="zip">ZIP:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="zip" name="zip" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="country">Country:</label>
                        <div class="col-xs-8">
                            <select class="form-control" id="country" name="country">
                                <option value="RU">Russia</option>
                                <option value="EN">United States</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="phone">Phone number:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="phone" name="phone" type="text">
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <button class="btn btn-default" onclick="window.location.href='/cart/view'"><span class="glyphicon glyphicon-chevron-left"></span> Overview</button>
        </div>
        <div class="col-xs-6">
            <button id="btn_shipping" class="btn btn-primary pull-right" data-target="#step3" data-toggle="tab">Use this address <span class="glyphicon glyphicon-chevron-right icon-white"></span></button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
