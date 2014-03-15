<?php namespace Developeryamhi\AuthModule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Developeryamhi\LaravelAdmin\Base\Eloquent;

class UserItem extends Eloquent implements UserInterface, RemindableInterface {

    protected $softDelete = true;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Constructor
     * 
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("auth-module::table_users");
        parent::__construct($attributes);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail() {
        return $this->email;
    }

    public static function findUser($name) {
        return self::where('username', '=', $name)->first();
    }

    public function group() {
        return $this->belongsTo('\\Developeryamhi\\AuthModule\\GroupItem')->first();
    }

    public function canAccess($name) {
        return $this->group()->canAccess($name);
    }

    public function isGroup($group) {
        return ($this->group()->group_name == $group);
    }

    public function isEnabled() {
        return ($this->enabled == 1);
    }

    public function metas() {
        return UserMetaItem::where("user_id", "=", $this->id);
    }

    public function populate_metas() {
        foreach($this->getMetas() as $meta)
            $this->{"user_meta_" . $meta->meta_key} = $meta->meta_value;
    }

    public function discard_metas() {
        foreach(array_keys($this->attributesToArray()) as $attr_key) {
            if(substr($attr_key, 0, 10) == "user_meta_")
                $this->offsetUnset($attr_key);
        }
    }

    public function extract_metas() {
        $metas = array();
        foreach($this->attributesToArray() as $attr_key => $attr_value) {
            if(substr($attr_key, 0, 10) == "user_meta_") {
                $metas[substr($attr_key, 10)] = $attr_value;
            }
        }
        return $metas;
    }

    public function getMeta($key) {
        return UserMetaItem::findMeta($key, $this->id);
    }

    public function getMetaValue($key) {
        $meta = $this->getMeta($key);
        if($meta)
            return $meta->meta_value;
        return null;
    }

    public function getMetas() {
        return UserMetaItem::where("user_id", "=", $this->id)->get();
    }

    public function saveMeta($mKey, $mValue) {
        $meta = (UserMetaItem::findMeta($mKey, $this->id) ? UserMetaItem::findMeta($mKey, $this->id) : new UserMetaItem());
        $meta->user_id = $this->id;
        $meta->meta_key = $mKey;
        $meta->meta_value = $mValue;
        $meta->save();
    }

    public function saveMetas($metas) {
        foreach($metas as $mKey => $mValue) {
            $this->saveMeta($mKey, $mValue);
        }
    }

    public static function dashboardRoute() {
        return mergeEventFireResponse('dashboard', Event::fire('route.dashboard', 'dashboard'));
    }

    public static function loginRoute() {
        return mergeEventFireResponse('login', Event::fire('route.login', 'login'));
    }

    public static function logoutRoute() {
        return mergeEventFireResponse('logout', Event::fire('route.logout', 'logout'));
    }

    public static function loginRouteParams() {
        return mergeEventFireResponse(array(), Event::fire('route_params.login'));
    }

    public static function logoutRouteParams() {
        return mergeEventFireResponse(array(), Event::fire('route_params.logout'));
    }

    public static function loginRouteArray($is_form = false) {
        $route = array(self::loginRoute());
        $route_array = array("route" => $route, "is_form" => $is_form);
        return mergeEventFireResponse($route, Event::fire('route_array.login', $route_array));
    }

    public static function logoutRouteArray() {
        $route = array(self::logoutRoute());
        return mergeEventFireResponse($route, Event::fire('route_array.logout', $route));
    }

    public function scopeFilterGroup($query, $group_name) {
        return $query->where("group_id", "=", GroupItem::findGroup($group_name)->id);
    }

    public function scopeFilterNotGroup($query, $group_name) {
        return $query->where("group_id", "!=", GroupItem::findGroup($group_name)->id);
    }

    public function scopeGroupWithoutInterface($query) {
        $groupIds = array_values(GroupItem::hasInterface()->lists("id", "group_name"));
        if($groupIds)
            $query->whereNotIn("group_id", $groupIds);
        return $query;
    }

    public function scopeNotMe($query) {
        if(Auth::check())
            $query->where("id", "!=", Auth::user()->id);
        return $query;
    }
}
