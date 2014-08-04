<script>
    $(function () {
        $.validate({
            form : '#shippingForm',
            modules : 'security',
            onError : function() {
            },
            onSuccess : function() {
                $.ajax({
                    url:'/checkout/shipping',
                    type:"POST",
                    dataType:"json",
                    data: $("#shippingForm").serialize(),
                    success: function(data){
                        window.location.href="/checkout/billing";
                    },
                    fail: function() {
                        alert( "error" );
                    }
                });
                return false; // Will stop the submission of the form
            }
        });
        $("#btn_shipping").click(function(){
            $("#shippingForm").submit();
        });
        $(".edit-address").click(function(){
            $.ajax({
                url:'/checkout/getAddress',
                type:"POST",
                dataType:"json",
                data: {address_id: $(this).attr('data-id')},
                success: function(data){
                    $("#fullName").val(data.full_name);
                    $("#addressLine1").val(data.address_line_1);
                    $("#addressLine2").val(data.address_line_2);
                    $("#city").val(data.full_name);
                    $("#region").val(data.region);
                    $("#zip").val(data.zip);
                    $("#country_id").val(data.country_id);
                    $("#phone").val(data.phone);
                },
                fail: function() {
                    alert( "error" );
                }
            });
        });
        $(".delete-address").click(function(){
            var elem = this;
            $.ajax({
                url:'/checkout/deleteAddress',
                type:"POST",
                dataType:"json",
                data: {address_id: $(this).attr('data-id')},
                success: function(){
                    $(elem).parent('div').remove();
                },
                fail: function() {
                    alert( "error" );
                }
            });
        });
        $(".confirm-address").click(function(){

        });
    });

</script>
<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active" id="step2">

    <?php foreach ($this->customerAddresses as $address) :?>
        <div class="col-sm-2" style="padding:5px;margin-right:5px;margin-bottom:5px;border: #e5e5e5 solid 1px">
        <b><?php echo $address->full_name ?></b><br />
        <?php echo $address->address_line_1 ?><br />
        <?php echo $address->address_line_2 ?><br />
        <?php echo $address->city . $address->region . $address->zip ?><br />
        <?php echo $address->country_id ?><br />
        <?php echo $address->phone ?><br />
        <button data-id="<?php echo $address->id?>" style="margin-bottom:5px;width:100%" class="btn btn-primary btn-sm confirm-address">Ship to this address</button>
        <button data-id="<?php echo $address->id?>" style="width:48%" class="btn btn-default btn-xs edit-address">Edit</button>&nbsp;
        <button data-id="<?php echo $address->id?>" style="width:47%" class="btn btn-default btn-xs delete-address">Delete</button>
        </div>
    <?php endforeach;?>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <form id="shippingForm" class="form-horizontal well">
                <fieldset>
                    <legend>Add a new address</legend>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="fullName">Full name:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="fullName" name="fullName" data-validation="length" data-validation-length="min5" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine1">Address line 1:</label>
                        <div class="col-xs-8">
                            <input class="form-control" data-validation="length" data-validation-length="min5" id="addressLine1" name="addressLine1" type="text" placeholder="Street address, P.O. box, company name, c/o">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine2">Address line 2:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="addressLine2" name="addressLine2" type="text" placeholder="Apartment, suite, unit, building, floor, etc. ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="city">City:</label>
                        <div class="col-xs-8">
                            <input class="form-control" data-validation="required" id="city" name="city" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="region">State/Province/Region:</label>
                        <div class="col-xs-8">
                            <input class="form-control" data-validation="required" id="region" name="region" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="zip">ZIP:</label>
                        <div class="col-xs-8">
                            <input class="form-control" data-validation="number" data-validation-allowing="range[1;1000000]" id="zip" name="zip" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="country_id">Country:</label>
                        <div class="col-xs-8">
                            <select class="form-control" id="country_id" data-validation="required" name="country_id">
                                <option value="RU">Russia</option>
                                <option value="EN">United States</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" data-validation="required" for="phone">Phone number:</label>
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
            <button id="btn_shipping" class="btn btn-primary pull-right" data-target="#step3" data-toggle="tab">Ship to this address <span class="glyphicon glyphicon-chevron-right icon-white"></span></button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
