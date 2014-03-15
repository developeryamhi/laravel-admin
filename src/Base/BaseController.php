<?php namespace Developeryamhi\LaravelAdmin\Base;

use Illuminate\Support\Facades\View;

abstract class BaseController extends \Illuminate\Routing\Controller {

    //  Is Admin Controller
    protected $in_admin = false;

    //  Namespace Name
    protected $namespaceName;

    //  Module Name
    protected $moduleName;

    //  Controller Name
    protected $controllerName;

    //  Inner Folder to Load From View
    protected $folderName;

    //  Layout for Page
    protected $layoutName = null;

    //  Permission Required for the Controller
    protected $permissionRequired = null;


    /**
     * Controller Init
     */
    protected function init() {
        //  Do Something Here by Overriding the Method
    }

    /**
     * Constructor
     */
    public function __construct() {

        //  Validate Controller Name
        if(is_null($this->controllerName))
            app()->abort(500, "Controller Name Attribute [\$controllerName] for class [" . \class_basename($this) . "] is Missing");

        //  Controller
        $controller = $this;

        //  Share the Controller
        app()->instance("controller", $controller);

        //  Share Controller Information with Application
        app()->bind("controller_info", function() use ($controller) {

            //  Return Array
            return array(
                "in_admin" => $controller->inAdmin(),
                "namespace" => $controller->namespaceName(),
                "module_name" => $controller->moduleName(),
                "controller_name" => $controller->controllerName(),
                "folder" => $controller->folderName(),
                "layout" => $controller->layoutName()
            );
        });

        //  Trigger Controller Created Event
        app("events")->fire("controller.created", array($this, app()));

        //  Check for In Admin
        if($this->inAdmin()) {

            //  Trigger Admin Controller Created Event
            app("events")->fire("admin.controller.created", array($this, app()));
        } else {

            //  Trigger Base Controller Created Event
            app("events")->fire("base.controller.created", array($this, app()));
        }

        //  Check Function Exists
        if(function_exists("mergeEventFireResponse")) {

            //  Add Before Filter
            $this->beforeFilter(function() use ($controller) {

                //  Trigger Controller Filter Before Event
                $return = \mergeEventFireResponse(null, app("events")->fire("controller.filter.before", array($controller)));

                //  Check Return
                if($return) return $return;

                //  Check for In Admin
                if($controller->inAdmin()) {

                    //  Trigger Admin Controller Filter Before Event
                    $return2 = \mergeEventFireResponse(null, app("events")->fire("admin.controller.filter.before", array($controller)));

                    //  Check Return
                    if($return2) return $return2;
                } else {

                    //  Trigger Base Controller Filter Before Event
                    $return3 = \mergeEventFireResponse(null, app("events")->fire("base.controller.filter.before", array($controller)));

                    //  Check Return
                    if($return3) return $return3;
                }
            });

            //  Add After Filter
            $this->afterFilter(function() use ($controller) {

                //  Trigger Controller Filter After Event
                $return = \mergeEventFireResponse(null, app("events")->fire("controller.filter.after", array($controller)));

                //  Check Return
                if($return) return $return;

                //  Check for In Admin
                if($controller->inAdmin()) {

                    //  Trigger Admin Controller Filter After Event
                    $return2 = \mergeEventFireResponse(null, app("events")->fire("admin.controller.filter.after", array($controller)));

                    //  Check Return
                    if($return2) return $return2;
                } else {

                    //  Trigger Base Controller Filter After Event
                    $return3 = \mergeEventFireResponse(null, app("events")->fire("base.controller.filter.after", array($controller)));

                    //  Check Return
                    if($return3) return $return3;
                }
            });
        }

        //  Run Controller Init
        $this->init();
    }

    /* Get In Admin */
    public function inAdmin() { return $this->in_admin; }

    /* Get Namespace */
    public function namespaceName() { return $this->namespaceName; }

    /* Get Module Name */
    public function moduleName() { return $this->controllerName; }

    /* Get Controller Name */
    public function controllerName() { return $this->controllerName; }

    /* Get Folder Name */
    public function folderName() { return $this->folderName; }

    /* Get Layout Name */
    public function layoutName() { return $this->layoutName; }

    /**
     * Override Layout Setup
     */
    protected function setupLayout() {
        if (!is_null($this->layoutName)) {
            $this->layout = View::make($this->layoutName);
        }
    }

    /**
     * Get Required Permission
     */
    public function permissionRequired() {
        return $this->permissionRequired;
    }

    /**
     * Exclude From Permissions
     */
    public function excludeFromPermissions($method, $permissions) {
        return false;
    }

    /**
     * Called when Permission is not available
     */
    public function accessDenied() {

        //  Return Error Message
        return setErrorMessageRedirect("You do not have proper permissions to view the requested page", "dashboard");
    }

    /**
     * Render View for Controller
     * 
     * @param string $view
     * @param array $params
     * @return mixed
     */
    protected function _render($view = "index", $params = array()) {

        //  Trigger Controller Ready Event
        app("events")->fire("controller.ready", array($this, app(), $view, $params));

        //  Check for In Admin
        if($this->inAdmin()) {

            //  Trigger Admin Controller Ready Event
            app("events")->fire("admin.controller.ready", array($this, app(), $view, $params));
        } else {

            //  Trigger Base Controller Ready Event
            app("events")->fire("base.controller.ready", array($this, app(), $view, $params));
        }

        //  View Path
        $viewPath = (isset($this->folderName) ? $this->folderName . "." : '') . $view;

        //  Namespace
        $namespace = ($this->moduleName ? $this->moduleName : ($this->namespaceName ? $this->namespaceName : null));

        //  Render View
        if($this->layout)
            $this->layout->content = makeView($viewPath, $namespace, $params);
        else
            return makeView($viewPath, $namespace, $params);
    }
}