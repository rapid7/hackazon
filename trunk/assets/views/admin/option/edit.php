<div class="panel panel-default product-option-page">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Return to list</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body product-option">
        <div class="col-xs-6 col-md-6">
            <?php
            /** @var \App\Admin\FieldFormatter $formatter */
            $formatter->renderForm();
            ?>
        </div>
        <div class="col-xs-6 col-md-6 option-variants-pane js-option-variant-pane">
            <h4>Option Variants:</h4>
            <div class="table-responsive option-list">
                <table id="variantList" class="table table-striped table-bordered table-hover dataTable no-footer">
                    <thead>
                        <tr role="row">
                            <td>Id</td>
                            <td>Variant</td>
                            <td>Sort Order</td>
                            <td>Edit</td>
                            <td>Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="variant-edit-block js-variant-edit-block">
                <h4>Add Option Variant</h4>
                <div class="js-add-variant-errors alert alert-danger add-variant-errors"></div>
                <form action="/admin/option-value/save" class="js-add-option-variant-form" method="post">
                    <input type="hidden" name="variantID" id="field_variant_id" />
                    <input type="hidden" name="optionID" id="field_option_id" value="<?php echo $item->id(); ?>" />
                    <div class="form-group">
                        <label for="field_name">Name</label><input type="text" class="form-control " required id="field_variant_name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="field_name">Sort Order</label><input type="text" class="form-control " required id="field_variant_sort_order" name="sort_order">
                    </div>
                    <button type="submit" class="btn btn-primary pull-right js-save-variant-button">Add variant</button>
                </form>
            </div>
            <div class="buttons-panel">
                <button type="submit" class="btn btn-primary pull-right js-add-variant-button">Add variant</button>
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>

<script type="text/javascript">
    jQuery(function ($) {
        var table = $('#variantList');

        table.dataTable({
            ajax: '/admin/option-value?option_id=<?php echo $item->id(); ?>',
            serverSide: true,
            searching: false,
            paging: true,
            order: [ 1, 'asc' ],
            columns: [
                {
                    data: "variantID"
                },
                {
                    data: "name"
                },
                {
                    data: "sort_order"
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
            showEditBlock, hideEditBlock, removeVariant,
            saveButtonText = 'Add variant';


        showEditBlock = function (data) {
            $variantEditBlock.show();
            $variantEditButton.text('Hide');
            $variantSaveButton.text(saveButtonText);
            $variantFormErrors.html('').hide();
            var populate = {
                variantID: '',
                name: '',
                sort_order: 0
            };

            if ('object' === typeof data && data) {
                $.extend(populate, data);
            }
            $.each(populate, function (name, value) {
                $variantForm.find('[name="' + name + '"]').val(value);
            });

            $variantForm.data('bootstrapValidator').resetForm();
        };

        hideEditBlock = function () {
            $variantEditBlock.hide();
            $variantEditButton.text('Add variant');
        };

        removeVariant = function (id, confirmDeletion) {
            confirmDeletion = !!confirmDeletion;

            $.ajax({
                url: '/admin/option-value/delete',
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
            }).error(function (xhr, responseType, statusText) {
                alert('Error while deleting option variant');
            });
        };
        $optionVariantsPane.off('click', '.js-edit-variant');
        $optionVariantsPane.on('click', '.js-edit-variant', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data();

            saveButtonText = 'Save variant';
            showEditBlock(row);
        });

        $optionVariantsPane.on('click', '.js-delete-variant', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data(),
                id = row.variantID;

            removeVariant(id);
        });

        $optionVariantsPane.on('click', '.js-add-variant-button', function (ev) {
            ev.preventDefault();
            if ($variantEditBlock.is(':hidden')) {
                saveButtonText = 'Add variant';
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
                dataTable.ajax.reload();
                hideEditBlock();
            }).error(function (xhr, responseType, statusText) {
                if (statusText) {
                    $variantFormErrors.html(statusText).show();
                }
            }).complete(function () {
                $variantSaveButton.removeAttr('disabled');
            });
        });
    });
</script>

