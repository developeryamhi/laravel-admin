<?php namespace Developeryamhi\AuthModule;

use Developeryamhi\LaravelAdmin\Base\Eloquent;

class GroupToPermissionItem extends Eloquent {

    protected $fillable = array("group_id", "permission_id");

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("auth-module::table_group_to_permissions");
        parent::__construct($attributes);
    }

    public function group() {
        return $this->belongsTo('\\Developeryamhi\\AuthModule\\GroupItem')->first();
    }

    public function permission() {
        return $this->belongsTo('\\Developeryamhi\\AuthModule\\PermissionItem')->first();
    }
}