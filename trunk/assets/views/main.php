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

        <!-- Fonts -->
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=McLaren">

        <!-- Add custom CSS here -->
        <link href="/css/subcategory.css" rel="stylesheet">
        <link href="/css/modern-business.css" rel="stylesheet">
        <link href="/css/site.css" rel="stylesheet">
        <link href="/css/sidebar.css" rel="stylesheet">
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="/css/ekko-lightbox.css" rel="stylesheet">
        <link href="/css/star-rating.min.css" rel="stylesheet">
        <link href="/css/star-rating.min.css" rel="stylesheet">
        <link href="/css/nivo-slider.css" rel="stylesheet">
        <link href="/css/nivo-themes/bar/bar.css" rel="stylesheet">
        <link href="/css/nivo-themes/light/light.css" rel="stylesheet">


        <!-- JavaScript -->
        <script src="/js/jquery-1.10.2.js"></script>
        <script src="/js/jquery-migrate-1.2.1.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/modern-business.js"></script>
        <script src="/js/jquery.validate.min.js"></script>
        <script src="/js/jquery.inputmask.js"></script>
        <script src="/js/ekko-lightbox.js"></script>
        <script src="/js/jquery.nivo.slider.pack.js"></script>
        <script src="/js/respond.min.js"></script>
        <script src="/js/star-rating.min.js"></script>
        <script src="/js/site.js"></script>
    </head>

    <body>

        <?php include($common_path."header.php")?>

        <?php //include($common_path."topbar.php")?>

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
                        <a class="restore" href="/user/register">New user?</a><br>
                        <a class="forgot" href="/facebook">Login via Facebook</a><br>
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
