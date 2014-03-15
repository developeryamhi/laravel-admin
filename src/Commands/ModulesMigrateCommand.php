<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesMigrateCommand extends AbstractCommand {

    /**
     * Name of the command
     * @var string
     */
    protected $name = 'modules:migrate';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Run migrations for modules.';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire() {

        //	Set Info
        $this->info('Migrating modules');

        // Get all modules or 1 specific
        $modules = array();
        if ($moduleName = $this->input->getArgument('module'))
            $modules = array(app('laravel-modules')->module($moduleName));
        else
            $modules = app('laravel-modules')->modules();

        foreach ($modules as $module) {
            if ($module) {
                if ($this->app['files']->exists($module->modulePath('migrations'))) {

                    // Prepare params
                    $path = $module->moduleFolder("migrations");

                    // Run command
                    $this->call('migrate', array('--path' => $path));

                    // Run seeder if needed
                    if ($this->input->getOption('seed') and $module->def('seeder')) {
                        $module->seed();
                        $this->info("Seeded '" . $module->name() . "' module.");
                    }
                } else {
                    $this->line("Module <info>'" . $module->name() . "'</info> has no migrations.");
                }
            } else {
                $this->error("Module '" . $moduleName . "' does not exist.");
            }
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments() {
        return array(
            array('module', InputArgument::OPTIONAL, 'The name of module being migrated.'),
        );
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions() {
        return array(
            array('seed', null, InputOption::VALUE_NONE, 'Indicates if the module should seed the database.')
        );
    }

}
