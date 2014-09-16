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
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=McLaren">
    <link href='//fonts.googleapis.com/css?family=Ubuntu:300,400,500,700,300italic,400italic,500italic,700italic' rel='stylesheet' type='text/css'>
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Libraries -->
    <link href="/css/ekko-lightbox.css" rel="stylesheet">
    <link href="/css/star-rating.min.css" rel="stylesheet">
    <link href="/css/nivo-slider.css" rel="stylesheet">
    <link href="/css/nivo-themes/bar/bar.css" rel="stylesheet">
    <link href="/css/nivo-themes/light/light.css" rel="stylesheet">
    <link href="/css/bootstrapValidator.css" rel="stylesheet">
    <link href="/css/modern-business.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/ladda-themeless.min.css">

    <!-- Add custom CSS here -->
    <link href="/css/subcategory.css" rel="stylesheet">
    <link href="/css/site.css" rel="stylesheet">
    <link href="/css/sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        var App = window.App || {};
        App.config = <?php echo json_encode([
            'host' => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST']
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

    <script src="/js/site.js"></script>
    <?php if (isset($headScripts)) { echo $headScripts; } ?>
</head>
<body class="<?php $_(isset($bodyClass) ? $bodyClass : ''); ?>">
