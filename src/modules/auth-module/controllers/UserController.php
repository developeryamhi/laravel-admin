<?php namespace Developeryamhi\AuthModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Developeryamhi\LaravelAdmin\Base\AdminController;

class UserController extends AdminController {

    protected $moduleName = "auth-module";

    protected $controllerName = "user";

    protected $folderName = "user";

    protected $permissionRequired = "manage_users";

    protected $customGroup = null;

    protected $failedResponse = null;

    protected function init() {

        //  Set Layout
        $this->layoutName = adminLayout();

        //  Validate Custom
        $this->failedResponse = $this->_validateCustom();

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.users"));
    }

    public function excludeFromPermissions($method, $permissions) {
        return ($method == "my_account" || $method == "update_my_account" || $method == "userExists");
    }

    private function _validateCustom(){

        //  Get Route Info
        $routeInfo = routeInfo();

        //  Get Action
        $routeName = $routeInfo["action"]["as"];

        //  Detect Custom
        if(substr($routeInfo["method"], 0, 7) != "custom_") return null;

        //  Get For
        $for = null;

        //  Switch
        switch($routeInfo["method"]) {
            case "custom_index":
                $for = "list";
                break;
            case "custom_create":
                $for = "create";
                break;
            case "custom_edit":
                $for = "edit";
                break;
            case "custom_save":
                $for = "save";
                break;
            case "custom_update":
                $for = "update";
                break;
            case "custom_delete":
                $for = "delete";
                break;
        }

        //  Detect For
        if($for) {

            //  Loop Each
            foreach(GroupItem::hasInterface()->get() as $group) {

                //  Check Valid
                if($group->hasValidInterface()) {

                    //  Routes
                    $routes = unserialize($group->group_routes);

                    //  Get the Value
                    $val = $routes[$for];

                    //  Match
                    if($val == $routeName) {

                        //  Store Custom Group
                        $this->customGroup = $group;

                        //  Set Page Data
                        setPageData("group", $this->customGroup);

                        return null;
                    }
                }
            }
        }

        return Redirect::route("users");
    }

    public function permissionRequired() {
        if($this->customGroup)
            return $this->customGroup->group_permission;
        return $this->permissionRequired;
    }

    public function index() {
        setPageData("items", UserItem::notMe()->groupWithoutInterface()->paginate(10));
        $this->_render();
    }

    public function custom_index() {
        if($this->failedResponse)   return $this->failedResponse;

        //  Set Page Title
        setPageTitle($this->customGroup->getLang("list_page_title"));

        setPageData("items", UserItem::notMe()->filterGroup($this->customGroup->group_name)->paginate(10));
        $this->_render("custom_index");
    }

    public function create() {

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.add_new_user"));

        return $this->_form();
    }

    public function custom_create() {
        if($this->failedResponse)   return $this->failedResponse;

        //  Set Page Title
        setPageTitle($this->customGroup->getLang("create_page_title"));

        return $this->_form_custom();
    }

    public function edit($id) {
        if(!UserItem::notMe()->find($id))
            return Redirect::route("users")
                    ->with(FLASH_MSG_ERROR, trans("auth-module::message.user_not_found"));

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.edit_user"));

        return $this->_form(UserItem::find($id));
    }

    public function custom_edit($id) {
        if($this->failedResponse)   return $this->failedResponse;

        if(!UserItem::filterGroup($this->customGroup->group_name)->notMe()->find($id))
            return Redirect::route($this->customGroup->getRoute("list"))
                    ->with(FLASH_MSG_ERROR, $this->customGroup->getLang("message_not_found"));

        //  Set Page Title
        setPageTitle($this->customGroup->getLang("edit_page_title"));

        return $this->_form_custom(UserItem::find($id));
    }

    private function _form($item = null) {

        //  Check New
        $newItem = ($item ? false : true);
        if($newItem) {
            $item = new UserItem();
            $item->enabled = 1;
        }

        //  Populate User Metas
        $item->populate_metas();

        //  Check New
        if($newItem) {

            //  Fire New Item Event
            mergeEventFireResponse($item, app("events")->fire('module.auth.item.new', array(&$item)));
        } else {

            //  Fire Existing Item Event
            mergeEventFireResponse($item, app("events")->fire('module.auth.item.existing', array(&$item)));
        }

        setPageData("item", $item);
        setPageData("newItem", $newItem);

        $this->_render('form');
    }

