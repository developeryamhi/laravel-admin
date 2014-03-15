<?php namespace Developeryamhi\LaravelAdmin;

class NavigationHelper {

    // App Instance
    private $app;

    //  Navigation Name
    private $name;

    //  Navigation Instance Names
    private static $instance_names = array();

    //  Navigation Items
    private $_items = array();

    //  Right Navigation Items
    private $_right_items = array();

    public function __construct($app, $name) {

        //  Store App Instance
        $this->app = $app;

        //  Store Name
        $this->name = $name;

        //  Clear Items
        $this->_items = array();

        //  Instance Name to Store in App
        $app_instance = "nav-group-" . $name;

        //  Store Instance Name
        self::$instance_names[$name] = $app_instance;

        //  Store Instance to App
        $app->instance($app_instance, $this);
    }

    public static function getInstanceName($name) {
        if(isset(self::$instance_names[$name]))
            return self::$instance_names[$name];
        return null;
    }

    public function addMenuItem($key, $label, $url = "#", $subnavs = array(), $pregs = null, $order = null) {
        if(is_null($order))
            $order = sizeof($this->_items);

        $subnavs || $subnavs = array();
        if(isset($this->_items[$key])
                && sizeof($this->_items[$key]['subnavs']) > 0)
            $subnavs = $this->_items[$key]['subnavs'];

        $this->_items[$key] = array(
            'label' => $label,
            'url' => $url,
            'subnavs' => $subnavs,
            'order' => $order,
            'pregs' => $pregs
        );
    }

    public function getMenuItem($key) {
        if(isset($this->_items[$key]))
            return $this->_items[$key];
        return null;
    }

    public function detectAddSubMenuItem($parent_key, $key, $label, $url = "#", $pregs = null, $order = null) {
        if($parent_key && $this->getMenuItem($parent_key))
            $this->addSubMenuItem($parent_key, $key, $label, $url, $pregs, $order);
        else
            $this->addMenuItem($key, $label, $url, array(), $pregs, $order);
    }

    public function addSubMenuItem($parent_key, $key, $label, $url = "#", $pregs = null, $order = null) {
        $parent = (isset($this->_items[$parent_key]) ? $this->_items[$parent_key] : null);
        if(!$parent) {
            $this->addMenuItem($parent_key, $label, $url);
        }

        if(is_null($order))
            $order = sizeof($this->_items[$parent_key]["subnavs"]);

        $this->_items[$parent_key]["subnavs"][$key] = array(
            'label' => $label,
            'url' => $url,
            'subnavs' => array(),
            'order' => $order,
            'pregs' => $pregs
        );
    }

    public function getSubMenuItem($key, $subkey) {
        $menuItem = $this->getMenuItem($key);
        if($menuItem && isset($menuItem["subnavs"][$subkey]))
            return $menuItem["subnavs"][$subkey];
        return null;
    }

    public function detectAddSubSubMenuItem($parent_key, $sub_parent_key, $key, $label, $url = "#", $pregs = null, $order = null) {
        if($parent_key && $this->getSubMenuItem($parent_key, $sub_parent_key))
            $this->addSubSubMenuItem($parent_key, $sub_parent_key, $key, $label, $url, $pregs, $order);
        else
            $this->addSubMenuItem($sub_parent_key, $key, $label, $url, $pregs, $order);
    }

    public function addSubSubMenuItem($parent_key, $sub_parent_key, $key, $label, $url = "#", $pregs = null, $order = null) {
        $parent = (isset($this->_items[$parent_key]) ? $this->_items[$parent_key] : null);
        if(!$parent) {
            $this->addMenuItem($parent_key, $label, $url);
        }
        $sub_parent = (isset($parent["subnavs"][$sub_parent_key]) ? $parent["subnavs"][$sub_parent_key] : null);
        if(!$sub_parent) {
            $this->addSubMenuItem($parent_key, $sub_parent_key, $label, $url);
        }

        if(is_null($order))
            $order = sizeof($this->_items[$parent_key]["subnavs"][$sub_parent_key]["subnavs"]);

        $this->_items[$parent_key]["subnavs"][$sub_parent_key]["subnavs"][$key] = array(
            'label' => $label,
            'url' => $url,
            'subnavs' => array(),
            'order' => $order,
            'pregs' => $pregs
        );
    }

