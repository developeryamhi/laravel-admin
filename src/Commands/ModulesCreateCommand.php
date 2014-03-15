<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesCreateCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:create';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Create a new module.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		// Name of new module
		$moduleName = $this->input->getArgument('module');
		$this->info('Creating module "'.$moduleName.'"');

		// Chech if module exists
		$exists = app('laravel-modules')->module($moduleName);

		if ( ! app('laravel-modules')->module($moduleName))
		{
			//	Details
			$name = ($this->option("name") ? $this->option("name") : $moduleName);
			$description = ($this->option("description") ? $this->option("description") : 'Description for Module');
			$version = ($this->option("ver") ? $this->option("ver") : "1.0");
			$order = ($this->option("order") ? $this->option("order") : "auto");

			// Get path to modules
			$modulePath = $this->app['config']->get('laravel-modules::path');
			if (is_array($modulePath)) $modulePath = $modulePath[0];
			$usePath = $modulePath . '/' . $moduleName;

			// Create the directory
			if ( ! $this->app['files']->exists($usePath))
			{
				$this->app['files']->makeDirectory($usePath, 0755);
			}

			// Create definition and write to file
			$definition = $this->app['laravel-modules']->prettyJsonEncode(array(
				'name' => $name,
				'description' => $description,
				'version' => $version,
				'order' => $order
			));
			$this->app['files']->put($usePath . '/' . $this->app['config']->get('laravel-modules::meta_file'), $definition);

			// Create routes and write to file
			$routes = '<?php' . PHP_EOL;
			$this->app['files']->put($usePath . '/routes.php', $routes);

			// Create some resource directories
			$this->app['files']->makeDirectory($usePath . '/assets', 0755);
			$this->app['files']->makeDirectory($usePath . '/config', 0755);
			$this->app['files']->makeDirectory($usePath . '/controllers', 0755);
			$this->app['files']->makeDirectory($usePath . '/lang', 0755);
			$this->app['files']->makeDirectory($usePath . '/models', 0755);
			$this->app['files']->makeDirectory($usePath . '/migrations', 0755);
			$this->app['files']->makeDirectory($usePath . '/views', 0755);

			//	Create the Module Object
			$newModule = new \Developeryamhi\LaravelAdmin\Module($this->app, $moduleName, app()->make("path.base") . "/" . $usePath, $modulePath);

			//	Sync the Data to Databse
			$newModule->syncToDatabase();

			// Autoload classes
			$this->dumpAutoload();
		}
		else
		{
			$this->error('Module with name "' . $moduleName . '" already exists.');
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module', InputArgument::REQUIRED, 'The name of module being created.'),
		);
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('name', null, InputOption::VALUE_OPTIONAL, 'Module Label', null),
			array('description', null, InputOption::VALUE_OPTIONAL, 'Module Description', null),
			array('ver', null, InputOption::VALUE_OPTIONAL, 'Module Version', null),
			array('order', null, InputOption::VALUE_OPTIONAL, 'Module Order Index', null),
		);
	}

}
