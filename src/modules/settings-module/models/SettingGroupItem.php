<?php namespace Developeryamhi\SettingsModule;

use Developeryamhi\LaravelAdmin\Base\Eloquent;

class SettingGroupItem extends Eloquent {

    protected $softDelete = true;

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("settings-module::table_setting_groups");
        parent::__construct($attributes);
    }

    public static function findSettingGroup($name) {
        return self::where('setting_group_key', '=', $name)->first();
    }

    public function hasSettings() {
        return ($this->settings()->count() > 0);
    }

    public function hasSettingsWithInterface() {
        return ($this->settingsWithInterface()->count() > 0);
    }

    public function settings() {
        return $this->hasMany('\\Developeryamhi\\SettingsModule\\SettingItem', 'setting_group_id');
    }

    public function settingsWithInterface() {
        return $this->hasMany('\\Developeryamhi\\SettingsModule\\SettingItem', 'setting_group_id')->hasInterface();
    }

    public function isFullRowSetting() {
        return (bool)($this->setting_full_row == 1);
    }

    public static function setSettingGroup($key, $name_lang, $desc_lang = '') {
        if(!self::findSettingGroup($key))
            return self::addSettingGroup($key, $name_lang, $desc_lang);
    }

    public static function addSettingGroup($key, $name_lang, $desc_lang = '') {
        $group = self::findSettingGroup($key);
        if(!$group)
            $group = new SettingGroupItem();
        $group->setting_group_key = $key;
        $group->setting_group_name_lang = $name_lang;
        $group->setting_group_desc_lang = $desc_lang;
        $group->save();
        return $group;
    }
}