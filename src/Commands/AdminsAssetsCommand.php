<?php namespace Developeryamhi\LaravelAdmin\Commands;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class AdminAssetsCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'admin:publish_assets';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Publish Assets for Admin Panel.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		$this->comment('Publishing Assets for Admin...' . PHP_EOL);

                //  Assets Sub Folder
                $assets_folder = app("laravel-admin")->getConfig("assets_folder");
                if($assets_folder)  $assets_folder .= "/";

                //  Check Assets Dir Exists
                if(!$this->app["files"]->exists(assetsPath($assets_folder))) {

                    //  Create Assets DIr
                    $this->app["files"]->makeDirectory(assetsPath($assets_folder), 0755, true);
                }

                //  Check Images Dir Exists
                if(!$this->app["files"]->exists(imagePath($assets_folder))) {

                    //  Create Images DIr
                    $this->app["files"]->makeDirectory(imagePath($assets_folder));
                }

                //  Check CSS Dir Exists
                if(!$this->app["files"]->exists(cssPath($assets_folder))) {

                    //  Create CSS DIr
                    $this->app["files"]->makeDirectory(cssPath($assets_folder));
                }

                //  Check JS Dir Exists
                if(!$this->app["files"]->exists(jsPath($assets_folder))) {

                    //  Create Images DIr
                    $this->app["files"]->makeDirectory(jsPath($assets_folder));
                }

                //  Copy All the assets
                $this->app["files"]->copyDirectory(dirname(dirname(__FILE__)) . '/base_assets/assets', assetsPath());

                $this->info('Styles, Scripts and Images for Admin has been successfully published');
	}
}
