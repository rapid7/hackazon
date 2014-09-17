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
        <div class="col-xs-6 col-md-6">
            <h4>Options</h4><?php
            foreach ($options as $optionId => $option) { ?>
                <div><label><?php $_($option['name']); ?></label><br>
                    <?php
                    foreach ($option['variants'] as $optVariantId => $optVariantName) {
                        ?>
                        <label class="option-label"><input type="checkbox" name="options[<?php echo $optionId; ?>][]" value="<?php echo $optVariantId; ?>"
                                                           <?php if (array_key_exists($optVariantId, [])): ?>checked <?php endif; ?>/> <?php echo $optVariantName; ?>
                        </label>
                    <?php
                    } ?>
                </div>
            <?php
            } ?>
        </div>
    </div>
    <!-- /.panel-body -->
</div>

