<?php

//  Apply Filters to Admin Path
//Route::when(adminAlias(), 'admin');
//Route::when(adminAlias() . '/*', 'admin');

//  Add Dashboard Route
register_admin_route('', array('as' => "dashboard", 'uses' => '\\Developeryamhi\\LaravelAdmin\\DashboardController@index'))->before('auth');

//  Add Modules Manager Routes
register_admin_route("modules", array('as' => "modules", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@index'))->before('auth');
register_admin_route("add_module", array('as' => "add_module", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@add_module'))->before('auth');
register_admin_route_post("add_module_process", array('as' => "add_module_process", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@add_module_process'))->before('auth');
register_admin_route("scan_modules", array('as' => "scan_modules", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@scan_modules'))->before('auth');
register_admin_route("sync_modules", array('as' => "sync_modules", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@sync_modules'))->before('auth');
register_admin_route("activate_module/{id}", array('as' => "activate_module", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@activate_module'))->before('auth');
register_admin_route("force_activate_module/{id}", array('as' => "force_activate_module", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@force_activate_module'))->before('auth');
register_admin_route("deactivate_module/{id}", array('as' => "deactivate_module", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@deactivate_module'))->before('auth');
register_admin_route("delete_module/{id}", array('as' => "delete_module", 'uses' => '\\Developeryamhi\\LaravelAdmin\\ModulesController@delete_module'))->before('auth');