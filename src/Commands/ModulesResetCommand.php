<?php namespace Developeryamhi\LaravelAdmin\Commands;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesResetCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:reset';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Resets Modules Tables and Version Informations';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{

		//	Reset Everything
		app("laravel-modules")->resetEverything();

		return $this->info("Modules informations has been successfully resetted");
	}

}
