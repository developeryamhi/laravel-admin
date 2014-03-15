<?php namespace Developeryamhi\AuthModule;

use Illuminate\Support\Facades\DB;
use Developeryamhi\LaravelAdmin\Base\Eloquent;

class UserMetaItem extends Eloquent {

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("auth-module::table_user_metas");
        parent::__construct($attributes);
    }

    public static function findMeta($name, $user_id = null) {
        return self::where('meta_key', '=', $name)->where("user_id", ($user_id ? "=" : ">"), ($user_id ? $user_id : "0"))->first();
    }

    public static function collectUserIds($key, $value) {
        $user_ids = array();
        $items = self::where("meta_key", "=", $key)->where("meta_value", "=", $value)->get();
        foreach($items as $item)
            $user_ids[] = $item->user_id;
        $user_ids = array_unique($user_ids);
        return $user_ids;
    }

    public static function collectUsers($key, $value) {
        $user_ids = self::collectUserIds($key, $value);
        if(sizeof($user_ids) < 1)   return array();
        return UserItem::whereIn('id', $user_ids)->select(DB::raw('concat (first_name, " ", last_name) as full_name,id'))->lists("full_name", "id");
    }
}