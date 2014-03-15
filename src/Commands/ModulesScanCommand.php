<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputOption;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesScanCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:scan';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Scan modules and cache module meta data.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{

		//	Sync
		$sync = (($this->option("sync") == "true" || $this->option("sync") == "1") ? true : false);

		//	Sync List
		$sync_list = ((!is_null($this->option("sync_list")) && !empty($this->option("sync_list"))) ? explode(",", $this->option("sync_list")) : null);

		//	Print
		$this->info('Scanning modules');

		// Get table helper
		$this->table = $this->getHelperSet()->get('table');

		//  Run the scanner
		$this->app['laravel-modules']->scanModules($sync, $sync_list);

		//  Get Modules
		$this->modules = $this->app['laravel-modules']->modules();

		// Return error if no modules found
		if (count($this->modules) == 0)
		{
			return $this->error("Your application doesn't have any valid modules.");
		}

		// Also run composer dump-autoload
		$this->dumpAutoload();

		// Display number of found modules
		$this->info('Found ' . count($this->modules) . ' modules:');

		// Display the modules info
		$this->displayModules($this->getModules());
	}

	/**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('sync', null, InputOption::VALUE_OPTIONAL, 'Sync Modules List to Database', null),
            array('sync_list', null, InputOption::VALUE_OPTIONAL, 'Modules List to Sync To Database', null),
        );
    }
}
