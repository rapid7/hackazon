<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">Installation</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/install">Step 1</a></li>
                <li class="active">Step 1</li>
            </ol>
            <div id="checkout-alert-info"></div>
        </div>
    </div>
    <!-- /.row -->
    <?php
    $menuTabs = array(
    	'step1' => array('/install/step1' => '1</span> <em>Database</em>'),
    	'step2' => array('/install/step2' => '2</span> <em>Email</em>'),
    	'step3' => array('/install/step3' => '3</span> <em>Finish</em>')
    );?>
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-pills nav-justified hw-steps-nav">
                <?php
                $disabled = false;
                foreach ($menuTabs as $key => $tab) {
                    $class = $disabled ? 'grey' : '';
                    if ($key == $this->tab) {
                        $class = 'active';
                    }
                    foreach ($tab as $href => $caption) {
                        if ($class == 'active' || $disabled) {
                			echo '<li class="' . $class . '"><a href="#" onclick="return false"><span class="badge badge-info">' . $caption . '</a></li>';
                        } else {
                			echo '<li><a href="' . $href .'"><span class="badge badge-info">' . $caption . '</a></li>';
                        }
                    }
                    if ($key == $this->step) {
                        $disabled = true;
                    }
                }
                ?>
            </ul>
            <div class="tab-content">