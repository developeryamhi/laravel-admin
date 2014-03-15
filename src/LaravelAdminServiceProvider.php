<?php namespace Developeryamhi\LaravelAdmin;

use Illuminate\Support\ServiceProvider;

class LaravelAdminServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        //  Register Package
        $this->package('developeryamhi/laravel-admin', 'laravel-admin', __DIR__);

        // Register commands
        $this->bootCommands();

        try {

            //  Run Start
            $this->app["laravel-admin"]->start();

            //  Start Laravel Modules
            $this->app['laravel-admin']->register();
        } catch (\Exception $e) {

            //  Add Error Log
            $this->app['laravel-admin']->logException($e, "There was an error when starting modules:");
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        // Register IoC bindings
        $this->app->bindShared('laravel-admin', function($app) {

            //  Set Admin Loaded
            $app["admin.loaded"] = true;

            //  Create Admin Instance
            $admin = new LaravelAdmin($app);

            //  Return Instance
            return $admin;
        });

        //  Init the Assets Manager
        $this->app["assets_manager"] = new AssetsManager($this->app);

        //  Load Every Helpers
        require_once dirname(__FILE__) . '/helpers/base.php';
        require_once dirname(__FILE__) . '/helpers/theme_manager.php';
        require_once dirname(__FILE__) . '/helpers/admin.php';
        require_once dirname(__FILE__) . '/helpers/module.php';
    }

    /**
     * Register all available commands
     * @return void
     */
    public function bootCommands() {

        // Add modules command
        $this->app->bindShared('modules.list', function($app) {
            return new Commands\ModulesCommand($app);
        });

        // Add scan command
        $this->app->bindShared('modules.scan', function($app) {
            return new Commands\ModulesScanCommand($app);
        });

        // Add publish command
        $this->app->bindShared('modules.publish', function($app) {
            return new Commands\ModulesPublishCommand($app);
        });

        // Add migrate command
        $this->app->bindShared('modules.migrate', function($app) {
            return new Commands\ModulesMigrateCommand($app);
        });

        // Add seed command
        $this->app->bindShared('modules.seed', function($app) {
            return new Commands\ModulesSeedCommand($app);
        });

        // Add create command
        $this->app->bindShared('modules.create', function($app) {
            return new Commands\ModulesCreateCommand($app);
        });

        // Add generate command
        $this->app->bindShared('modules.generate', function($app) {
            return new Commands\ModulesGenerateCommand($app);
        });

        // Add reset command
        $this->app->bindShared('modules.reset', function($app) {
            return new Commands\ModulesResetCommand($app);
        });

        // Add execute command
        $this->app->bindShared('modules.execute', function($app) {
            return new Commands\ModulesExecuteCommand($app);
        });

        // Add publish assets command
        $this->app->bindShared('admin.publish_assets', function($app) {
            return new Commands\AdminAssetsCommand($app);
        });

        // Now register all the commands
        $this->commands(array(
            'modules.list', 'modules.scan', 'modules.publish',
            'modules.migrate', 'modules.seed', 'modules.create',
            'modules.generate', 'modules.reset', 'modules.execute',
            'admin.publish_assets'
        ));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array('laravel-admin', 'laravel-modules', 'modules', 'admin.publish_assets');
    }
}
