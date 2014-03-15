<!doctype html>
<html lang="<?php echo App::getLocale(); ?>">
<head>
<title><?php echo @$pageTitle; ?></title>
{{ printableMetas() }}
@yield('header_metas')

<link rel="shortcut icon" href="<?php echo URL::to('favicon.ico'); ?>">
{{ printableStyles(VIEW_LOCATION_HEADER) }}
{{ enqueue_detected_css() }}
@yield('header_styles')

<script>
BASE_ROOT = '<?php echo URL::route("home") . "/"; ?>';
ADMIN_ROOT = '<?php echo URL::route("dashboard") . "/"; ?>';
IN_MAINTAINANCE_MODE = <?php echo intval(App::isDownForMaintenance()); ?>;
</script>
{{ printableScripts(VIEW_LOCATION_HEADER) }}
@yield('header_scripts')
</head>

<body>
    
    @section('admin_header')
        @include('laravel-admin::includes.admin_header')
    @stop
    @yield('admin_header')

    <div id="body-wrapper">
        <div id="page" class="container">
            <div class="page-wrapper">
                @include('laravel-admin::includes.messages')
                {{ $content }}
            </div>
        </div>
    </div>

    @section('admin_footer')
        @include('laravel-admin::includes.admin_footer')
    @stop
    @yield('admin_footer')

    {{ printableStyles(VIEW_LOCATION_FOOTER) }}
    {{ printableScripts(VIEW_LOCATION_FOOTER) }}
    {{ enqueue_detected_js() }}
    @yield('footer_scripts')

</body>
</html>