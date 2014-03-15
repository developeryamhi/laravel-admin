<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesPublishCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:publish';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Publish public assets for modules.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		$this->info('Publishing module assets');

		// Get all modules or 1 specific
		if ($moduleName = $this->input->getArgument('module')) $modules = array(app('laravel-modules')->module($moduleName));
		else                                                   $modules = app('laravel-modules')->modules();

		foreach ($modules as $module)
		{
			if ($module)
			{
				if ($this->app['files']->exists($module->modulePath('assets')))
				{

					// Prepare params
					$path = $module->modulePath("assets");

					// Get destination path
					$destination = app()->make('path.public') . '/packages/module/' . $module->name() . '/assets';

					// Try to copy
					$success = $this->app['files']->copyDirectory($path, $destination);

					// Result
					if ( ! $success) $this->line("Unable to publish assets for module '" . $module->name() . "'");
					else             $this->info("Published assets for module '" . $module->name() . "'");
				}
				else
				{
					$this->line("Module <info>'" . $module->name() . "'</info> has no assets available.");
				}
			}
			else
			{
				$this->error("Module '" . $moduleName . "' does not exist.");
			}
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module', InputArgument::OPTIONAL, 'The name of module being published.'),
		);
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
