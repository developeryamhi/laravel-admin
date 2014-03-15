<?php namespace Developeryamhi\AuthModule;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Developeryamhi\LaravelAdmin\Base\AdminController;

class ACLController extends AdminController {

    protected $moduleName = "auth-module";

    protected $controllerName = "acl";

    protected $folderName = "acl";

    protected $permissionRequired = "manage_acl";

    protected function init() {

        //  Set Layout
        $this->layoutName = adminLayout();
    }

    public function groups() {

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.groups"));

        setPageData("items", GroupItem::paginate(10));
        $this->_render("groups.index");
    }

    public function group_create() {

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.add_new_group"));

        return $this->_group_form();
    }

    public function group_edit($id) {
        if(!GroupItem::find($id))
            return Redirect::route("groups")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.group_not_found"));

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.edit_group"));

        return $this->_group_form(GroupItem::find($id));
    }

    private function _group_form($item = null) {

        //  Check New
        $newItem = ($item ? false : true);
        if($newItem) {
            $item = new GroupItem();
        }

        setPageData("item", $item);
        setPageData("newItem", $newItem);

        $this->_render('groups.form');
    }

    public function group_save($id = null) {

        //  Validate Requested Item
        if($id && !GroupItem::find($id))
            return Redirect::route("groups")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.group_not_found"));

        //  Success
        $success = true;

        //  Error Messages
        $error_messages = array();

        //  Get User
        $group = ($id ? GroupItem::find($id) : new GroupItem());
        
        //  Assign Values
        $group->group_name = Input::get("group_name");
        $group->group_description = Input::get("group_description");

        //  Check if Group Already Exists
        if($this->groupExists($id, $group->group_name)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.group_already_exists", array("group" => $group->group_name));
        }

        //  Check Validation was successful
        if($success) {

            //  Save the Group
            $group->save();

            //  Permissions
            $permissions = (array)Input::get("permissions");

            //  Validate Dashboard Permission
            $dash_perm = PermissionItem::forDashboard();

            //  Loop Existings
            foreach($group->permissionIds() as $e_perm_id) {

                //  Check Still Exists
                if($e_perm_id != $dash_perm->id && !in_array($e_perm_id, $permissions)) {

                    //  Remove Permission
                    $group->removePermissionById($e_perm_id);
                }
            }

            //  Add Permissions
            $group->addPermissionsById($permissions);

            //  Validate Dashboard Permission
            $dash_perm->getAdded($group);

            //  Redirect
            return Redirect::route("groups")
                    ->with(FLASH_MSG_SUCCESS, trans("auth-module::message.group_saved"));
        } else {

            //  Redirect with Error
            return Redirect::route(($id ? "edit_group" : "create_group"), ($id ? array("id" => $id) : null))
                    ->with(FLASH_MSG_ERROR, implode("<br/>", $error_messages));
        }
    }

    public function group_delete($id) {

        //  Validate Requested Item
        if(!$id || !GroupItem::notAdmin()->find($id))
            return Redirect::route("groups")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.group_not_found"));

        //  Get GroupItem
        $group = GroupItem::find($id);

        //  Drop Permissions
        $group->dropPermissions();

        // Delete
        $group->delete();

        return Redirect::route("groups")
                    ->with(FLASH_MSG_SUCCESS, trans($this->moduleName . "::message.group_deleted"));
    }

    public function permissions() {

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.permissions"));

        setPageData("items", PermissionItem::notDashboard()->paginate(10));
        $this->_render("permissions.index");
    }

    public function permission_create() {

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.add_new_permission"));

        return $this->_permission_form();
    }

    public function permission_edit($id) {
        if(!PermissionItem::notDashboard()->find($id))
            return Redirect::route("permissions")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.permission_not_found"));

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.edit_permission"));

        return $this->_permission_form(PermissionItem::find($id));
    }

    private function _permission_form($item = null) {

        //  Check New
        $newItem = ($item ? false : true);
        if($newItem) {
            $item = new PermissionItem();
        }

        setPageData("item", $item);
        setPageData("newItem", $newItem);

        $this->_render('permissions.form');
    }

    public function permission_save($id = null) {

        //  Validate Requested Item
        if($id && !PermissionItem::notDashboard()->find($id))
            return Redirect::route("permissions")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.permission_not_found"));

        //  Success
        $success = true;

        //  Error Messages
        $error_messages = array();

        //  Get User
        $permission = ($id ? PermissionItem::find($id) : new PermissionItem());
        
        //  Assign Values
        $permission->permission_key = Input::get("permission_key");
        $permission->permission_description = Input::get("permission_description");

        //  Check if Permission Already Exists
        if($this->permissionExists($id, $permission->permission_key)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.permission_already_exists", array("permission" => $permission->permission_key));
        }

        //  Check Validation was successful
        if($success) {

            //  Save the Permission
            $permission->save();

            //  Redirect
            return Redirect::route("permissions")
                    ->with(FLASH_MSG_SUCCESS, trans("auth-module::message.permission_saved"));
        } else {

            //  Redirect with Error
            return Redirect::route(($id ? "edit_permission" : "create_permission"), ($id ? array("id" => $id) : null))
                    ->with(FLASH_MSG_ERROR, implode("<br/>", $error_messages));
        }
    }

    public function permission_delete($id) {

        //  Validate Requested Item
        if(!$id || !PermissionItem::notDashboard()->find($id))
            return Redirect::route("permissions")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.permission_not_found"));

        //  Get PermissionItem
        $permission = PermissionItem::find($id);

        //  Get Dropped
        $permission->getDropped();

        // Delete
        $permission->delete();

        return Redirect::route("permissions")
                    ->with(FLASH_MSG_SUCCESS, trans($this->moduleName . "::message.permission_deleted"));
    }

    public function groupExists($id = null, $value = null) {
        $exists = false;
        $id || $id = Input::get("search_id");
        $value || $value = Input::get("search_value");
        $group = GroupItem::alreadyExistingItem("group_name", $value);
        if($group) {
            if(!$id || ($id && $id != $group->id)) {
                $exists = true;
            }
        }
        if (Request::wantsJson()) {
            return Response::json(array('valid' => !$exists));
        }
        return $exists;
    }

    public function permissionExists($id = null, $value = null) {
        $exists = false;
        $id || $id = Input::get("search_id");
        $value || $value = Input::get("search_value");
        $permission = PermissionItem::alreadyExistingItem("permission_key", $value);
        if($permission) {
            if(!$id || ($id && $id != $permission->id)) {
                $exists = true;
            }
        }
        if (Request::wantsJson()) {
            return Response::json(array('valid' => !$exists));
        }
        return $exists;
    }
}