<!doctype html>
<html lang="<?php echo App::getLocale(); ?>">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, user-scalable=no" />
<title><?php echo $pageTitle; ?></title>
<link rel="shortcut icon" href="<?php echo URL::to('favicon.ico'); ?>">

<?php echo HTML::style(adminCssAssetURL("bootstrap/bootstrap.min.css")); ?>
<?php echo HTML::style(adminCssAssetURL("jquery/jquery-ui-1.10.3.min.css")); ?>
<?php echo HTML::style(urlRoute(guestThemeUrlRoute())); ?>
<?php echo HTML::style(urlRoute(guestStyleUrlRoute())); ?>

<?php echo HTML::script(adminJsAssetURL("jquery/jquery-1.10.2.min.js")); ?>
<?php echo HTML::script(adminJsAssetURL("jquery/jquery-ui-1.10.3.min.js")); ?>
<?php echo HTML::script(urlRoute(guestScriptUrlRoute())); ?>
</head>

<body>
    
    <div id="body-wrapper" class="guest-body">
        <div id="page" class="container">
            <div class="page-wrapper">
                <div class="col-xs-12">
                    @include('laravel-admin::includes.messages')
                    {{ $content }}
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <?php echo HTML::script(adminJsAssetUrl("bootstrap/bootstrap.min.js")); ?>

</body>
</html>