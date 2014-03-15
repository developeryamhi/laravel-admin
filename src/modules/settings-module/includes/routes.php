<?php

// Add Routes
register_admin_route('settings', array('as' => 'settings', 'uses' => '\\Developeryamhi\\SettingsModule\\SettingsController@index'))->before('auth');
register_admin_route_post('save_settings', array('as' => 'save_settings', 'uses' => '\\Developeryamhi\\SettingsModule\\SettingsController@save'))->before('auth');