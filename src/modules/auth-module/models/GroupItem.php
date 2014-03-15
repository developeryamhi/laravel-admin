<?php namespace Developeryamhi\AuthModule;

use Developeryamhi\LaravelAdmin\Base\Eloquent;

class GroupItem extends Eloquent {

    protected $fillable = array("group_name");

    protected $group_routes_arr = array();
    protected $group_langs_arr = array();

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("auth-module::table_groups");
        parent::__construct($attributes);
    }

    public static function findGroup($name) {
        return self::where('group_name', '=', $name)->first();
    }

    public static function findGroupAndAddPermission($name, $permission_key) {
        $group = self::findGroup($name);
        if($group)
            return $group->addPermissionByKey($permission_key);
        return null;
    }

    public function scopeHasInterface($query) {
        return $query->where("has_interface", 1);
    }

    public function scopeHasNoInterface($query) {
        return $query->where("has_interface", 0);
    }

    public function scopeIsHidden($query) {
        return $query->where("is_hidden", 1);
    }

    public function scopeIsNotHidden($query) {
        return $query->where("is_hidden", 0);
    }

    public function scopeNotAdmin($query) {
        return $query->where("group_name", "!=", adminGroup());
    }

    public function hasValidInterface() {
        $this->group_routes_arr = unserialize($this->group_routes);
        $this->group_langs_arr = unserialize($this->group_langs);
        return (sizeof($this->group_routes_arr) > 0 && sizeof($this->group_langs_arr) > 0);
    }

    public function getRoute($for) {
        if(isset($this->group_routes_arr[$for]))
            return $this->group_routes_arr[$for];
        return null;
    }

    public function getLang($for) {
        if(isset($this->group_langs_arr[$for]))
            return $this->group_langs_arr[$for];
        return null;
    }

    public function canAccess($name) {
        $hasPermission = false;
        $checkPermission = PermissionItem::findPermission($name);
        if($checkPermission) {
            $permissionFound = $this->permissions()->where("permission_id", "=", $checkPermission->id)->first();
            if($permissionFound)
                $hasPermission = true;
        }
        return $hasPermission;
    }

    public function permissions() {
        return GroupToPermissionItem::where("group_id", "=", $this->id);
    }

    public function permissionIds() {
        $ids = array();
        foreach($this->permissions()->get() as $permission)
            $ids[] = $permission->permission_id;
        return $ids;
    }

    public function addPermissionByKey($permission_key) {
        $permission = PermissionItem::findPermission($permission_key);
        if($permission) {
            return $this->addPermission($permission);
        }
        return null;
    }

    public function addPermission($permission) {
        $gtp = GroupToPermissionItem::firstOrCreate(array(
            "group_id" => $this->id,
            "permission_id" => $permission->id
        ));
        return $gtp->save();
    }

    public function addPermissions($permissions) {
        foreach($permissions as $permission) {
            $this->addPermission($permission);
        }
    }

    public function addPermissionById($permission_id) {
        $gtp = GroupToPermissionItem::firstOrCreate(array(
            "group_id" => $this->id,
            "permission_id" => $permission_id
        ));
        return $gtp->save();
    }

    public function addPermissionsById($permissions_ids) {
        foreach($permissions_ids as $permissions_id) {
            $this->addPermissionById($permissions_id);
        }
    }

    public function removePermissionByKey($permission_key) {
        $permission = PermissionItem::findPermission($permission_key);
        if($permission) {
            $this->removePermission($permission);
        }
    }

    public function removePermission($permission) {
        $this->removePermissionById($permission->id);
    }

    public function removePermissionById($permission_id) {
        GroupToPermissionItem::where("group_id", "=", $this->id)->where("permission_id", "=", $permission_id)->delete();
    }

    public function dropPermissions() {
        GroupToPermissionItem::where("group_id", "=", $this->id)->delete();
    }

    public function saveUser($user) {
        $user->group_id = $this->id;
        $user->save();
    }

    public function createUser() {
        $user = new UserItem();
        $user->group_id = $this->id;
        return $user;
    }
}