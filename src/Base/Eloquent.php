<?php namespace Developeryamhi\LaravelAdmin\Base;

use Illuminate\Database\Eloquent\Model;

class Eloquent extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * Log User Data
     *
     * @var type 
     */
    protected $logId = false;

    /**
     * Log From User
     * 
     * @param type
     */
    protected $user = null;


    public function __construct(array $attributes = array(), $user = null) {
        parent::__construct($attributes);

        if($user)   $this->setLogUser($user);
        if(isset($this->tableKey))
            $this->table = app("config")->get("table." . $this->tableKey);
    }

    public function scopeActive($query) {
        return $query->where("is_active", '=', 1);
    }

    public function scopeInactive($query) {
        return $query->where("is_active", '=', 0);
    }

    public function scopeEnabled($query) {
        return $query->where("enabled", '=', 1);
    }

    public function scopeDisabled($query) {
        return $query->where("enabled", '=', 0);
    }

    public function isActive() {
        return (bool)$this->is_active;
    }

    public function activeText() {
        return ($this->is_active == 1 ? "Active" : "Inactive");
    }

    public function isEnabled() {
        return (bool)$this->enabled;
    }

    public function enabledText() {
        return ($this->enabled == 1 ? "Yes" : "No");
    }

    public static function isValid($id) {
        return (self::find($id) != NULL);
    }

    public static function alreadyExists($key, $val) {
        return (self::where($key, "=", $val)->get()->first() != NULL);
    }

    public static function alreadyExistingItem($key, $val) {
        return self::where($key, "=", $val)->get()->first();
    }

    public function setLogUser($user) {
        $this->user = $user;
        return $this;
    }

    public function logUser() {
        return $this->user;
    }

    public function save(array $options = array(), $user = null) {
        if($user)   $this->setLogUser($user);
        if($this->logId && $this->user) {
            if($this->user) {
                if(!$this->exists)  $this->setAttribute("created_by", $this->user->id);
                $this->setAttribute("updated_by", $this->user->id);
            }
        }
        parent::save($options);
    }

    public function delete($user = null) {
        if($user)   $this->setLogUser($user);
        if($this->logId && $this->softDelete && $this->user) {
            $query = $this->newQuery()->where($this->getKeyName(), $this->getKey());
            $this->deleted_by = $this->user->id;
            $query->update(array("deleted_by" => $this->user->id));
        }
        parent::delete();
    }
}