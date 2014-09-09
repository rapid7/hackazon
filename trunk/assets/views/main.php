<?php include($common_path . "start.php") ?>
    <?php include($common_path . "header.php") ?>

    <div id="container">
        <?php include($subview . ".php") ?>
    </div>

    <div class="container" >
        <?php include($common_path . "footer.php") ?>
    </div>
    <?php if (is_null($this->pixie->auth->user())): ?>
        <div id="login-box" class="login-popup">
            <a href="#" class="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="glyphicon glyphicon-remove"></i></a>
            <form role="form" method="post" class="signin" action="/user/login" id="loginForm">
                <h2>Please login <small></small></h2>
                <hr class="colorgraph">
                <div class="form-group">
                    <input type="text" maxlength="100" required name="username" id="username" autocomplete="off" class="form-control input-lg" placeholder="Username or Email" tabindex="1">
                </div>
                <div class="form-group">
                    <input type="password" maxlength="100" required name="password" autocomplete="off" id="password" class="form-control input-lg" placeholder="Password" tabindex="5">
                </div>
                <hr class="colorgraph">
                <div class="row">
                    <div class="col-xs-6 col-md-6"><button id="loginbtn" type="submit" class="btn btn-success btn-block btn-lg">Sign In</button></div>
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
    <?php endif ?>
<?php include($common_path . "end.php") ?>