    public function getSubSubMenuItem($key, $subkey, $subsubkey) {
        $subMenuItem = $this->getSubMenuItem($key, $subkey);
        if($subMenuItem && isset($subMenuItem["subnavs"][$subsubkey]))
            return $subMenuItem["subnavs"][$subsubkey];
        return null;
    }

    public function removeMenuItem($key) {
        if(isset($this->_items[$key]))
            unset($this->_items[$key]);
    }

    public function removeSubMenuItem($parent_key, $key) {
        if(isset($this->_items[$parent_key])) {
            if(isset($this->_items[$parent_key]["subnavs"][$key]))
                unset($this->_items[$parent_key]["subnavs"][$key]);
        }
    }

    public function addRightMenuItem($key, $label, $url = "#", $pregs = null, $order = null) {
        if(is_null($order))
            $order = sizeof($this->_right_items);

        $this->_right_items[$key] = array(
            'label' => $label,
            'url' => $url,
            'order' => $order,
            'pregs' => $pregs
        );
    }

    public function hasNavigation() {
        return (sizeof($this->_items) > 0);
    }

    public function getItems() {
        return $this->_items;
    }

    public function navigation() {
        $items = $this->getItems();
        if(sizeof($items) > 0) {
            return $this->renderNavigation();
        }
        return "";
    }

    public function fullNavigation($menuTitle = null, $route = null) {
        $output = "";
        $navOutput = $this->navigation();
        if(strlen($navOutput) > 0) {
            $output .= '
            <navigation class="navbar navbar-default navbar-fixed-top" role="navigation">
                <div class="container">
                    <div class="navbar-header">';
            if($menuTitle)
                $output .= '<a href="' . ($route ? $this->app["url"]->route($route) : '#') . '" class="navbar-brand">' . $menuTitle . '</a>';
            $output .= '<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>';
            $output .= $navOutput;
            $output .= '
                </div>
            </navigation>';
        }
        return $output;
    }

