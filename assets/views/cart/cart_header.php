<div class="container">

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">Shopping Cart</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">My Cart</li>
            </ol>
            <div id="checkout-alert-info"></div>
        </div>


    </div>
    <!-- /.row -->
    <?php
    $menuTabs = array(
        'overview' => array('/cart/view' => 'step 1:</b> Overview'),
        'shipping' => array('/checkout/shipping' => 'step 2:</b> Shipping address'),
        'billing' => array('/checkout/billing' => 'step 3:</b> Billing address'),
        'confirmation' => array('/checkout/confirmation' => 'step 4:</b> Confirmation'),
        'order' => array('/checkout/order' => 'step 5:</b> Place order'),
    );?>
    <div class="row">
        <div class="col-xs-12">

            <ul class="nav nav-tabs">
                <?php
                $disabled = false;
                foreach ($menuTabs as $key => $tab) {
                    $class = $disabled ? 'grey' : '';
                    if ($key == $this->tab) {
                        $class = 'active';
                    }
                    foreach ($tab as $href => $caption) {
                        if ($class == 'active' || $disabled) {
                            echo '<li class="' . $class . '"><a href="#" onclick="return false"><b>' . $caption . '</a></li>';
                        } else {
                            echo '<li><a href="' . $href .'"><b>' . $caption . '</a></li>';
                        }
                    }
                    if ($key == $this->step) {
                        $disabled = true;
                    }
                }
                ?>
            </ul>

            <div class="tab-content">