<?php namespace Developeryamhi\LaravelAdmin;

class AssetsHelper {

    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function casingFormatted($str) {
        $matches = null;
        if(preg_match_all('/[A-Z][^A-Z]*/', $str, $matches)) {
            $strs = array();
            foreach($matches[0] as $match)
                $strs[] = strtolower($match);
            $str = implode("_", $strs);
        }
        return str_ireplace("-_", "_", $str);
    }

    public function in_admin() {
        return ((isset($this->app["controller_info"]) && $this->app["controller_info"]["in_admin"] == true) ? true : false);
    }

    public function routeInfo() {

        //  Route Info Holder
        $info = array();

        //  Get Router
        $router = $this->app["router"];

        //  Get Action
        $action = ($router->current() ? $router->current()->getAction() : null);

        //  Check Action
        if(!$action)    return null;

        //  Get Controller
        $controller = (isset($action["controller"]) ? $action["controller"] : "Closure@closure");

        //  Explode for Method
        $explodes1 = explode("@", $controller);

        //  Explode for Controller
        $explodes2 = explode("\\", $explodes1[0]);

        //  Explode Again for Controller Name
        $explodes3 = explode("Controller", end($explodes2));

        //  Controller Name
        $controller_name = $this->casingFormatted(implode("", $explodes3));

        //  Get Controller Info
        $controller_info = (isset($this->app["controller_info"]) ? $this->app["controller_info"] : array(
            "module_name" => null,
            "controller_name" => $controller_name
        ));

        //  Store Action
        $info["action"] = $action;

        //  Store Module Name
        $info["module"] = $controller_info["module_name"];

        //  Store Controller Name
        $info["controller"] = $controller_info["controller_name"];

        //  Store Method
        $info["method"] = (sizeof($explodes1) > 1 ? $explodes1[1] : null);

        //  Return
        return $info;
    }

    public function enqueue_detected_css($routeInfo = null) {
        $routeInfo || $routeInfo = $this->routeInfo();

        $module = $routeInfo['module'];
        $controller = $routeInfo['controller'];
        $method = $routeInfo['method'];

        $mcss_file = null;
        if($module) {
            $f1 = $module . '-' . $controller . '-' . $method . '.css';
            $f2 = $module . '-' . $controller . '.css';
            $f3 = $module . '-' . $method . '.css';
            $f4 = $module . '.css';
        } else {
            $f1 = $controller . '-' . $method . '.css';
            $f2 = $controller . '.css';
            $f3 = false;
            $f4 = false;
        }
        if(file_exists($this->moduleCssPath($f1))) {
            $mcss_file = $f1;
        }
        else if(file_exists($this->moduleCssPath($f2))) {
            $mcss_file = $f2;
        }
        else if($f3 && file_exists($this->moduleCssPath($f3))) {
            $mcss_file = $f3;
        }
        else if($f4 && file_exists($this->moduleCssPath($f4))) {
            $mcss_file = $f4;
        }
        if($mcss_file)
            return '<link type="text/javascript" href="' . $this->moduleCssUrl($mcss_file) . '" rel="stylesheet" />';
        return null;
    }

    public function enqueue_detected_js($routeInfo = null) {
        $routeInfo || $routeInfo = $this->routeInfo();

        $module = $routeInfo['module'];
        $controller = $routeInfo['controller'];
        $method = $routeInfo['method'];

        $mjs_file = null;
        if($module) {
            $f1 = $module . '-' . $controller . '-' . $method . '.js';
            $f2 = $module . '-' . $controller . '.js';
            $f3 = $module . '-' . $method . '.js';
            $f4 = $module . '.js';
        } else {
            $f1 = $controller . '-' . $method . '.js';
            $f2 = $controller . '.js';
            $f3 = false;
            $f4 = false;
        }
        if(file_exists($this->moduleJsPath($f1))) {
            $mjs_file = $f1;
        }
        else if(file_exists($this->moduleJsPath($f2))) {
            $mjs_file = $f2;
        }
        else if($f3 && file_exists($this->moduleJsPath($f3))) {
            $mjs_file = $f3;
        }
        else if($f4 && file_exists($this->moduleJsPath($f4))) {
            $mjs_file = $f4;
        }
        if($mjs_file)
            return '<script type="text/javascript" src="' . $this->moduleJsUrl($mjs_file) . '"></script>';
        return null;
    }