    private function _form_custom($item = null) {

        //  Check New
        $newItem = ($item ? false : true);
        if($newItem) {
            $item = $this->customGroup->createUser();
            $item->enabled = 1;
        }

        //  Populate User Metas
        $item->populate_metas();

        //  Check New
        if($newItem) {

            //  Fire New Item Event
            mergeEventFireResponse($item, app("events")->fire('module.auth.item.new', array(&$item)));
            mergeEventFireResponse($item, app("events")->fire('module.auth.' . $this->customGroup->group_name . '.item.new', array(&$item)));
        } else {

            //  Fire Existing Item Event
            mergeEventFireResponse($item, app("events")->fire('module.auth.item.existing', array(&$item)));
            mergeEventFireResponse($item, app("events")->fire('module.auth.' . $this->customGroup->group_name . '.item.existing', array(&$item)));
        }

        setPageData("item", $item);
        setPageData("newItem", $newItem);

        $this->_render('custom_form');
    }

    public function save($id = null) {

        //  Validate Requested Item
        if($id && !UserItem::notMe()->find($id))
            return Redirect::route("users")
                    ->with(FLASH_MSG_ERROR, trans($this->moduleName . "::message.user_not_found"));

        //  Success
        $success = true;

        //  Error Messages
        $error_messages = array();

        //  Get User
        $user = ($id ? UserItem::find($id) : new UserItem());
        
        //  Assign Values
        $user->group_id = ucfirst(Input::get("group_id"));
        $user->full_name = ucfirst(Input::get("full_name"));
        $user->nicename = Input::get("nicename");
        $user->email = Input::get("email");
        $user->username = Input::get("username");
        $user->enabled = Input::get("enabled");

        //  Check for Password
        if(!$id || ($id && Input::get("password") && Input::get("password") == Input::get("password2")))
            $user->password = Hash::make(Input::get("password"));
            

        //  Check if Email Address Already Exists
        if($this->userExists($id, "email", $user->email)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.email_already_exists", array("email" => $user->email));
        }

        //  Check if Username Already Exists
        if($this->userExists($id, "username", $user->username)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.username_already_exists", array("username" => $user->username));
        }

        //  Check Validation was successful
        if($success) {

            //  Save the User
            $user->save();

            //  Metas
            $metas = array();

            //  Loop Each Metas
            foreach(Input::all() as $key => $value) {

                //  Check if Meta Data
                if(substr($key, 0, 10) == "user_meta_") {

                    //  Store Meta
                    $metas[substr($key, 10)] = $value;
                }
            }

            //  Save Metas For User
            if($metas)  $user->saveMetas($metas);

            //  Fire Save Event
            app("events")->fire("module.auth.item.saved", array($user));

            //  Check for New
            if($id) {

                //  Fire Created Event
                app("events")->fire("module.auth.item.updated", array($user));
            } else {

                //  Fire Created Event
                app("events")->fire("module.auth.item.created", array($user));
            }
        } else {

            //  Check Success
            $error_messages = array_merge(array("<strong>" . trans("auth-module::message.fix_following_errors") . "</strong>"), $error_messages);
        }

        //  Check for Error
        if(!$success) {

            //  Redirect with Error
            return Redirect::route(($id ? "edit_user" : "create_user"), ($id ? array("id" => $id) : null))
                    ->with(FLASH_MSG_ERROR, implode("<br/>", $error_messages));
        } else {

            //  Redirect
            return Redirect::route("users")
                    ->with(FLASH_MSG_SUCCESS, trans("auth-module::message.user_saved"));
        }
    }

    public function custom_update($id) {
        return $this->custom_save($id);
    }

