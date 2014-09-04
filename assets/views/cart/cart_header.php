<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">Shopping Cart</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li class="active">My Cart</li>
            </ol>
            <div id="checkout-alert-info"></div>
        </div>
    </div>
    <!-- /.row -->
    <?php
    $menuTabs = array(
    	'overview' => array('/cart/view' => '1</span> <em>Overview</em>'),
    	'shipping' => array('/checkout/shipping' => '2</span> <em>Shipping address</em>'),
    	'billing' => array('/checkout/billing' => '3</span> <em>Billing address</em>'),
    	'confirmation' => array('/checkout/confirmation' => '4</span> <em>Confirmation</em>'),
    	'order' => array('/checkout/order' => '5</span> <em>Place order</em>'),
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