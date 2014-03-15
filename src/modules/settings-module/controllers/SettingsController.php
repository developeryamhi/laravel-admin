<?php namespace Developeryamhi\SettingsModule;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Developeryamhi\LaravelAdmin\Base\AdminController;

class SettingsController extends AdminController {

    protected $moduleName = "settings-module";

    protected $controllerName = "settings";

    protected $permissionRequired = "manage_settings";

    protected function init() {

        //  Set Layout
        $this->layoutName = adminLayout();

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.settings"));
    }

    public function index() {
        setPageData('groups', SettingGroupItem::all());
        $this->_render();
    }

    public function save() {
        $parsedSettings = $this->parseSettings();
        SettingItem::saveSettings($parsedSettings);

        return Redirect::route("settings")
                ->with(FLASH_MSG_SUCCESS, trans("settings-module::message.settings_updated"));
    }

    private function parseSettings() {
        $settings = SettingItem::hasInterface()->get();
        $parsedSettings = array();

        foreach($settings as $setting) {
            if($setting->isFile() || $setting->isImage()) {
                if(Input::has("setting.$setting->setting_key")) {
                    $value = Input::get("setting.$setting->setting_key");
                    if(Input::hasFile($setting->setting_key)) {
                        $subFolder = ($setting->isImage() ? "images" : "files") . "/";
                        $newFilename = uniqid() . "_" . time() . "." . Input::file($setting->setting_key)->getClientOriginalExtension();

                        $file = Input::file($setting->setting_key);
                        $file->move(AssetsHelper::uploadPath($subFolder), $newFilename);
                        $value = $subFolder . $newFilename;
                    }
                    $parsedSettings[$setting->setting_key] = $value;
                }
            }
            else if($setting->isMultipleChoice()) {
                $parsedSettings[$setting->setting_key] = implode("|||", Input::get("setting.$setting->setting_key"));
            } else {
                if(Input::has("setting.$setting->setting_key"))
                    $parsedSettings[$setting->setting_key] = Input::get("setting.$setting->setting_key");
                else {
                    if(!$setting->isRequired())
                        $parsedSettings[$setting->setting_key] = Input::get("setting.$setting->setting_key");
                }
            }
        }

        return $parsedSettings;
    }
}
