<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesGenerateCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:generate';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Generate module resources.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		// Name of new module
		$module     = $this->getModule($this->input->getArgument('module'));
		$modulePath = $module->modulePath() . "/";
		$type       = $this->input->getArgument('type');
		$resource   = $this->input->getArgument('resource');

		// Generate a controller
		if ($type == 'controller')
		{
			$dirPath = $modulePath . '/controllers';
			$this->call('generate:controller', array('name' => $resource, '--path' => $dirPath));
		}

		// Generate a model
		if ($type == 'model')
		{
			$dirPath = $modulePath . '/models';
			$this->call('generate:model', array('name' => $resource, '--path' => $dirPath));
		}

		// Generate a migration
		if ($type == 'migration')
		{
			$dirPath = $modulePath . '/migrations';
			$this->call('generate:migration', array('name' => $resource, '--path' => $dirPath));
		}

		// Generate a view
		if ($type == 'view')
		{
			$dirPath = $modulePath . '/views';
			$this->call('generate:view', array('name' => $resource, '--path' => $dirPath));
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module',   InputArgument::REQUIRED, 'The name of module.'),
			array('type',     InputArgument::REQUIRED, 'Type of resource you want to generate.'),
			array('resource', InputArgument::REQUIRED, 'Name of resource.'),
		);
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('path', null, InputOption::VALUE_OPTIONAL, 'Path to the directory.'),
			array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.')
		);
	}

}
