<?php

//  Load Start
require_once __DIR__ . '/start.php';

//  Define Nav Parent
define("ADMIN_NAV_DASHBOARD", "dashboard");
define("ADMIN_NAV_ADMINISTRATION", "administration");
define("ADMIN_NAV_CONTENTS", "contents");
define("ADMIN_NAV_SYSTEM", "system");
define("ADMIN_NAV_SETTINGS", "settings");
define("ADMIN_NAV_FRONTEND", "front_end");

//  Define Routes
define("ROUTE_DASHBOARD", "dashboard");

//  Listen Admin Controller Created Event
Event::listen("admin.controller.created", function() {

    //  Create Admin Nav Resource
    generate_navigation(adminNavGroup());

    //  Reset Resources
    resetResources(true);

    //  Change Base Page Title
    setBasePageTitle(app("laravel-admin")->getConfig(Auth::check() ? "title" : "login_title"), true);

    //  Set Meta Data for Pages
    setRawMeta("charset", "UTF-8");
    setRawMeta("http-equiv", "X-UA-Compatible", array("content" => "IE=edge"));
    setMeta("viewport", "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0");

    //  Enqueue Styles for Pages
    enqueue_style('jquery-ui', false, adminCssAssetURL('jquery/jquery-ui-1.10.3.min.css'));
    enqueue_style('bootstrap', false, adminCssAssetURL('bootstrap/bootstrap.min.css'));
    enqueue_style('bootstrap-wysihtml5', false, adminCssAssetURL('bootstrap-wysihtml5/bootstrap-wysihtml5.css'));
    enqueue_style('fancybox2', false, adminCssAssetURL('fancybox2/jquery.fancybox.css'));
    enqueue_style('font-awesome', false, adminCssAssetURL('font-awesome/font-awesome.min.css'));
    enqueue_style('bootstrap-date', false, adminCssAssetURL('bootstrap-editable/date/datepicker.css'));
    enqueue_style('bootstrap-datetime', false, adminCssAssetURL('bootstrap-editable/datetime/datetimepicker.css'));
    enqueue_style('select2', false, adminCssAssetURL('bootstrap-editable/select2/select2.css'));
    enqueue_style('bootstrap-editable', false, adminCssAssetURL('bootstrap-editable/bootstrap-editable.css'));
    enqueue_style('bootstrap-select2', false, adminCssAssetURL('bootstrap-editable/select2/select2-bootstrap.css'));
    enqueue_style('bootstrap-editable-address', false, adminCssAssetURL('bootstrap-editable/inputs-ext/address/address.css'));
    enqueue_style('bootstrap-editable-typeaheadjs', false, adminCssAssetURL('bootstrap-editable/inputs-ext/typeaheadjs/typeahead-bootstrap.css'));
    enqueue_style('bootstrap-tabdrop', false, adminCssAssetURL('bootstrap-tabdrop/bootstrap-tabdrop.css'));
    enqueue_style('bootstrap-select', false, adminCssAssetURL('bootstrap-select/bootstrap-select.min.css'));

    //  Enqueue Scripts for Pages
    enqueue_script('jquery', false, adminJsAssetURL('jquery/jquery-1.10.2.min.js'), VIEW_LOCATION_HEADER);
    enqueue_script('jquery-migrate', false, adminJsAssetURL('jquery/jquery-migrate.js'), VIEW_LOCATION_HEADER);
    enqueue_script('jquery-ui', false, adminJsAssetURL('jquery/jquery-ui-1.10.3.min.js'), VIEW_LOCATION_HEADER);
    enqueue_script('hashchange', false, adminJsAssetURL('hashchange/jquery.ba-hashchange.min.js'));
    enqueue_script('bootstrap', false, adminJsAssetURL('bootstrap/bootstrap.min.js'));
    enqueue_script('handlebars', false, adminJsAssetURL('bootstrap-typeahead/handlebars.js'));
    enqueue_script('bootstrap-typeahead', false, adminJsAssetURL('bootstrap-typeahead/bootstrap.typeahead.js'));
    enqueue_script('wysihtml5', false, adminJsAssetURL('bootstrap-wysihtml5/wysihtml5-0.3.0.js'));
    enqueue_script('bootstrap-wysihtml5', false, adminJsAssetURL('bootstrap-wysihtml5/bootstrap-wysihtml5.js'));
    enqueue_script('jquery-validate', false, adminJsAssetURL('jquery-validate/jquery.validate.min.js'));
    enqueue_script('jquery-validate-extended', false, adminJsAssetURL('others/jquery.validate.extended.js'));
    enqueue_script('fancybox2', false, adminJsAssetURL('fancybox2/jquery.fancybox.pack.js'));
    enqueue_script('custom_autocomplete', false, adminJsAssetURL('others/jquery.e_autocomplete.js'));
    enqueue_script('tmpl', false, adminJsAssetURL('others/tmpl.min.js'));
    enqueue_script('momentjs', false, adminJsAssetURL('momentjs/moment.js'));
    enqueue_script('bootstrap-datepicker', false, adminJsAssetURL('bootstrap-editable/date/bootstrap-datepicker.js'));
    enqueue_script('bootstrap-datetimepicker', false, adminJsAssetURL('bootstrap-editable/datetime/bootstrap-datetimepicker.js'));
    enqueue_script('bootstrap-select2', false, adminJsAssetURL('bootstrap-editable/select2/select2.min.js'));
    enqueue_script('bootstrap-editable', false, adminJsAssetURL('bootstrap-editable/bootstrap-editable.js'));
    enqueue_script('bootstrap-editable-address', false, adminJsAssetURL('bootstrap-editable/inputs-ext/address/address.js'));
    enqueue_script('bootstrap-editable-typeaheadjs', false, adminJsAssetURL('bootstrap-editable/inputs-ext/typeaheadjs/typeaheadjs.js'));
    enqueue_script('bootstrap-editable-wysihtml5', false, adminJsAssetURL('bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js'));
    enqueue_script('jquery-noty', false, adminJsAssetURL('noty/jquery.noty.js'));
    enqueue_script('bootstrap-tabdrop', false, adminJsAssetURL('bootstrap-tabdrop/bootstrap-tabdrop.js'));
    enqueue_script('bootstrap-select', false, adminJsAssetURL('bootstrap-select/bootstrap-select.min.js'));

    //  Enqueue Admin Custom Styles, Scripts
    enqueue_style('admin-theme', false, urlRoute(adminThemeUrlRoute()));
    enqueue_style('admin-style', false, urlRoute(adminStyleUrlRoute()));
    enqueue_script('admin-script', false, urlRoute(adminScriptUrlRoute()));

    //  Check Nav
    if($nav = nav(adminNavGroup())) {

        //  Add Dashboard Navigation Item
        $nav->addMenuItem(ADMIN_NAV_DASHBOARD, trans("laravel-admin::menu_item.dashboard"), urlRoute(ROUTE_DASHBOARD), null, null, 10);

        //  Add Content Navigation Item
        $nav->addMenuItem(ADMIN_NAV_ADMINISTRATION, trans("laravel-admin::menu_item.administration"), "#", null, null, 20);

        //  Add Content Navigation Item
        $nav->addMenuItem(ADMIN_NAV_CONTENTS, trans("laravel-admin::menu_item.contents"), "#", null, null, 30);

        //  Add System Navigation Item
        $nav->addMenuItem(ADMIN_NAV_SYSTEM, trans("laravel-admin::menu_item.system"), "#", null, null, 40);

        //  Add Settings Navigation Item
        $nav->addMenuItem(ADMIN_NAV_SETTINGS, trans("laravel-admin::menu_item.settings"), "#", null, null, 50);

        //  Check Permissions
        if(isAdmin() || userHasPermission("manage_modules")) {

            //  Add Modules Manager Navigation
            $nav->addSubMenuItem(ADMIN_NAV_SETTINGS, "modules", trans("laravel-admin::menu_item.modules"), urlRoute("modules"));
        }

        //  Check if Site Has Frontend
        if(urlRoute(ROUTE_DASHBOARD) != urlRoute("home")) {

            //  Add Navigate to Frontend Navigation Item
            $nav->addRightMenuItem(ADMIN_NAV_FRONTEND, array("icon" => "glyphicon-log-out", "title" => trans("laravel-admin::menu_item.front_end"), "append" => ' data-placement="bottom" target="_blank"'), urlRoute("home"), null, null, 5000);
        }
    }
});