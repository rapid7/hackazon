<script>
    $(function () {
        $('#shippingForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            container: 'tooltip',
            fields: {
                zip: {
                    validators: {
                        stringLength: {
                            min: 3,
                            max: 10
                        },
                        regexp: {
                            regexp: /^[0-9]+$/,
                            message: 'The zip can only consist of number'
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
            var el = $('#btn_shipping'),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/checkout/shipping',
                type:"POST",
                dataType:"json",
                data: $("#shippingForm").serialize(),
                success: function(data){
                    window.location.href="/checkout/billing";
                },
                fail: function() {
                    l.ladda('stop');
                    el.removeAttr('disabled');
                    alert( "error" );
                }
            });
            return false; // Will stop the submission of the form
        });
        $("#btn_shipping").click(function(){
            $("#shippingForm").submit();
        });
        $(".edit-address").click(function(){
            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

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

                    $('#shippingForm').data('bootstrapValidator').resetForm();
                },
                fail: function() {
                    alert( "error" );
                },
                complete: function () {
                    l.ladda('stop');
                    el.removeAttr('disabled');
                }
            });
        });
        $(".delete-address").click(function(){
            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/checkout/deleteAddress',
                type:"POST",
                dataType:"json",
                data: {address_id: $(this).attr('data-id')},
                success: function(){
                    $(el).parent('div').remove();
                },
                fail: function() {
                    alert( "error" );
                    l.ladda('stop');
                    el.removeAttr('disabled');
                }
            });
        });
        $(".confirm-address").click(function(){
            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/checkout/shipping',
                type:"POST",
                dataType:"json",
                data: {address_id: $(this).attr('data-id'), _csrf_checkout_step2: $(this).data('token') },
                timeout: 10000,
                success: function(){
                    window.location.href="/checkout/billing";
                },
                fail: function() {
                    alert( "error" );
                },
                complete: function () {
                    l.ladda('stop');
                    el.removeAttr('disabled');
                }
            });
        });

        $('#shippingForm').on('change', 'input, select, textarea', function () {
            var form = $(this).closest('form');
            form.find('input[name="address_id"]').val('');
        });
    });



</script>
<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active" id="step2">

    <?php foreach ($this->customerAddresses as $address) :?>
        <div class="col-sm-2 blockShadow">
        <b><?php echo $_($address->full_name, 'full_name') ?></b><br />
        <?php echo $_($address->address_line_1, 'address_line_1') ?><br />
        <?php echo $_($address->address_line_2, 'address_line_2') ?><br />
        <?php echo $_($address->city, 'city')  . ' ' .  $_($address->region, 'region')  . ' ' .  $_($address->zip, 'zip'); ?><br />
        <?php echo $_($address->country_id, 'country_id') ?><br />
        <?php echo $_($address->phone, 'phone') ?><br />
        <button data-id="<?php echo $address->id?>" style="margin-bottom:5px;width:100%" class="btn btn-primary btn-sm confirm-address ladda-button"
                data-token="<?php echo $this->getToken('checkout_step2'); ?>" data-style="expand-right"
                data-spinner-size="20"><span class="ladda-label">Ship to this address</span></button>
        <button data-id="<?php echo $address->id?>" style="width:48%" class="btn btn-default btn-xs edit-address ladda-button small-button"
                data-size="xs" data-spinner-size="16" data-spinner-color="#666666" data-style="expand-right"
                ><span class="ladda-label">Edit</span></button>&nbsp;
        <button data-id="<?php echo $address->id?>" style="width:47%" class="btn btn-default btn-xs delete-address ladda-button small-button"
                data-size="xs" data-spinner-size="16" data-spinner-color="#666666" data-style="expand-right"
                ><span class="ladda-label">Delete</span></button>
        </div>
    <?php endforeach;?>
    <div style="clear:both"></div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <form id="shippingForm" class="form-horizontal well">
                <fieldset>
                    <legend>Add a new address</legend>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="fullName">Full name:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="fullName" name="fullName" required type="text" value="<?php $_($shippingAddress['full_name'], 'fullName'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine1">Address line 1:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="addressLine1" name="addressLine1" type="text" placeholder="Street address, P.O. box, company name, c/o" value="<?php $_($shippingAddress['address_line_1'], 'addressLine1'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine2">Address line 2:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="addressLine2" name="addressLine2" type="text" placeholder="Apartment, suite, unit, building, floor, etc. " value="<?php $_($shippingAddress['address_line_2'], 'addressLine2'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="city">City:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="city" name="city" type="text" value="<?php $_($shippingAddress['city'], 'city'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="region">State/Province/Region:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="region" name="region" type="text" value="<?php $_($shippingAddress['region'], 'region'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="zip">ZIP:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="zip" name="zip" type="text" value="<?php $_($shippingAddress['zip'], 'zip'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 required control-label" for="country_id">Country:</label>
                        <div class="col-xs-8">
                            <select class="form-control" id="country_id" data-validation="required" name="country_id">
                                <option value="RU" <?php echo $shippingAddress['country_id'] == 'RU' ? 'selected' : ''; ?>>Russia</option>
                                <option value="EN" <?php echo $shippingAddress['country_id'] == 'EN' ? 'selected' : ''; ?>>United States</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" data-validation="required" for="phone">Phone number:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="phone" name="phone" type="text" value="<?php $_($shippingAddress['phone'], 'phone'); ?>">
                        </div>
                    </div>
                </fieldset>
                <?php $_token('checkout_step2', false); ?>
                <input type="hidden" id="address_id" name="address_id" value="<?php $_($shippingAddress['id'], 'address_id'); ?>"/>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <button class="btn btn-default" onclick="window.location.href='/cart/view'"><span class="glyphicon glyphicon-chevron-left"></span> Overview</button>
        </div>
        <div class="col-xs-6">
            <button id="btn_shipping" class="btn btn-primary pull-right ladda-button" data-target="#step3" data-toggle="tab"
                    data-style="expand-left"><span class="ladda-label">Ship to this address <span class="glyphicon glyphicon-chevron-right icon-white"></span></span></button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