    public function custom_save($id = null) {
        if($this->failedResponse)   return $this->failedResponse;

        //  Validate Requested Item
        if($id && !UserItem::filterGroup($this->customGroup->group_name)->notMe()->find($id))
            return Redirect::route($this->customGroup->getRoute("list"))
                    ->with(FLASH_MSG_ERROR, $this->customGroup->getLang("message_not_found"));

        //  Success
        $success = true;

        //  Error Messages
        $error_messages = array();

        //  Get User
        $user = ($id ? UserItem::find($id) : $this->customGroup->createUser());
        
        //  Assign Values
        $user->full_name = ucfirst(Input::get("full_name"));
        $user->nicename = Input::get("nicename");
        $user->email = Input::get("email");
        $user->username = Input::get("username");
        $user->enabled = Input::get("enabled");

        //  Check for Password
        if(!$id || ($id && Input::get("password") && Input::get("password") == Input::get("password2")))
            $user->password = Hash::make(Input::get("password"));
            

        //  Check if Email Address Already Exists
        if($this->userExists($id, "email", $user->email)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.email_already_exists", array("email" => $user->email));
        }

        //  Check if Username Already Exists
        if($this->userExists($id, "username", $user->username)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.username_already_exists", array("username" => $user->username));
        }

        //  Check Validation was successful
        if($success) {

            //  Save the User
            $user->save();

            //  Metas
            $metas = array();

            //  Loop Each Metas
            foreach(Input::all() as $key => $value) {

                //  Check if Meta Data
                if(substr($key, 0, 10) == "user_meta_") {

                    //  Store Meta
                    $metas[substr($key, 10)] = $value;
                }
            }

            //  Save Metas For User
            if($metas)  $user->saveMetas($metas);

            //  Fire Save Event
            app("events")->fire("module.auth.item.saved", array($user));
            app("events")->fire("module.auth.{$this->customGroup->group_name}.item.saved", array($user));

            //  Check for New
            if($id) {

                //  Fire Created Event
                app("events")->fire("module.auth.item.updated", array($user));
                app("events")->fire("module.auth.{$this->customGroup->group_name}.item.updated", array($user));
            } else {

                //  Fire Created Event
                app("events")->fire("module.auth.item.created", array($user));
                app("events")->fire("module.auth.{$this->customGroup->group_name}.item.created", array($user));
            }
        } else {

            //  Check Success
            $error_messages = array_merge(array("<strong>" . trans("auth-module::message.fix_following_errors") . "</strong>"), $error_messages);
        }

        //  Check for Error
        if(!$success) {

            //  Redirect with Error
            return Redirect::route(($id ? $this->customGroup->getRoute("edit") : $this->customGroup->getRoute("create")), ($id ? array("id" => $id) : null))
                    ->with(FLASH_MSG_ERROR, implode("<br/>", $error_messages));
        } else {

            //  Redirect
            return Redirect::route($this->customGroup->getRoute("list"))
                    ->with(FLASH_MSG_SUCCESS, $this->customGroup->getLang("message_saved"));
        }
    }

    public function my_account() {

        //  Set Page Title
        setPageTitle(trans($this->moduleName . "::menu_item.my_account"));

        //  Get User
        $item = Auth::user();

        //  Populate User Metas
        $item->populate_metas();

        //  Fire Account Item Event
        mergeEventFireResponse($item, app("events")->fire('module.auth.account', array(&$item)));

        setPageData("item", $item);

        $this->_render('my_account');
    }

