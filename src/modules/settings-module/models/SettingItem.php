<?php namespace Developeryamhi\SettingsModule;

use Illuminate\Support\Facades\Event;
use Developeryamhi\LaravelAdmin\Base\Eloquent;

class SettingItem extends Eloquent {

    protected $softDelete = true;

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("settings-module::table_settings");
        parent::__construct($attributes);
    }

    public static function findSetting($name, $group_id = null) {
        $q = self::where('setting_key', '=', $name);
        if($group_id)   $q->where("setting_group_id", $group_id);
        return $q->first();
    }

    public function scopeHasInterface($query) {
        return $query->where("has_interface", "=", "1");
    }

    public function scopeAutoload($query) {
        return $query->where("autoload", "=", "1");
    }

    public function isMultipleChoice() {
        return ($this->setting_mchoice == 1);
    }

    public function getValues() {
        return (explode("|||", $this->setting_value));
    }

    public function isFile() {
        return ($this->setting_type == "file");
    }

    public function isImage() {
        return ($this->setting_type == "image");
    }

    public function isRequired() {
        return ($this->setting_required == "1");
    }

    public function getChoices($settingOptions = null) {

        //  Check Options
        $settingOptions || $settingOptions = array();

        //  Check for Callback
        if(preg_match('/^callback\:\:(.*)/i', $this->setting_options, $cMatch)) {

            //  Get The Callback
            $callback = $cMatch[1];

            //  Check Function Exists
            if(function_exists($callback)) {

                //  Get Choices
                $settingOptions = $callback();
            }
        } else {

            //  Check Empty
            if(!empty($this->setting_options)) {

                //  Truncate
                $settingOptions = array();

                //  Explode
                $explodes = explode("|||", $this->setting_options);

                //  Loop Each
                foreach($explodes as $explode) {

                    //  Store Datasets
                    $key = $val = $explode;
                    $match = null;
                    preg_match('/^(.*)\{(.*)\}/i', $explode, $match);
                    if($match) {
                        $key = $match[2];
                        $val = $match[1];
                    }
                    $settingOptions[$key] = $val;
                }
            }
        }
        return $settingOptions;
    }

    public static function getSettingsConfig() {
        $settingsConfig = array();
        $settings = self::autoload()->get();
        foreach($settings as $setting) {
            $settingsConfig[$setting->setting_key] = $setting->getValue();
        }
        return mergeEventFireResponseArray($settingsConfig, Event::fire('setting.config', array($settingsConfig)));
    }

    public function setting_group() {
        return $this->belongsTo('\\Developeryamhi\\SettingsModule\\SettingGroupItem');
    }

    public static function setSettingItem($group, $key, $value, $lang_key, $autoload = 1, $required = 1, $desc_key = '', $type = 'text', $options = '', $mchoice = 0, $has_interface = true) {
        if(!self::findSetting($key))
            self::addSetting($group, $key, $value, $lang_key, $autoload, $required, $desc_key, $type, $options, $mchoice, $has_interface);
    }

    public static function addSetting($group, $key, $value, $lang_key, $autoload = 1, $required = 1, $desc_key = '', $type = 'text', $options = '', $mchoice = 0, $has_interface = true) {
        $settingGroup = SettingGroupItem::findSettingGroup($group);
        if($settingGroup) {
            if($type == "choices")
                $mchoice = 1;
            $new = false;
            $setting = self::findSetting($key);
            if(!$setting) {
                $new = true;
                $setting = new SettingItem();
                $setting->setting_group_id = $settingGroup->id;
            }
            if($setting->setting_group_id == $settingGroup->id) {
                $setting->setting_name_lang = $lang_key;
                $setting->setting_desc_lang = $desc_key;
                $setting->setting_key = $key;
                if($new)
                    $setting->setting_value = $value;
                $setting->setting_required = $required;
                $setting->setting_type = $type;
                $setting->setting_options = $options;
                $setting->setting_mchoice = (int)$mchoice;
                $setting->has_interface = (int)$has_interface;
                $setting->autoload = (int)$autoload;
                $setting->save();
            }
        }
    }

    public function getValue() {
        if($this->isMultipleChoice())
            return $this->getValues();
        return $this->setting_value;
    }

    public static function saveSettings($settings) {
        foreach($settings as $setting_key => $setting_val) {
            $setting = self::where("setting_key", '=', $setting_key)->first();
            $setting->setting_value = $setting_val;
            $setting->save();
        }
    }

    public static function getSetting($key, $def = null) {
        $app = app();
        if(isset($app["settings_data"])) {
            $settings = $app["settings_data"];
            if(isset($settings[$key]))
                return $settings[$key];
        }
        return $def;
    }
}