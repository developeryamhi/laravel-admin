<?php namespace Developeryamhi\AuthModule;

use Developeryamhi\LaravelAdmin\Base\Eloquent;

class LoginLogItem extends Eloquent {

    public $timestamps = false;

    public function __construct(array $attributes = array()) {
        $this->table = app("config")->get("auth-module::table_login_logs");
        parent::__construct($attributes);
    }

    public static function addLog($user, $success = 0) {
        self::addLogUsername($user->username, $success);
    }

    public static function addLogUsername($username, $success = 0) {
        self::insert(array(
            'username' => $username,
            'ip_address' => ip_address(),
            'success' => (int)$success,
            'attempt_at' => date("Y-m-d H:i:s")
        ));
    }

    public static function updateLog($user, $success = null, $logout_time = null) {
        $log = self::where("username", "=", $user->username)->orderBy("attempt_at", "DESC")->first();
        if($log) {
            if(!is_null($success))
                $log->success = (int)$success;
            if($logout_time)
                $log->logout_at = date("Y-m-d H:i:s");
            $log->save();
        }
    }
}