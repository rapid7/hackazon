<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Webscantest</title>

        <!-- Bootstrap core CSS -->
        <link href="/css/bootstrap.css" rel="stylesheet">

        <!-- Add custom CSS here -->
        <link href="/css/subcategory.css" rel="stylesheet">
        <link href="/css/modern-business.css" rel="stylesheet">
        <link href="/css/site.css" rel="stylesheet">
        <link href="/css/sidebar.css" rel="stylesheet">
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">

        <!-- JavaScript -->
        <script src="/js/jquery-1.10.2.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/modern-business.js"></script>
        <script src="/js/jquery.validate.min.js"></script>
        <script src="/js/site.js"></script>
        <script src="/js/jquery.inputmask.js"></script>

    </head>

    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- You'll want to use a responsive image option so this logo looks good on devices - I recommend using something like retina.js (do a quick Google search for it and you'll find it) -->
                    <a class="navbar-brand" href="/">Modern Business</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/about">About</a>
                        </li>
                        <li><a href="/product">Products</a>
                        </li>
                        <li><a href="/contact">Contact</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Portfolio <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="portfolio-1-col.html">1 Column Portfolio</a>
                                </li>
                                <li><a href="portfolio-2-col.html">2 Column Portfolio</a>
                                </li>
                                <li><a href="portfolio-3-col.html">3 Column Portfolio</a>
                                </li>
                                <li><a href="portfolio-4-col.html">4 Column Portfolio</a>
                                </li>
                                <li><a href="portfolio-item.html">Single Portfolio Item</a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="/blog">Blog</a>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Other Pages <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="full-width.html">Full Width Page</a>
                                </li>
                                <li><a href="sidebar.html">Sidebar Page</a>
                                </li>
                                <li><a href="/faq">FAQ</a>
                                </li>
                                <li><a href="404.html">404</a>
                                </li>
                                <li><a href="pricing.html">Pricing Table</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                </div>
                <!-- /.navbar-collapse -->
                <div id="logincontrol">
                    <div id="loginuser">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAIAAAHDVQljAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA7NJREFUeNpiXHHi/o/ffxnAgAnI+vvvv5mSMJDBBBT4/fcfRIZFgItNTYIXyLJTFwMIIMYFh+8wwABID1CDGB8HVA8Q3H/9BUgyXn/2/v9/EP/7778AAQTSAzHdUVMcKHTu4bsvP/6A9EO0cLExQxi6MgJQgyHU119Qx1189B7CYLz5/APQHAYMABBAKO6CAxaI95w0xf/8+8/CxLjv+kuE6X/BDvwDM40JTS/E+VDRe6++AklmJkZGRpgoUF5WmBPIABoAMQSL44B2MmG6F6gcIICgYQIXAioCBqOsMNev3/8uP/kAtAnFGGQOGwuTpYoIxHkMHAzAMH3x8cetF58Q0YJi19//UEfjACiqf//79/vPP0SoMjK8+vQDp2pmRsYjt17D3fr47bf3336huxvoM252Fn1ZQS52ZiAbHhoyQlxA7wKNuPv6y91XX0Bhe+v5hz/YIhcrAAgg7DGOFXCwMrMgBzbYZ4wqYjy8nKwP33wFehFfeIvwsuvICPwDO0xbmh+YSPddf8GEFKZMyGlGXYLvH5IfgOb+/487vP8zoHv3P87YYWT4hRQ1EKWMuFT///+fnZUZWQ7oKGDKwa4a6JvbLz4jJxNg7v+NahuK1mcfvrMgBdm1px+ZUEMQPZ28/vITymZifPf1J840+AeUWpgFudjg2cJGTQwoiByILP/BEspiPMqiPMBcjCLHxOiqLfHt598Lj99/+wkqZBjP3n/Dy8GCP2EBvQ4Mg+vPPrFwsjETTINAC4HWaknxMTEQDYBGAgRotcpyGoaBaLxlaVrSQgCpSCyH4Lochj8ugJBAiB9AVC2kVbpkdZ8TBFVSW61UK/KP7fF43jIhd/fPDQjbm0AeqSqmcqqJ1OBmewBWTvUaR6mGA+869Ds2J9Udsqo4ZhjHZ7T8nqe6KxCWG5K9vTkJOmJT1Ypo1Xzac4Z992WktM014beXDwXwBINblFIaEOCU7upqm89BP1qkBbFMNV2k+d6hkaxv867DpT7rQlpXIcyQ6rbos05z1WL1TABJkqyAanU8oAYYYeJv4zlnW44Cusf36OF1YiCYCQfGyCROslyS1puiZTaaJYLtD+NvxUsZeAJZyxYSfU9cDLy8MGmNN84of2Qk7DrngXfUEYLSrT8auTKe3mXozxbZ13Q5jpO0kLSyj//QNXOPffsscMOuC92VpfVHZwOvsQSzHPg2PkQExdNcjuPVaLqCSrFKnj5+0LuKndvkLgPkAWvpwePW5oOwnCmxltahB8KuAf+pyQJVxN7hAAAAAElFTkSuQmCC" id="loginusericon" class="userpic small">
                        <?php if (!is_null($this->pixie->auth->user())): ?>
                            <a href="/user/logout" class="login-window" style="color:white">Logout</a>
                        <?php else: ?>
                            <a href="#login-box" class="login-window" style="color:white">Login / Sign In</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <!-- /.container -->
        </nav>
        <?php if (isset($categories) && !is_null($categories)): ?>
            <div class="navbar" role="navigation">
                <div class="container">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse navbar-ex1-collapse">
                        <ul class="nav navbar-nav">
                            <?php foreach ($categories as $value): ?>
                                <li><a href="/product/category/<?php echo $value->categoryID; ?>"><?php echo $value->name; ?></a></li>
                            <?php endforeach; ?>    
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container -->
            </div>
        <?php endif ?>

        <div id="container">
            <?php include($subview . ".php") ?>
        </div>


        <div class="container" >
            <?php include($common_path."footer.php")?>
        </div>
        <!-- /.container -->

        <div id="login-box" class="login-popup">
            <a href="#" class="close"><img src="/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>
            <form method="post" class="signin" action="/user/login" id="loginForm">
                <fieldset class="textbox">
                    <label class="username">
                        <span>Username or email</span>
                        <input id="username" name="username" value="" type="text" autocomplete="on" placeholder="Username">
                    </label>
                    <br/>
                    <label class="password">
                        <span>Password</span>
                        <input id="password" name="password" value="" type="password" placeholder="Password">
                    </label>
                    <button id="loginbtn" class="submit button" type="submit">Sign in</button>
                    <p>
                        <a class="forgot" href="/user/password">Forgot your password?</a>
                        <a class="restore" href="/user/register">New user?</a></br>
                        <a class="forgot" href="/facebook">Login via Facebook</a></br>
                        <a class="forgot" href="/twitter">Login via Twitter</a>
                    </p>        
                </fieldset>
            </form>
        </div>

        <!--<div>
            <a href="/facebook/popup" onclick="return popup(this,'fblogin')">Login via Facebook Popup</a>
        </div>-->

        <script>
            //A very basic way to open a popup

            function popup(link, windowname) {
                window.open(link.href, windowname, 'width=400,height=200,scrollbars=yes');
                return false;
            }

        </script>


        <script>

    // When the browser is ready...
    $(function() {

      // Setup form validation on the #register-form element
      $("#loginForm").validate({
          // Specify the validation rules
          rules: {
              username: {
                  required: true
              },
              password: "required"
          },
          // Specify the validation error messages
          messages: {
              username: "Please enter username or valid email address",
              password: "Please enter your password"
          },

          submitHandler: function(form) {
              form.submit();
          }
      });

    });

    </script>        
        
    </body>

</html>