    public function renderNavigation($items = null, $sub = false, $level = 0) {
        $items || $items = $this->getItems();

        $output = '';
        if($level < 1) {
            $output .= '
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">';
        }

        $alreadyFoundActive = false;
        $requestedURL = $this->app["request"]->url();
        $requestedPath = $this->app["request"]->path();

        //  Order Menu Items
        usort($items, array($this, 'sortOrderAsc'));

        foreach($items as $nav_key => $nav_data) {
            $subnavs = array();
            $has_subnav = (isset($nav_data["subnavs"]) && sizeof($nav_data["subnavs"]) > 0) ? true : false;
            if($has_subnav)
                $subnavs = $nav_data["subnavs"];

            if($nav_data["url"] == "#" && !$has_subnav) continue;

            $foundInSubnav = false;

            $pregs = (isset($nav_data["pregs"]) ? $nav_data["pregs"] : array());
            $includePregs = (isset($pregs["include"]) ? $pregs["include"] : array());
            $excludePregs = (isset($pregs["exclude"]) ? $pregs["exclude"] : array());

            $li_classes = array();
            if($has_subnav) $li_classes[] = "dropdown";

            if(!$alreadyFoundActive && $requestedURL == $nav_data["url"]) {
                $li_classes[] = "active";
                $alreadyFoundActive = true;
            }
            else if(!$alreadyFoundActive && $has_subnav) {
                foreach($subnavs as $subnav) {
                    if($requestedURL == $subnav["url"]) {
                        $li_classes[] = "active";
                        $foundInSubnav = true;
                    }

                    $subPregs = (isset($subnav["pregs"]) ? $subnav["pregs"] : array());
                    $subIncludePregs = (isset($subPregs["include"]) ? $subPregs["include"] : array());
                    $subExcludePregs = (isset($subPregs["exclude"]) ? $subPregs["exclude"] : array());

                    if($subPregs) {
                        $tmpFound = $foundInSubnav;
                        if(!$tmpFound && $subIncludePregs) {
                            foreach($subIncludePregs as $includePreg) {
                                if(substr($includePreg, 0, 1) != "/" && $includePreg == $requestedPath) {
                                    $tmpFound = true;
                                    break;
                                }
                                else if(substr($includePreg, 0, 1) == "/" && preg_match($includePreg, $requestedURL)) {
                                    $tmpFound = true;
                                    break;
                                }
                            }
                        }
                        if($tmpFound && $subExcludePregs) {
                            foreach($subExcludePregs as $excludePreg) {
                                if(substr($excludePreg, 0, 1) != "/" && $excludePreg == $requestedPath) {
                                    $tmpFound = false;
                                    break;
                                }
                                else if(substr($excludePreg, 0, 1) == "/" && preg_match($excludePreg, $requestedURL)) {
                                    $tmpFound = false;
                                    break;
                                }
                            }
                        }
                        $foundInSubnav = $tmpFound;
                        if($foundInSubnav)
                            $li_classes[] = "active";
                    }
                }
            }

            if($pregs) {
                $tmpFound = false;
                if(!$tmpFound && $includePregs) {
                    foreach($includePregs as $includePreg) {
                        if(substr($includePreg, 0, 1) != "/" && $includePreg == $requestedPath) {
                            $tmpFound = true;
                            break;
                        }
                        else if(substr($includePreg, 0, 1) == "/" && preg_match($includePreg, $requestedURL)) {
                            $tmpFound = true;
                            break;
                        }
                    }
                }
                if($tmpFound && $excludePregs) {
                    foreach($excludePregs as $excludePreg) {
                        if(substr($excludePreg, 0, 1) != "/" && $excludePreg == $requestedPath) {
                            $tmpFound = false;
                            break;
                        }
                        else if(substr($excludePreg, 0, 1) == "/" && preg_match($excludePreg, $requestedURL)) {
                            $tmpFound = false;
                            break;
                        }
                    }
                }
                if($tmpFound) {
                    $li_classes[] = "active";
                    $alreadyFoundActive = true;
                }
            }

            if($level > 0 && $has_subnav)
                $li_classes[] = 'dropdown-submenu';

            if(is_array($nav_data["label"]) && isset($nav_data["label"]["class"]))
                $li_classes[] = $nav_data["label"]["class"];

            $output .= '<li class="' . implode(' ', $li_classes) . '">';

            if($has_subnav && $level < 1)
                $output .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="menu-' .$nav_key . '" data-order="' . $nav_data["order"] . '"';
            else
                $output .= '<a href="' . $nav_data["url"] . '" id="menu-' .$nav_key . ($sub ? "-lvl" . $level : '') . '" data-order="' . $nav_data["order"] . '"';

            if(is_array($nav_data["label"]) && isset($nav_data["label"]["title"]))
                $output .= ' title="' . $nav_data["label"]["title"] . '"';

            if(is_array($nav_data["label"]) && isset($nav_data["label"]["append"]))
                $output .= ' ' . $nav_data["label"]["append"];

            $output .= '>';

            if(is_array($nav_data["label"]) && isset($nav_data["label"]["icon"]))
                $output .= '<i class="glyphicon ' . $nav_data["label"]["icon"] . '"></i>&nbsp;';
            else
                $output .= $nav_data["label"];

            if(is_array($nav_data["label"]) && isset($nav_data["label"]["label"]))
                $output .= $nav_data["label"]["label"];

            if($has_subnav && $level < 1)
                $output .= ' <span class="caret"></span>';

            $output .= '</a>';

            if($has_subnav) {
                $output .= '<ul class="dropdown-menu" aria-labelledby="menu-' . $nav_key . '">';
                $output .= $this->renderNavigation($subnavs, true, $level + 1);
                $output .= '</ul>';
            }

            $output .= "</li>";
        }

        if($level < 1) {
            $output .= '
                </ul>';

            if(sizeof($this->_right_items) > 0) {
                $output .= '<ul class="nav navbar-nav navbar-right">' . $this->renderNavigation($this->_right_items, true, 500) . '</ul>';
            }

            $output .= '
            </div>';
        }

        return $output;
    }

    public function sortOrderAsc($a, $b) {
        return $a['order'] > $b['order'];
    }

    public function sortOrderDesc($a, $b) {
        return $b['order'] > $a['order'];
    }
}