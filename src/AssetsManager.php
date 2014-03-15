<?php namespace Developeryamhi\LaravelAdmin;

use Illuminate\Support\Facades\View;
use Illuminate\Filesystem\Filesystem;

//  Define Theme Locations
define('VIEW_LOCATION_HEADER', "header");
define('VIEW_LOCATION_FOOTER', "footer");

//  Define Flash Message Types
define('FLASH_MSG_SUCCESS', "msg_success");
define('FLASH_MSG_ERROR', "msg_error");
define('FLASH_MSG_WARNING', "msg_warning");
define('FLASH_MSG_INFO', "msg_info");


class AssetsManager {

    //  Page Title Data
    protected $_page_title = null;
    protected $_page_title_seperator = " &raquo; ";

    //  Data to Share with Views
    protected $_data = array();

    //  Meta Data for Theme
    protected $_metas = array();

    //  Styles for Theme
    protected $_styles = array();

    //  Scripts for Theme
    protected $_scripts = array();

    //  Styles to Use
    protected $_use_styles = array();

    //  Scripts to Use
    protected $_use_scripts = array();

    //  App Instance
    private $app;

    //  Assets Helper Instance
    private $_helper;


    public function __construct($app) {

        //  Store App Instance
        $this->app = $app;

        //  Create Assets Helper Instance
        $this->app["assets_helper"] = $this->_helper = new AssetsHelper($this->app);

        //  Reset Resources
        $this->resetResources();
    }

    /**
     * Get Config Value for Package
     * 
     * @param type $key
     */
    public function getConfig($key) {
        return $this->app["config"]->get("laravel-admin::theme_manager." . $key);
    }

    public function resetResources($clear_registered = false) {

        //  Reset Resources
        $this->_metas = array();
        $this->_use_styles = array(
            VIEW_LOCATION_HEADER => array(),
            VIEW_LOCATION_FOOTER => array()
        );
        $this->_use_scripts = array(
            VIEW_LOCATION_HEADER => array(),
            VIEW_LOCATION_FOOTER => array()
        );

        //  Check for Registered Resets
        if($clear_registered) {

            //  Reset Registered Resources
            $this->_styles = array();
            $this->_scripts = array();
        }
    }

    public function makeView($view = 'index', $namespace = null, $data = array(), $mergeData = array()) {
        $loadView = ($namespace ? $namespace . "::" : '') . $view;
        $overrideView = null;
        if($namespace)
            $overrideView = $namespace . "." . $view;
        if($overrideView) {
            $fSystem = new Filesystem();
            $overridePath1 = $this->app->make("path.base") . "/app/views/" . $namespace . "/" . $view . ".php";
            $overridePath2 = $this->app->make("path.base") . "/app/views/" . $namespace . "/" . $view . ".blade.php";
            if($fSystem->exists($overridePath1) || $fSystem->exists($overridePath2))
                $loadView = $overrideView;
        }

        if(sizeof($this->_data) > 0) {
            foreach($this->_data as $sKey => $sVal)
                View::share($sKey, $sVal);
        }

        return View::make($loadView, $data, $mergeData);
    }

    public function setBasePageTitle($title) {
        $this->_page_title = $title;
        $this->setPageTitle($title, true);
    }

    public function setPageTitle($title, $hard = false) {
        if($hard)
            $this->setPageData('pageTitle', $title);
        else
            $this->setPageData('pageTitle', $title . $this->_page_title_seperator . $this->_page_title);
    }

    public function setPageTitleSeperator($sep) {
        $existingTitle = $this->getPageData("pageTitle");
        $newTitle = str_ireplace($this->_page_title_seperator, $sep, $existingTitle);
        $this->setPageTitle($newTitle, true);
        $this->_page_title_seperator = $sep;
    }

    public function setPageData($key, $value) {
        $this->_data[$key] = $value;
    }

    public function pageHasData($key) {
        if(isset($this->_data[$key]))
            return true;
        return false;
    }

    public function getPageData($key, $def = null) {
        return (isset($this->_data[$key]) ? $this->_data[$key] : $def);
    }

    public function removePageData($key) {
        if(isset($this->_data[$key]))
            unset($this->_data[$key]);
    }

    public function register_style($src, $name = null) {
        $name || $name = $src;
        $this->_styles[$name] = $src;
    }

    public function unregister_style($name) {
        unset($this->_styles[$name]);
    }

    public function register_script($src, $name = null) {
        $name || $name = $src;
        $this->_scripts[$name] = $src;
    }

    public function unregister_script($name) {
        unset($this->_scripts[$name]);
    }

