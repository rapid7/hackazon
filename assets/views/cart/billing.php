<script>
    $(function () {
        $('#billingForm').bootstrapValidator({
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
            var el = $('#btn_billing'),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/checkout/billing',
                type:"POST",
                data: $("#billingForm").serialize(),
                success: function(data){
                    window.location.href="/checkout/confirmation";
                },
                fail: function() {
                    l.ladda('stop');
                    el.removeAttr('disabled');
                    alert( "error" );
                }
            });
            return false; // Will stop the submission of the form
        });
        $("#btn_billing").click(function(){
            $("#billingForm").submit();
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

                    $('#billingForm').data('bootstrapValidator').resetForm();
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
                data: {address_id: $(this).attr('data-id')},
                success: function(){
                    $(el).closest('.blockShadow').remove();
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
                url:'/checkout/billing',
                type:"POST",
                data: {address_id: $(this).attr('data-id'), _csrf_checkout_step3: $(this).data('token') },
                success: function(){
                    window.location.href="/checkout/confirmation";
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

        $('#billingForm').on('change', 'input, select, textarea', function () {
            var form = $(this).closest('form');
            form.find('input[name="address_id"]').val('');
        });
    });
</script>
<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active" id="step3">
    <div style="clear:both"></div>
    <div class="row">
        <div class="col-xs-8 col-sm-8">
            <form id="billingForm" class="form-horizontal well">
                <fieldset>
                    <legend>Add a new address</legend>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="fullName">Full name:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="fullName" name="fullName" required type="text" value="<?php $_(''.$billingAddress['full_name']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine1">Address line 1:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="addressLine1" name="addressLine1" type="text" placeholder="Street address, P.O. box, company name, c/o"
                                   value="<?php $_(''.$billingAddress['address_line_1']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine2">Address line 2:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="addressLine2" name="addressLine2" type="text" placeholder="Apartment, suite, unit, building, floor, etc. "
                                   value="<?php $_(''.$billingAddress['address_line_2']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="city">City:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="city" name="city" type="text" value="<?php $_(''.$billingAddress['city']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="region">State/Province/Region:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="region" name="region" type="text" value="<?php $_(''.$billingAddress['region']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 control-label" for="zip">ZIP:</label>
                        <div class="col-xs-8">
                            <input class="form-control" required id="zip" name="zip" type="text" value="<?php $_(''.$billingAddress['zip']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label required class="col-xs-4 required control-label" for="country_id">Country:</label>
                        <div class="col-xs-8">
                            <select class="form-control" id="country_id" data-validation="required" name="country_id">
                                <option value="EN" <?php echo ''.$billingAddress['country_id'] == 'EN' ? 'selected' : ''; ?>>United States</option>
                                <option value="RU" <?php echo ''.$billingAddress['country_id'] == 'RU' ? 'selected' : ''; ?>>Russia</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" data-validation="required" for="phone">Phone number:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="phone" name="phone" type="text" value="<?php $_(''.$billingAddress['phone']); ?>">
                        </div>
                    </div>
                </fieldset>
                <?php $_token('checkout_step3', false); ?>
                <input type="hidden" id="address_id" name="address_id" value="<?php $_(''.$billingAddress['uid']); ?>"/>
                <input type="hidden" id="full_form" name="full_form" value="1"/>
            </form>
        </div>
		<div class="col-xs-4">
		<?php foreach ($this->customerAddresses as $address) :?>
			<div class="blockShadow bg-info">
				<b><?php echo $_($address->getWrapperOrValue('full_name')); ?></b><br />
				<?php echo $_($address->getWrapperOrValue('address_line_1')); ?><br />
				<?php echo $_($address->getWrapperOrValue('address_line_2')); ?><br />
				<?php echo $_($address->getWrapperOrValue('city'))  . ' ' .  $_(''.$address->getWrapperOrValue('region'))
                    . ' ' .  $_($address->getWrapperOrValue('zip')) ?><br />
				<?php echo $_($address->getWrapperOrValue('country_id')); ?><br />
				<?php echo $_($address->getWrapperOrValue('phone')); ?><br />
				<div class="row">
					<div class="col-xs-12" style="margin-bottom: 10px;">
						<button data-id="<?php echo $address->getUid(); ?>" class="btn btn-primary btn-block confirm-address ladda-button" data-token="<?php echo $this->getToken('checkout_step3'); ?>" data-style="expand-right" data-spinner-size="20"><span class="ladda-label">Bill to this address</span></button>
					</div>
				</div>
                <?php if ($address->id()): ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <button data-id="<?php echo $address->getUid(); ?>" class="btn btn-success btn-block edit-address ladda-button small-button" data-size="xs" data-spinner-size="16" data-spinner-color="#666666" data-style="expand-right"><span class="ladda-label">Edit</span></button>
                            </div>
                            <div class="col-xs-6">
                            <button data-id="<?php echo $address->getUid(); ?>" class="btn btn-danger btn-block delete-address ladda-button small-button" data-size="xs" data-spinner-size="16" data-spinner-color="#666666" data-style="expand-right"><span class="ladda-label">Delete</span></button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach;?>
		</div>
    </div>

    <div class="row">

        <div class="col-xs-6">
            <button class="btn btn-default" onclick="window.location.href='/checkout/shipping'"><span class="glyphicon glyphicon-chevron-left"></span> Shipping Step</button>
        </div>
        <div class="col-xs-6">
            <button id="btn_billing" class="btn btn-primary pull-right ladda-button" data-target="#step3" data-toggle="tab" data-style="expand-left"><span class="ladda-label">Confirmation Step <span class="glyphicon glyphicon-chevron-right icon-white"></span></span></button>
        </div>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
