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
        <link href="/css/bootstrapValidator.css" rel="stylesheet">

        <!-- JavaScript -->
        <script src="/js/jquery-1.10.2.js"></script>
        <script src="/js/jquery-migrate-1.2.1.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/modern-business.js"></script>
        <script src="/js/bootstrapValidator.min.js"></script>
        <script src="/js/jquery.inputmask.js"></script>
        <script src="/js/ekko-lightbox.js"></script>
        <script src="/js/jquery.nivo.slider.pack.js"></script>
        <script src="/js/respond.min.js"></script>
        <script src="/js/star-rating.min.js"></script>
        <script src="/js/site.js"></script>
        
        <script src="/js/knockout-2.2.1.js"></script>
        <script src="/js/knockout.localStorage.js"></script>
        <script src="/js/koExternalTemplateEngine_all.min.js"></script>  
        
        <link rel="stylesheet" href="/css/ladda-themeless.min.css">
        <script src="/js/spin.min.js"></script>
        <script src="/js/ladda.min.js"></script>
    </head>

    <body>

        <?php include($common_path . "header.php") ?>

        <?php //include($common_path."topbar.php")?>

        <div id="container">
            <?php include($subview . ".php") ?>
        </div>


        <div class="container" >
            <?php include($common_path . "footer.php") ?>
        </div>
        <!-- /.container -->



        <div id="login-box" class="login-popup">
            <a href="#" class="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="glyphicon glyphicon-remove"></i></a>
            <form role="form" method="post" class="signin" action="/user/login" id="loginForm">
                <h2>Please login <small></small></h2>
                <hr class="colorgraph">

                <div class="form-group">
                    <input type="text" maxlength="100" required name="username" id="username" class="form-control input-lg" placeholder="Username or Email" tabindex="1">
                </div>

                <div class="form-group">
                    <input type="password" maxlength="100" required name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="5">
                </div>


                <hr class="colorgraph">
                <div class="row">
                    <div class="col-xs-6 col-md-6"><button id="loginbtn"  type="submit" class="btn btn-success btn-block btn-lg">Sign In</button></div>
                    <div class="col-xs-6 col-md-6">
                        <div>
                            <span class="login-social-span">Or login via</span>
                            <ul class="list-unstyled list-inline list-social-icons">

                                <li class="tooltip-social facebook-link"><a href="/facebook" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fa fa-facebook-square fa-3x"></i></a></li>
                                <li class="tooltip-social twitter-link"><a href="/twitter" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter-square fa-3x"></i></a></li>
                            </ul>
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-md-6"><a href="/user/password" class="btn btn-info btn-lg" style="width:100%">Forgot your password?</a></div>
                    <div class="col-xs-6 col-md-6"><a href="/user/register" class="btn btn-info btn-lg" style="width:100%">New user?</a></div>
                </div>
            </form>
        </div>

        <script>
            //A very basic way to open a popup

            function popup(link, windowname) {
                window.open(link.href, windowname, 'width=400,height=200,scrollbars=yes');
                return false;
            }

        </script>


        <script>

            jQuery(function ($) {
                $('#loginForm').bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    container: 'tooltip'
                });
            });

        </script>        

    </body>

</html>
