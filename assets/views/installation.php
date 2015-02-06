<?php include($common_path . "start.php"); ?>
<div id="container" class="installation">
    <div class="container">
        <div class="row">
            <h1 class="text-center">Hackazon Installation Wizard</h1>
        </div>
    </div>
    <?php if (isset($steps)): ?>
        <div class="container">
            <div class="row step-meter">
                <div class="row bs-wizard" style="border-bottom:0;">
                    <?php foreach ($steps as $stepName => $stepData): ?>
                        <div class="col-xs-<?php echo count($steps) == 3 ? 4 : 3; ?> bs-wizard-step <?php if ($stepData['started']) { echo 'complete'; } else { echo "disabled"; }
                                if ($stepData['is_last_started']) { echo ' active'; } ?>">
                            <div class="text-center bs-wizard-stepnum <?php if ($stepData['current']) { echo 'active'; } ?>">
                                <?php $_($stepData['title']); ?>
                            </div>
                            <div class="progress"><div class="progress-bar"></div></div>
                            <a href="/install/<?php $_($stepName); ?>" class="bs-wizard-dot"></a>
                            <!--div class="bs-wizard-info text-center">Lorem ipsum dolor sit amet.</div-->
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="col-md-6 col-md-offset-3">
            <?php if (isset($steps)): ?>
                <h3><?php $_($step->getTitle()); ?></h3>
                <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong><?php echo $errorMessage; ?></strong>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php include($subview . ".php"); ?>
        </div>
        <div class="col-md-6 col-md-offset-3" style="text-align: center;">
            <br/><br/><br/><br/>
            <a href="/install?force=1">Restart installation</a>
        </div>
    </div>
</div>
<?php include($common_path . "end.php"); ?>