    public function assetsPath($file = '') {
        return base_path($this->app["assets_manager"]->getConfig("assets_dir") . $file);
    }

    public function cssPath($file = '') {
        return $this->assetsPath($this->app["assets_manager"]->getConfig("css_dir") . $file);
    }

    public function jsPath($file = '') {
        return $this->assetsPath($this->app["assets_manager"]->getConfig("js_dir") . $file);
    }

    public function imagePath($file = '') {
        return $this->assetsPath($this->app["assets_manager"]->getConfig("image_dir") . $file);
    }

    public function uploadPath($file = '') {
        return $this->assetsPath($this->app["assets_manager"]->getConfig("upload_dir") . $file);
    }

    public function moduleAssetsPath($file = '') {
        return $this->assetsPath($this->app["assets_manager"]->getConfig("module_assets_dir") . $file);
    }

    public function moduleCssPath($file = '') {
        return $this->moduleAssetsPath($this->app["assets_manager"]->getConfig("css_dir") . $file);
    }

    public function moduleJsPath($file = '') {
        return $this->moduleAssetsPath($this->app["assets_manager"]->getConfig("js_dir") . $file);
    }

    public function moduleImagePath($file = '') {
        return $this->moduleAssetsPath($this->app["assets_manager"]->getConfig("image_dir") . $file);
    }

    public function packageAssetsPath($package, $file = '') {
        return app()->make("path.public") . "packages/" . $package . "/" . $file;
    }

    public function packageCssPath($package, $file = '') {
        return $this->packageAssetsPath($package, $this->app["assets_manager"]->getConfig("css_dir") . $file);
    }

    public function packageJsPath($package, $file = '') {
        return $this->packageAssetsPath($package, $this->app["assets_manager"]->getConfig("js_dir") . $file);
    }

    public function packageImagePath($package, $file = '') {
        return $this->packageAssetsPath($package, $this->app["assets_manager"]->getConfig("image_dir") . $file);
    }

    public function assetsUrl($file = '') {
        return $this->app["request"]->root() . "/" . $this->app["assets_manager"]->getConfig("assets_dir") . $file;
    }

    public function cssUrl($file = '') {
        return $this->assetsUrl($this->app["assets_manager"]->getConfig("css_dir") . $file);
    }

    public function jsUrl($file = '') {
        return $this->assetsUrl($this->app["assets_manager"]->getConfig("js_dir") . $file);
    }

    public function imageUrl($file = '') {
        return $this->assetsUrl($this->app["assets_manager"]->getConfig("image_dir") . $file);
    }

    public function uploadUrl($file = '') {
        return $this->assetsUrl($this->app["assets_manager"]->getConfig("upload_dir") . $file);
    }

    public function moduleAssetsUrl($file = '') {
        return $this->assetsUrl($this->app["assets_manager"]->getConfig("module_assets_dir") . $file);
    }

    public function moduleCssUrl($file = '') {
        return $this->moduleAssetsUrl($this->app["assets_manager"]->getConfig("css_dir") . $file);
    }

    public function moduleJsUrl($file = '') {
        return $this->moduleAssetsUrl($this->app["assets_manager"]->getConfig("js_dir") . $file);
    }

    public function moduleImageUrl($file = '') {
        return $this->moduleAssetsUrl($this->app["assets_manager"]->getConfig("image_dir") . $file);
    }

    public function packageAssetsUrl($package, $file = '') {
        return $this->app["request"]->root() . "/packages/" . $package . "/" . $file;
    }

    public function packageCssUrl($package, $file = '') {
        return $this->packageAssetsUrl($package, $this->app["assets_manager"]->getConfig("css_dir") . $file);
    }

    public function packageJsUrl($package, $file = '') {
        return $this->packageAssetsUrl($package, $this->app["assets_manager"]->getConfig("js_dir") . $file);
    }

    public function packageImageUrl($package, $file = '') {
        return $this->packageAssetsUrl($package, $this->app["assets_manager"]->getConfig("image_dir") . $file);
    }
}