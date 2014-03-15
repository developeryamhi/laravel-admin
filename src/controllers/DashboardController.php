<?php namespace Developeryamhi\LaravelAdmin;

class DashboardController extends Base\AdminController {

    protected $namespaceName = "laravel-admin";

    protected $controllerName = "dashboard";

    protected function init() {
        $this->layoutName = adminLayout();
    }

    public function index() {
        return $this->_render('dashboard');
    }
}
