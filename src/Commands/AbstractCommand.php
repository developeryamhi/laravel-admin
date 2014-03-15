<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

/**
* Modules console commands
* @author Biraj Pandey <developeryamhi@gmail.com>
*/
abstract class AbstractCommand extends Command {

	/**
	 * List of all available modules
	 * @var array
	 */
	protected $modules;

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * DI
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct();
		$this->app = $app;
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	/**
	 * Reformats the modules list for table display
	 * @return array
	 */
	public function getModules()
	{
		$results = array();

		foreach($this->app['laravel-modules']->modules() as $module)
		{
			$results[] = array(
				'name'    => $module->moduleName(),
                                'version'    => $module->version(),
				'packaged'    => $module->isPackage() ? 'YES' : 'NO',
				'activated' => $module->isEnabled() ? 'YES' : 'NO',
                                'registered' => $module->registeredInSystem() ? 'YES' : 'NO'
			);
		}

		return array_filter($results);
	}


	/**
	 * Return a given module
	 *
	 * @param $module_name
	 * @return mixed
	 */
	public function getModule($module_name)
	{
		return $this->app["laravel-modules"]->module($module_name);
	}

	/**
	 * Display a module info table in the console
	 * @param  array $modules
	 * @return void
	 */
	public function displayModules($modules)
	{
		// Get table helper
		$this->table = $this->getHelperSet()->get('table');

		$headers = array('Module', 'Version', 'Package', 'Activated', 'Registered');

		$this->table->setHeaders($headers)->setRows($modules);

		$this->table->render($this->getOutput());
	}

	/**
	 * Dump autoload classes
	 * @return void
	 */
	public function dumpAutoload()
	{
		// Also run composer dump-autoload
		$this->info('Generating optimized class loader');
		$this->app["laravel-modules"]->doDumpAutoload();
		$this->line('');
	}
}