    public function enqueue_style($name, $is_inline = false, $data = null, $section = VIEW_LOCATION_HEADER, $media = "all", $rel = "stylesheet", $attrs = array()) {
        if(is_string($name) && strpos($name, '|')>0)   $name = explode('|', $name);
        if(is_array($name)) {
            foreach($name as $n)
                $this->enqueue_style($n, $is_inline);
        } else {
            $style_data = $this->getStyle($name);
            if(!$style_data && $data) {
                $this->register_style($data, $name, $section);
                $style_data = $data;
            }
            if($style_data) {
                $attrs["media"] = $media;
                $attrs["rel"] = $rel;
                $this->_use_styles[$section][$name] = array("inline" => $is_inline, "data" => ($is_inline ? $style_data : ((strpos($style_data, "://") > -1) ? $style_data : $this->_helper->cssUrl($style_data))), "attrs" => $attrs);
            }
        }
    }

    public function unenqueue_style($name, $section = VIEW_LOCATION_HEADER) {
        if(isset($this->_use_styles[$section][$name]))
            unset($this->_use_styles[$section][$name]);
    }

    public function enqueue_script($name, $is_inline = false, $data = null, $section = VIEW_LOCATION_FOOTER, $attrs = array()) {
        if(is_string($name) && strpos($name, '|')>0)   $name = explode('|', $name);
        if(is_array($name)) {
            foreach($name as $n)
                $this->enqueue_script($n, $is_inline);
        } else {
            $script_data = $this->getScript($name);
            if(!$script_data && $data) {
                $this->register_script($data, $name, $section);
                $script_data = $data;
            }
            if($script_data) {
                
                $this->_use_scripts[$section][$name] = array("inline" => $is_inline, "data" => ($is_inline ? $script_data : ((strpos($script_data, "://") > -1) ? $script_data : $this->_helper->jsUrl($script_data))), "attrs" => $attrs);
            }
        }
    }

    public function unenqueue_script($name, $section = VIEW_LOCATION_FOOTER) {
        if(isset($this->_use_scripts[$section][$name]))
            unset($this->_use_scripts[$section][$name]);
    }

    public function setMeta($name, $content, $attrs = array()) {
        $this->_metas[$name] = array_merge(array("name" => $name, "content" => $content), $attrs);
    }

    public function setRawMeta($key, $val, $attrs = array()) {
        $this->_metas[$key] = array_merge(array($key => $val), $attrs);
    }

    public function removeMeta($key) {
        if(isset($this->_metas[$key]))
            unset($this->_metas[$key]);
    }

    public function getStyle($name) {
        if(isset($this->_styles[$name]))    return $this->_styles[$name];
        return null;
    }

    public function getScript($name) {
        if(isset($this->_scripts[$name]))    return $this->_scripts[$name];
        return null;
    }

    public function getMeta($key) {
        if(isset($this->_metas)) {
            return $this->_metas[$key];
        }
        return null;
    }

    public function printableStyles($section) {
        $output = "";

        $styles = $this->_use_styles[$section];
        foreach($styles as $style_key => $style) {
            if($style["inline"]) {
                $output .= '<style type="text/css" ' . $this->prepareAttrs($style["attrs"]) . '>' . PHP_EOL;
                $output .= $style["data"];
                $output .= PHP_EOL . '</style>' . PHP_EOL;
            } else {
                $output .= '<link type="text/css" href="' . $style["data"] . '" ' . $this->prepareAttrs($style["attrs"]) . ' />' . PHP_EOL;
            }
        }

        return $output;
    }

    public function printableScripts($section) {
        $output = "";

        $scripts = $this->_use_scripts[$section];
        foreach($scripts as $script_key => $script) {
            $output .= '<script ';
            if($script["inline"]) {
                $output .= $this->prepareAttrs(array_merge(array("type" => "text/javascript"), $script["attrs"])) . '>' . PHP_EOL . $script["data"] . PHP_EOL;
            } else {
                $output .= 'src="' . $script["data"] . '" ' . $this->prepareAttrs($script["attrs"]) . '>';
            }
            $output .= '</script>' . PHP_EOL;
        }

        return $output;
    }

    public function printableMetas() {
        $output = "";
        foreach($this->_metas as $meta)
            $output .= "<meta " . $this->prepareAttrs($meta) . " />" . PHP_EOL;
        return $output;
    }

    public function prepareAttrs($attrs) {
        $output = array();
        foreach($attrs as $key => $val) {
            if(empty($key))
                $output[] = $val;
            else
                $output[] = $key . '="' . $val . '"';
        }
        return implode(" ", $output);
    }
}