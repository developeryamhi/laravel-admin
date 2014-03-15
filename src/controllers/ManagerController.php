<?php namespace Developeryamhi\LaravelAdmin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class ModulesController extends Base\AdminController {

    protected $namespaceName = "laravel-admin";

    protected $controllerName = "modules";

    protected $folderName = "modules";

    protected function init() {
        $this->layoutName = adminLayout();
    }

    public function permissionRequired() {
        return (isAdmin() ? null : "manage_modules");
    }

    public function index() {
        setPageData("items", ModuleItem::orderBy("order_index", "ASC")->paginate(10));
        return $this->_render();
    }

    public function add_module() {
        $this->_render("form");
    }

    public function add_module_process() {

        //  Module File
        $moduleFile = Input::file("module_file");

        if(!$moduleFile || $moduleFile->getClientOriginalExtension() != "zip")
            return Redirect::route("add_module")
                    ->with(FLASH_MSG_ERROR, Lang::get("laravel-admin::message.module_import_invalid_file"));

        //  File Name
        $zipName = $moduleFile->getClientOriginalName();

        //  Module Name
        $moduleName = strtolower(substr($zipName, 0, -(strlen($moduleFile->getClientOriginalExtension()) + 1)));

        //  Check Module Already Exists
        if(hasTheModule($moduleName))
            return Redirect::route("add_module")
                    ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_already_exists", array("module" => $moduleName)));

        //  Try Adding the Module
        if(!addNewModule($moduleName, $moduleFile))
            return Redirect::route("add_module")
                    ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_import_failed"));
        else
            scanTheModules(true, array($moduleName));

        //  Redirect
        return Redirect::route("modules")
                    ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.module_added"));
    }

    public function scan_modules() {

        //  Scan Modules
        scanTheModules(true);

        //  Redirect
        return Redirect::route("modules")
                ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.modules_scanned"));
    }

    public function sync_modules() {

        //  Loop Each
        foreach(getTheModules() as $module) {

            //  Run the Sync
            $module->syncFromMeta();
        }

        //  Redirect
        return Redirect::route("modules")
                ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.modules_synced"));
    }

    public function activate_module($id, $forced = false) {

        //  Get Module Item
        $moduleItem = ModuleItem::find($id);

        //  Check Module Exists
        if(!$moduleItem)
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_not_found"));

        //  Get Module
        $module = getTheModule($moduleItem->module);

        //  Check Module Exists
        if(!$module)
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_not_found2", array("module" => $moduleItem->module)));

        //  Run Activation
        $result = $module->activate($forced);

        //  Check Result
        if($result !== true) {

            //  Get Formatted Messages
            $messages = ModuleItem::activationResponseFormatted($result);

            //  Redirect
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_activate_failed", array(
                    "module" => $module->moduleName(),
                    "messages" => implode("<br/>", $messages)
                )));
        }

        //  Redirect
        return Redirect::route("modules")
                ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.module_activated", array("module" => $module->moduleName())));
    }

    public function force_activate_module($id) {
        return $this->activate_module($id, true);
    }

    public function deactivate_module($id) {

        //  Get Module Item
        $moduleItem = ModuleItem::find($id);

        //  Check Module Exists
        if(!$moduleItem)
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_not_found"));

        //  Get Module
        $module = getTheModule($moduleItem->module);

        //  Check Module Exists
        if(!$module)
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_not_found2", array("module" => $moduleItem->module)));

        //  Run Deactivation
        $result = $module->deactivate();

        //  Check Result
        if(!$result) {

            //  Check Locked
            if($module->isLocked()) {

                //  Redirect
                return Redirect::route("modules")
                    ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_deactivate_locked_failed", array("module" => $module->moduleName())));
            } else {

                //  Redirect
                return Redirect::route("modules")
                    ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_deactivate_failed", array("module" => $module->moduleName())));
            }
        }

        //  Redirect
        return Redirect::route("modules")
                ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.module_deactivated", array("module" => $module->moduleName())));
    }

    public function delete_module($id) {

        //  Get Module Item
        $moduleItem = ModuleItem::find($id);

        //  Check Module Exists
        if(!$moduleItem)
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_not_found"));

        //  Get Module
        $module = getTheModule($moduleItem->module);

        //  Check Module Exists
        if(!$module)
            return Redirect::route("modules")
                ->with(FLASH_MSG_ERROR, trans("laravel-admin::message.module_not_found2", array("module" => $moduleItem->module)));

        //  Run Module Delete
        if($module->delete()) {

            //  Redirect
            return Redirect::route("modules")
                    ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.module_deleted", array("module" => $module->moduleName())));
        } else {

            //  Redirect
            return Redirect::route("modules")
                    ->with(FLASH_MSG_SUCCESS, trans("laravel-admin::message.module_delete_failed", array("module" => $module->moduleName())));
        }
    }
}
