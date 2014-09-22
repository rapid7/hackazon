<div class="panel panel-default">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Return to list</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="col-xs-6 col-md-6">
        <?php
        /** @var \App\Admin\FieldFormatter $formatter */
        $formatter->renderFormStart();
        $formatter->renderFields(['productID', 'name', 'categoryID', 'description', 'brief_description', 'Price']);
        $formatter->renderFields();
        $formatter->renderSubmitButtons();
        $formatter->renderFormEnd();
        ?>
        </div>
        <div class="col-xs-6 col-md-6 option-variants-pane js-option-variant-pane">
            <h4>Options</h4>
            <table id="variantList" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr>
                    <th>Option</th>
                    <th>Variant</th>
                    <th>Price surplus</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="variant-edit-block js-variant-edit-block">
                <h4>Add Option</h4>
                <div class="js-add-variant-errors alert alert-danger add-variant-errors"></div>
                <form action="/admin/product-option-value/save" class="js-add-option-variant-form" method="post">
                    <input type="hidden" name="ID" id="field_id" />
                    <input type="hidden" name="productID" id="field_product_id" value="<?php echo $item->id(); ?>" />
                    <?php $fieldFormatter->renderField('optionID'); ?>
                    <?php $fieldFormatter->renderField('variantID'); ?>
                    <div class="form-group">
                        <label for="field_price_surplus">Price Surplus</label><input type="text" class="form-control " required id="field_price_surplus" name="price_surplus">
                    </div>
                    <button type="submit" class="btn btn-primary pull-right js-save-variant-button">Add option</button>
                </form>
            </div>
            <div class="buttons-panel">
                <button type="submit" class="btn btn-primary pull-right js-add-variant-button">Add option</button>
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>


<script type="text/javascript">
    jQuery(function ($) {
        var table = $('#variantList');

        table.dataTable({
            ajax: '/admin/product-option-value?product_id=<?php echo $item->id(); ?>',
            serverSide: true,
            searching: false,
            paging: true,
            order: [ 0, 'asc' ],
            columns: [
                {
                    data: "optionVariant___parentOption___name"
                },
                {
                    data: "optionVariant___name"
                },
                {
                    data: "price_surplus"
                },
                {
                    data: "edit",
                    orderable: false,
                    searching: false
                },
                {
                    data: "delete",
                    orderable: false,
                    searching: false
                }
            ]
        });

        var $optionVariantsPane = $('.js-option-variant-pane'),
            dataTable = table.DataTable(),
            $variantEditBlock = $('.js-variant-edit-block'),
            $variantEditButton = $('.js-add-variant-button'),
            $variantSaveButton = $('.js-save-variant-button'),
            $variantForm = $('.js-add-option-variant-form'),
            $variantFormErrors = $('.add-variant-errors'),
            $optionSelect = $('#field_optionID'),
            $optionVariantsSelect = $('#field_variantID'),
            showEditBlock, hideEditBlock, removeVariant, loadOptionVariants,
            saveButtonText = 'Add option';


        showEditBlock = function (data) {
            $variantEditBlock.show();
            $variantEditButton.text('Hide');
            $variantSaveButton.text(saveButtonText);
            $variantFormErrors.html('').hide();
            var populate = {
                ID: '',
                price_surplus: 0
            };

            if ('object' === typeof data && data) {
                $.extend(populate, data);
                populate.optionID = data.optionVariant___optionID;
            }
            $.each(populate, function (name, value) {
                $variantForm.find('[name="' + name + '"]').val(value);
            });
            $variantForm.data('bootstrapValidator').resetForm();
            if (populate.ID) {
                var id = parseInt(populate.variantID, 10);
                loadOptionVariants(populate.optionID, id)
                .then(function () {
                    $optionVariantsSelect.val(id);
                    $variantForm.data('bootstrapValidator').resetForm();
                });
            }
        };

        hideEditBlock = function () {
            $variantEditBlock.hide();
            $variantEditButton.text('Add option');
        };

        removeVariant = function (id, confirmDeletion) {
            confirmDeletion = !!confirmDeletion;

            $.ajax({
                url: '/admin/product-option-value/delete',
                type: 'post',
                dataType: 'json',
                data: {id: id, confirm: +confirmDeletion}
            }).success(function (res) {
                if (res.error) {
                    if (confirm(res.message)) {
                        removeVariant(id, true);
                    }
                } else {
                    dataTable.ajax.reload();
                }
            }).error(function (/*xhr, responseType, statusText*/) {
                alert('Error while deleting option');
            });
        };

        loadOptionVariants = function (optionId, varToSelectId) {
            var $toDisable = $('.js-add-variant-button, .js-save-variant-button').add($optionSelect),
                deferred = $.Deferred();
            varToSelectId = varToSelectId || null;

            $.ajax({
                url: '/admin/option-value/get-option-values',
                type: 'post',
                dataType: 'json',
                data: {option_id: optionId},
                beforeSend: function () {
                    $toDisable.attr('disabled', 'disabled');
                },
                complete: function () {
                    $toDisable.removeAttr('disabled');
                }
            }).success(function (res) {
                var opts = res.optionVariants, optionsHtml;
                if (opts) {
                    optionsHtml = $.map(opts, function (name, varId) {
                        return '<option value="' + varId + '"'
                            + (varToSelectId && varToSelectId == varId ? " selected" : '') + '>' + name + '</option>';
                    }).join('');
                    $optionVariantsSelect.html(optionsHtml);
                }
                deferred.resolve();

            }).error(function (/*xhr, responseType, statusText*/) {
                deferred.reject();
                alert('Error while loading variants');
            });

            return deferred;
        };

        $optionVariantsPane.off('click', '.js-edit-variant');
        $optionVariantsPane.on('click', '.js-edit-variant', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data();

            saveButtonText = 'Save option';
            showEditBlock(row);
        });

        $optionVariantsPane.on('click', '.js-delete-variant', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data(),
                id = row.ID;

            removeVariant(id);
        });

        $optionVariantsPane.on('click', '.js-add-variant-button', function (ev) {
            ev.preventDefault();
            if ($variantEditBlock.is(':hidden')) {
                saveButtonText = 'Add option';
                showEditBlock();
            } else {
                hideEditBlock();
            }
        });

        $variantForm.hzBootstrapValidator().on('success.form.bv', function(ev) {
            ev.preventDefault();
            $variantSaveButton.attr('disabled', 'disabled');

            $.ajax({
                url: $variantForm.attr('action'),
                type: 'post',
                dataType: 'json',
                data: $variantForm.serialize()
            }).success(function (res) {
                if (res.error) {
                    $variantFormErrors.html(res.message).show();
                } else {
                    dataTable.ajax.reload();
                    hideEditBlock();
                }
            }).error(function (xhr, responseType, statusText) {
                if (statusText) {
                    $variantFormErrors.html(statusText).show();
                }
            }).complete(function () {
                $variantSaveButton.removeAttr('disabled');
            });
        });

        $optionSelect.on('change', function (ev) {
            var id = $optionSelect.val();
            loadOptionVariants(id);
        });
    });
</script>