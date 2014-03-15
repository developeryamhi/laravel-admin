<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesSeedCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:seed';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Seed the database from modules.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		$this->info('Seeding database from modules');

		// Get all modules or 1 specific
		if ($moduleName = $this->input->getArgument('module')) $modules = array(app('laravel-modules')->module($moduleName));
		else                                                   $modules = app('laravel-modules')->modules();

		foreach ($modules as $module)
		{
			if ($module)
			{
				if ($module->def('seeder'))
				{
					$module->seed();
					$this->info("Seeded '" . $module->name() . "' module.");
				}
				else
				{
					$this->line("Module <info>'" . $module->name() . "'</info> has no seeds.");
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
			array('module', InputArgument::OPTIONAL, 'The name of module being seeded.'),
		);
	}

}
