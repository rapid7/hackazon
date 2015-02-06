<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Vulnerability Debugger</title>
    <link rel="stylesheet" href="/css/bootstrap.css"/>
    <link rel="stylesheet" href="/css/bootstrap-theme.css"/>
    <link rel="stylesheet" href="/css/sb-admin-2.css"/>
    <link rel="stylesheet" href="/css/vuln.css"/>
    <!-- MetisMenu CSS -->
    <link href="/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet" />

    <script type="text/javascript" src="/js/jquery-1.10.2.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="/js/plugins/metisMenu/metisMenu.min.js"></script>
    <script src="/js/bootstrap.file-input.js"></script>

    <script type="text/javascript">
        jQuery(function ($) {
            $(document).on('click', '.js-show-computed-vulns', function (ev) {
                ev.preventDefault();

                var el = $(ev.target),
                    container = el.next('.js-computed-vulns');

                if (container.is(':visible')) {
                    container.hide();
                } else {
                    container.show();
                }
            });
        });
    </script>
</head>
<body>
    <header>

    </header>
    <main class="container">
        <h1>Context tree</h1>

        <?php echo $result; ?>
    </main>
</body>
</html>