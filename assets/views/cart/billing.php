<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active" id="step3">

<div class="tab-pane" id="step3">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <form class="form-horizontal well">
                <fieldset>
                    <legend>Add a new address</legend>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="fullname">Full name:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="fullname" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine1">Address line 1:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="addressLine1" type="text" placeholder="Street address, P.O. box, company name, c/o">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="addressLine2">Address line 2:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="companytaxregister" type="text" placeholder="Apartment, suite, unit, building, floor, etc. ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="city">City:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="city" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="state">State/Province/Region:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="state" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="zip">ZIP:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="zip" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="country">Country:</label>
                        <div class="col-xs-8">
                            <select class="form-control" id="country">
                                <option value="RU">Russia</option>
                                <option value="EN">United States</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="companyemail">Phone number:</label>
                        <div class="col-xs-8">
                            <input class="form-control" id="companyemail" type="text" placeholder="naam@domein.nl">
                        </div>
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
            <button class="btn btn-primary pull-right" data-target="#step3" data-toggle="tab">Use this address <span class="glyphicon glyphicon-chevron-right icon-white"></span></button>
        </div>
    </div>
</div>
    <?php include __DIR__ . '/cart_footer.php'; ?>
