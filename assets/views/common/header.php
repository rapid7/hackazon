<?php $baseImgPath = $this->pixie->getParameter('parameters.use_external_dir') ? '/upload/download.php?image=' : '/user_pictures/'; ?>
<header class="hw-header">
    <nav class="navbar hw-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#hw-navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand hw-navbar-brand" href="/"><!--span>Hackazon <em>Webscantest</em></span--><img src="/images/Hackazon.png"></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse hw-navbar-collapse navbar-collapse navbar-ex1-collapse" id="hw-navbar">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/contact">Contact Us</a></li>
                    <?php if (!is_null($this->pixie->auth->user())): ?>
					          <li class="dropdown">
						          <a href="/wishlist" class="dropdown-toggle" data-toggle="dropdown">Wish List <b class="caret"></b></a>
						          <ul class="dropdown-menu">
							          <li><a href="<?php echo $controller->generateUrl('default', array('controller' => 'wishlist')); ?>">Wish Lists</a></li>
						          </ul>
					          </li>
                    <li class="dropdown">
                        <?php
                        /** @var \App\Model\User $curUser */
                        if (($curUser = $this->pixie->auth->user()) && $curUser->photo && $curUser->photo != 'null') {
                            $userImage = $baseImgPath.$curUser->getPhotoPath();
                        } else {
                            $userImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAIAAAHDVQljAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA7NJREFUeNpiXHHi/o/ffxnAgAnI+vvvv5mSMJDBBBT4/fcfRIZFgItNTYIXyLJTFwMIIMYFh+8wwABID1CDGB8HVA8Q3H/9BUgyXn/2/v9/EP/7778AAQTSAzHdUVMcKHTu4bsvP/6A9EO0cLExQxi6MgJQgyHU119Qx1189B7CYLz5/APQHAYMABBAKO6CAxaI95w0xf/8+8/CxLjv+kuE6X/BDvwDM40JTS/E+VDRe6++AklmJkZGRpgoUF5WmBPIABoAMQSL44B2MmG6F6gcIICgYQIXAioCBqOsMNev3/8uP/kAtAnFGGQOGwuTpYoIxHkMHAzAMH3x8cetF58Q0YJi19//UEfjACiqf//79/vPP0SoMjK8+vQDp2pmRsYjt17D3fr47bf3336huxvoM252Fn1ZQS52ZiAbHhoyQlxA7wKNuPv6y91XX0Bhe+v5hz/YIhcrAAgg7DGOFXCwMrMgBzbYZ4wqYjy8nKwP33wFehFfeIvwsuvICPwDO0xbmh+YSPddf8GEFKZMyGlGXYLvH5IfgOb+/487vP8zoHv3P87YYWT4hRQ1EKWMuFT///+fnZUZWQ7oKGDKwa4a6JvbLz4jJxNg7v+NahuK1mcfvrMgBdm1px+ZUEMQPZ28/vITymZifPf1J840+AeUWpgFudjg2cJGTQwoiByILP/BEspiPMqiPMBcjCLHxOiqLfHt598Lj99/+wkqZBjP3n/Dy8GCP2EBvQ4Mg+vPPrFwsjETTINAC4HWaknxMTEQDYBGAgRotcpyGoaBaLxlaVrSQgCpSCyH4Lochj8ugJBAiB9AVC2kVbpkdZ8TBFVSW61UK/KP7fF43jIhd/fPDQjbm0AeqSqmcqqJ1OBmewBWTvUaR6mGA+869Ds2J9Udsqo4ZhjHZ7T8nqe6KxCWG5K9vTkJOmJT1Ypo1Xzac4Z992WktM014beXDwXwBINblFIaEOCU7upqm89BP1qkBbFMNV2k+d6hkaxv867DpT7rQlpXIcyQ6rbos05z1WL1TABJkqyAanU8oAYYYeJv4zlnW44Cusf36OF1YiCYCQfGyCROslyS1puiZTaaJYLtD+NvxUsZeAJZyxYSfU9cDLy8MGmNN84of2Qk7DrngXfUEYLSrT8auTKe3mXozxbZ13Q5jpO0kLSyj//QNXOPffsscMOuC92VpfVHZwOvsQSzHPg2PkQExdNcjuPVaLqCSrFKnj5+0LuKndvkLgPkAWvpwePW5oOwnCmxltahB8KuAf+pyQJVxN7hAAAAAElFTkSuQmCC" id="loginusericon" class="userpic small';
                        } ?>
                        <a href="#" class="dropdown-toggle hw-account-link" data-toggle="dropdown">Your account
                            <img src="<?php echo $userImage; ?>" class="header-user-photo"><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/account<?php echo $useRest ? '#!orders' : '/orders'; ?>">My orders</a></li>
                            <li><a href="/account<?php echo $useRest ? '#!profile' : '#profile'; ?>">My profile</a></li>
                            <li><a href="/account/documents">My documents</a></li>
                            <li><a href="/account/help_articles">Help Articles</a></li>
                            <li><a href="/helpdesk">Helpdesk</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li><a href="<?php echo $controller->generateUrl('default', array('controller' => 'wishlist', 'action' => '')) ?>">Wish List</a>
                    <?php endif ?>
                    <li class="dropdown hw-login-item">
                      <?php if (!is_null($this->pixie->auth->user())): ?>
							        <a href="/user/logout" class="login-window">Logout</a>
                      <?php else: ?>
							        <a href="#login-box" class="login-window">Sign In / Sign Up</a>
                      <?php endif ?>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle js-cart-top-icon" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-shopping-cart"></span></a>
                        <ul class="dropdown-menu js-cart-top-list cart-top-list">
                            <?php
                            $items = $this->pixie->cart->getItems();
                            foreach ($items as $item) {
                            	echo '<li><a href="/product/view?id=' . $item->product_id . '"><span class="pull-left product-name"><small>' . $item->qty . 'x</small> ' . $item->name . '</span> &nbsp; <small class="pull-right label label-info">$ ' . $item->price * $item->qty . ',-</small></a></li>';
                            }
                            ?>
                            <li class="divider"></li>
                            <li><a href="/cart/view">Show all items in shopping cart <i class="glyphicon glyphicon-chevron-right"></i></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-12">
                  <?php include($common_path. "/../search/search.php") ?>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </nav>
</header>