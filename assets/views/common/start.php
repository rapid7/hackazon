<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Hackazon <?=(isset($pageTitle) ? " &mdash; " . $pageTitle : "") ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.css" rel="stylesheet">

    <!-- Fonts -->
    <!--link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=McLaren">
    <link href='//fonts.googleapis.com/css?family=Ubuntu:300,400,500,700,300italic,400italic,500italic,700italic' rel='stylesheet' type='text/css'-->
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Libraries -->
    <link href="/css/ekko-lightbox.css" rel="stylesheet">
    <link href="/css/star-rating.min.css" rel="stylesheet">
    <link href="/css/nivo-slider.css" rel="stylesheet">
    <link href="/css/nivo-themes/bar/bar.css" rel="stylesheet">
    <link href="/css/nivo-themes/light/light.css" rel="stylesheet">
    <link href="/css/bootstrapValidator.css" rel="stylesheet">
    <link href="/css/modern-business.css" rel="stylesheet">
    <link href="/css/ladda-themeless.min.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="/css/subcategory.css" rel="stylesheet">
    <link href="/css/site.css" rel="stylesheet">
    <link href="/css/sidebar.css" rel="stylesheet">

    <?php
    $currentUser = $this->pixie->auth->user();
    ?>
    <script type="text/javascript">
        var App = window.App || {};
        App.config = <?php echo json_encode([
            'host' => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'],
            'user' => $currentUser ? $currentUser->getPublicData() : null,
            'baseImgPath' => $this->pixie->getParameter('parameters.use_external_dir') ? '/upload/download.php?image=' : '/user_pictures/',
            'dataType' => $this->pixie->getParameter('parameters.profile_rest_data_type', 'xml')
        ]);?>;
    </script>

    <!-- JavaScript -->
    <script src="/js/jquery-1.10.2.js"></script>
    <script src="/js/json3.min.js"></script>
    <script src="/js/jquery.dump.js"></script>
    <script src="/js/jquery-migrate-1.2.1.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/modern-business.js"></script>
    <script src="/js/bootstrapValidator.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
    <script src="/js/spin.min.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/jquery.modern-blink.js"></script>
    <script src="/js/ladda.min.js"></script>
    <script src="/js/ladda.jquery.min.js"></script>
    <script src="/js/jquery.inputmask.js"></script>
    <script src="/js/ekko-lightbox.js"></script>
    <script src="/js/jquery.nivo.slider.pack.js"></script>
    <script src="/js/respond.min.js"></script>
    <script src="/js/star-rating.min.js"></script>
    <script src="/js/bootstrap.file-input.js"></script>
    <script src="/js/knockout-2.2.1.js"></script>
    <script src="/js/knockout.localStorage.js"></script>
    <script src="/js/koExternalTemplateEngine_all.min.js"></script>
    <script src="/js/amf/services.js"></script>
    <script src="/js/swfobject.js"></script>

    <script src="/js/tools.js"></script>
    <script src="/js/site.js"></script>

    <script type="text/javascript">
        // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
        var swfVersionStr = "11.1.0";
        // To use express install, set to playerProductInstall.swf, otherwise the empty string.
        var xiSwfUrlStr = "/swf/playerProductInstall.swf";
        var flashvars = {
            host: "<?php echo $_SERVER['HTTP_HOST'] ? 'http://'.$_SERVER['HTTP_HOST'] : $this->pixie->config->get('parameters.host'); ?>"
        };
        var params = {};
        params.quality = "high";
        params.bgcolor = "#ffffff";
        params.allowscriptaccess = "sameDomain";
        params.allowfullscreen = "false";
        var attributes = {};
        attributes.id = "SliderBanner";
        attributes.name = "SliderBanner";
        attributes.align = "middle";

        $(function () {
            if ($('#flashBanner').length) {
                setTimeout(function () {
                    swfobject.embedSWF(
                        "/swf/SliderBanner.swf", "flashBanner",
                        "360", "290",
                        swfVersionStr, xiSwfUrlStr,
                        flashvars, params, attributes);
                    // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
                    swfobject.createCSS("#flashBanner", "display:block;text-align:left;");
                }, 300);
            }
        });
    </script>

    <?php if (isset($headScripts)) { echo $headScripts; } ?>
</head>
<body class="<?php $_(isset($bodyClass) ? $bodyClass : ''); ?>">