    public function update_my_account() {

        //  Success
        $success = true;

        //  Error Messages
        $error_messages = array();

        //  Get User
        $user = Auth::user();
        
        //  Assign Values
        $user->full_name = ucfirst(Input::get("full_name"));
        $user->nicename = Input::get("nicename");
        $user->email = Input::get("email");
        $user->username = Input::get("username");

        //  Check for Password
        if(Input::get("password") && Input::get("password") == Input::get("password2"))
            $user->password = Hash::make(Input::get("password"));
            

        //  Check if Email Address Already Exists
        if($this->userExists($user->id, "email", $user->email)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.email_already_exists", array("email" => $user->email));
        }

        //  Check if Username Already Exists
        if($this->userExists($user->id, "username", $user->username)) {

            //  Set Invalid
            $success = false;

            //  Add Error Message
            $error_messages[] = (sizeof($error_messages) + 1) . ". " . trans("auth-module::message.username_already_exists", array("username" => $user->username));
        }

        //  Check Validation was successful
        if($success) {

            //  Save the User
            $user->save();

            //  Metas
            $metas = array();

            //  Loop Each Metas
            foreach(Input::all() as $key => $value) {

                //  Check if Meta Data
                if(substr($key, 0, 10) == "user_meta_") {

                    //  Check For Location Data
                    if(substr($key, 0, 19) != "user_meta_location_")
                        $metas[substr($key, 10)] = $value;
                }
            }

            //  Save Metas For User
            if($metas)  $user->saveMetas($metas);

            //  Fire Created Event
            app("events")->fire("module.auth.account.updated", array($user));
        } else {

            //  Check Success
            $error_messages = array_merge(array("<strong>" . trans("auth-module::message.fix_following_errors") . "</strong>"), $error_messages);
        }

        //  Check for Error
        if(!$success) {

            //  Redirect with Error
            return Redirect::route("my_account")
                    ->with(FLASH_MSG_ERROR, implode("<br/>", $error_messages));
        } else {

            //  Redirect
            return Redirect::route("my_account")
                    ->with(FLASH_MSG_SUCCESS, trans("auth-module::message.account_saved"));
        }
    }

    public function delete($id) {

        //  Validate Requested Item
        if(!$id || !UserItem::notMe()->find($id))
            return Redirect::route("users")
                    ->with(FLASH_MSG_ERROR, trans($this->moduleName . "::message.user_not_found"));

        //  Get UserItem
        $user = UserItem::find($id);

        //  Fire Deleted Event
        app("events")->fire("module.auth.item.deleted", array($user));

        // Delete
        $user->delete();

        return Redirect::route("users")
                    ->with(FLASH_MSG_SUCCESS, trans($this->moduleName . "::message.user_deleted"));
    }

    public function custom_delete($id) {

        //  Validate Requested Item
        if(!$id || !UserItem::filterGroup($this->customGroup->group_name)->notMe()->find($id))
            return Redirect::route($this->customGroup->getRoute("list"))
                    ->with(FLASH_MSG_ERROR, $this->customGroup->getLang("message_not_found"));

        //  Get UserItem
        $user = UserItem::find($id);

        //  Fire Deleted Event
        app("events")->fire("module.auth.item.deleted", array($user));
        app("events")->fire("module.auth.{$this->customGroup->group_name}.item.deleted", array($user));

        // Delete
        $user->delete();

        return Redirect::route($this->customGroup->getRoute("list"))
                    ->with(FLASH_MSG_SUCCESS, $this->customGroup->getLang("message_deleted"));
    }

    public function userExists($id = null, $key = null, $value = null) {
        $exists = false;
        $id || $id = Input::get("search_id");
        $key || $key = Input::get("search_key");
        $value || $value = Input::get("search_value");
        $user = UserItem::alreadyExistingItem($key, $value);
        if($user) {
            if(!$id || ($id && $id != $user->id)) {
                $exists = true;
            }
        }
        if (Request::wantsJson()) {
            return Response::json(array('valid' => !$exists));
        }
        return $exists;
    }

    public function userLists() {
        $items = array();
        $query = UserItem::select(array("id", "group_id", "full_name", "username"));//->filterNotGroup(adminGroup());
        if(Input::has("user_type") && Input::get("user_type") != "*")
            $query->filterGroup(Input::get("user_type"));
        if(Input::has("_term")) {
            $term = Input::get("_term");
            $query->whereRaw(DB::raw("(full_name like '%{$term}%' OR username like '%{$term}%' OR full_name like '{$term}%' OR username like '{$term}%')"));
        }
        foreach($query->get() as $user) {
            $items[] = array(
                "id" => $user->id,
                "group" => $user->group()->group_description,
                "full_name" => $user->full_name,
                "username" => $user->username
            );
        }
        return Response::json($items);
    }
}