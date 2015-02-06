<?php if (isset($conditionList) && is_array($conditionList) && count($conditionList)): ?>
    <div class="ve-conditions">
        <table class="table">
            <?php
            $className = 'even';
            ?>
            <?php foreach ($conditionList as $conditionName => $condition):
                $propCount = max((count($condition) - 1), 1);
                $renderedName = false;
                $className = $className == 'odd' ? 'even' : 'odd';
                ?>
                <?php if (is_array($condition)): ?>
                    <?php foreach ($condition as $propName => $propValue): ?>
                    <?php if ($propName == 'name') { continue; } ?>
                    <?php $renderName = false; ?>
                    <?php if (!$renderedName): $renderName = true; ?><?php endif; ?>
                    <tr class="<?php echo $className; ?>">
                        <?php if ($renderName): $renderedName = true; ?>
                            <td rowspan="<?php echo $propCount?>"><strong><?php $_($conditionName); ?></strong></td>
                        <?php endif; ?>
                        <td><?php $_($propName); ?></td>
                        <td><?php $_(is_bool($propValue) ? ($propValue ? 'Yes' : 'No') : (is_scalar($propValue) ? $propValue : 'not scalar')); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="<?php echo $className; ?>">
                        <td><strong><?php $_($conditionName); ?></strong></td>
                        <td colspan="2"><?php $_(is_bool($condition) ? ($condition ? 'Yes' : 'No') : $condition); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>