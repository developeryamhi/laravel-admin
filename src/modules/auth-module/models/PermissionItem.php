<?php namespace Developeryamhi\AuthModule;

use Developeryamhi\LaravelAdmin\Base\Eloquent;

class PermissionItem extends Eloquent {

    protected $fillable = array("permission_key");

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("auth-module::table_permissions");
        parent::__construct($attributes);
    }

    public static function findPermission($name) {
        return self::where('permission_key', '=', $name)->first();
    }

    public static function addPermission($permission_key, $permission_description = null) {
        $permission = self::firstOrCreate(array(
            "permission_key" => $permission_key
        ));
        if($permission_description)
            $permission->permission_description = $permission_description;
        $permission->save();
        return $permission;
    }

    public function scopeNotDashboard($query) {
        return $query->where("permission_key", "!=", "dashboard");
    }

    public function getDropped() {
        GroupToPermissionItem::where("permission_id", $this->id)->delete();
    }

    public static function forDashboard() {
        return PermissionItem::findPermission("dashboard");
    }

    public function isForDashboard() {
        return ($this->permission_key == "dashboard");
    }

    public function getAdded($group) {
        $gtp = GroupToPermissionItem::firstOrCreate(array(
            "group_id" => $group->id,
            "permission_id" => $this->id
        ));
        return $gtp->save();
    }

    public function getAddedByName($group_name) {
        $group = GroupItem::findGroup($group_name);
        if($group) {
            $gtp = GroupToPermissionItem::firstOrCreate(array(
                "group_id" => $group->id,
                "permission_id" => $this->id
            ));
            $gtp->save();
        }
    }
}