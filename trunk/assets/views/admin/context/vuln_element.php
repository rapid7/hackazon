<?php if ((!$isRoot && $conditionList) || count($vulnerabilities) || $childrenVulns || $computedVulnerabilities): ?>
<div class="vulnerability-element panel panel-yellow">
    <div class="panel-heading">
        Vulnerabilities
    </div>
    <div class="panel-body">
        <?php if (!$isRoot): ?>
            <?php if ($conditionList): ?>
                <h4>Conditions:</h4>
                <div class="ve-conditions context-subsection">
                    <?php include __DIR__ . '/condition_list.php'; ?>
                </div>
                <hr/>
            <?php endif; ?>
        <?php endif; ?>

        <?php $vulnList = $vulnerabilities; ?>
        <?php include __DIR__ . '/vulnerability_list.php'; ?>

        <?php if (isset($computedVulnerabilities)): ?>
            <a href="#" class="js-show-computed-vulns">Show computed</a>
            <div class="js-computed-vulns computed-vulns">
                <?php $vulnList = $computedVulnerabilities; ?>
                <?php include __DIR__ . '/vulnerability_list.php'; ?>
            </div>
        <?php endif; ?>


        <?php if ($childrenVulns): ?>
            <h4>Children:</h4>
            <div class="ve-children context-subsection">
                <?php echo $childrenVulns;?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
