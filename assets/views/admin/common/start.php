<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hackazon vulnerable website">
    <meta name="author" content="">

    <title>Webscantest Admin<?=(isset($pageTitle) ? " &mdash; " . $pageTitle : "") ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrapValidator.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="/css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="/css/plugins/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <?php if (isset($headCSS)) { echo $headCSS; } ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/js/html5shiv.js"></script>
    <script src="/js/respond-1.4.2.min.js"></script>
    <![endif]-->

    <!-- jQuery Version 1.11.0 -->
    <script src="/js/jquery-1.10.2.js"></script>
    <script src="/js/jquery-migrate-1.2.1.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/bootstrapValidator.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <!--<script src="/js/plugins/morris/raphael.min.js"></script>-->
    <!--<script src="/js/plugins/morris/morris.min.js"></script>-->
    <!--<script src="/js/plugins/morris/morris-data.js"></script>-->
    <script src="/js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="/js/plugins/dataTables/dataTables.bootstrap.js"></script>

    <script src="/js/bootstrap.file-input.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/js/tools.js"></script>
    <script src="/js/sb-admin-2.js"></script>

    <?php if (isset($headScripts)) { echo $headScripts; } ?>
</head>

<body>