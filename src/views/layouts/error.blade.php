<!doctype html>
<html lang="<?php echo App::getLocale(); ?>">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, user-scalable=no" />
<title><?php echo $errorTitle; ?></title>
<link rel="shortcut icon" href="<?php echo URL::to('favicon.ico'); ?>">

<?php echo HTML::style(adminCssAssetURL("bootstrap/bootstrap.min.css")); ?>
<?php echo HTML::style(urlRoute(errorThemeUrlRoute())); ?>
<?php echo HTML::style(urlRoute(errorStyleUrlRoute())); ?>

<?php echo HTML::script(adminJsAssetURL("jquery/jquery-1.10.2.min.js")); ?>
<?php echo HTML::script(urlRoute(errorScriptUrlRoute())); ?>
</head>

<body>
    
    <div id="body-wrapper" class="error-body">
        <div id="page" class="container">
            <div class="page-wrapper">
                <div class="col-xs-12">
                    @yield('content')
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

</body>
</html>