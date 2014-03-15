<?php namespace Developeryamhi\AuthModule;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Developeryamhi\LaravelAdmin\Base\AdminController;

class AuthController extends AdminController {

    protected $moduleName = "auth-module";

    protected $controllerName = "auth";

    protected function init() {

        //  Set Layout
        $this->layoutName = guestLayout();
    }

    public function __construct() {
        parent::__construct();
    }

    public function showLogin() {

        $this->_render('login');
    }

    public function doLogout() {

        //  Update Log
        LoginLogItem::updateLog(Auth::user(), null, true);

        //  Logout URL
        $logout_url = URL::route(UserItem::loginRoute(), UserItem::logoutRouteParams());

        //  Do Logout
        Auth::logout();

        //  Redirect
        return Redirect::to($logout_url)
                ->with(FLASH_MSG_INFO, trans("auth-module::message.success_logout"));;
    }

    public function doLogin() {

        // validate the info, create rules for the inputs
        $rules = array(
            'username' => 'required|min:5',
            'password' => 'required|min:6'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::route(UserItem::loginRoute(), UserItem::loginRouteParams())
                            ->withErrors($validator) // send back all errors to the login form
                            ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            // create our user data for the authentication
            $userdata = array(
                'username' => Input::get('username'),
                'password' => Input::get('password')
            );

            // attempt to do the login
            if (Auth::validate($userdata)) {

                //  Valid Login
                $valid_login = true;

                //  Get User
                $user = UserItem::findUser($userdata["username"]);

                //  Check if User Disabled
                if(!$user->isEnabled())
                    return Redirect::route(UserItem::loginRoute(), UserItem::loginRouteParams())
                            ->with(FLASH_MSG_ERROR, trans("auth-module::message.account_disabled"));

                //  Trigger Login Event & Validate
                mergeEventFireResponse(true, Event::fire('user.login_validate', array($user, &$valid_login)));

                //  Check Valid
                if($valid_login) {

                    //  Do Login
                    Auth::login($user);

                    //  Add Login Log
                    LoginLogItem::addLog($user, true);

                    //  Trigger Valid Login Event
                    Event::fire('user.valid_login', array($user));

                    // validation successful!
                    return Redirect::intended(URL::route(UserItem::dashboardRoute()))
                            ->with(FLASH_MSG_INFO, trans("auth-module::message.success_login"));
                } else {

                    //  Add Login Log
                    LoginLogItem::addLog($user, false);

                    //  Trigger Invalid Login Event
                    Event::fire('user.invalid_login', array($userdata['username']));

                    // validation not successful, send back to form	
                    return Redirect::route(UserItem::loginRoute(), UserItem::loginRouteParams())
                            ->with(FLASH_MSG_ERROR, trans("auth-module::message.invalid_login"))
                            ->withInput(Input::except('password'));
                }
            } else {

                //  Add Login Log
                LoginLogItem::addLogUsername($userdata["username"], false);

                //  Trigger Invalid Login Event
                Event::fire('user.invalid_login', array(Input::get('username')));

                // validation not successful, send back to form	
                return Redirect::route(UserItem::loginRoute(), UserItem::loginRouteParams())
                        ->with(FLASH_MSG_ERROR, trans("auth-module::message.invalid_login"))
                        ->withInput(Input::except('password'));
            }
        }
    }

}
