<?php namespace Developeryamhi\LaravelAdmin\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 * @author Biraj Pandey <developeryamhi@gmail.com>
 */
class ModulesExecuteCommand extends AbstractCommand {

    /**
     * Name of the command
     * @var string
     */
    protected $name = 'modules:execute';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Process actions on specific module';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire() {

        //	Get Module to Process
        $moduleName = $this->input->getArgument("module");

        //	Get Action to Run
        $action = $this->input->getArgument("action");

        //	Force Action
        $force = (($this->option("force") == "true" || $this->option("force") == "1") ? true : false);

        //	Get Module Data
        $moduleFirst = $this->getModule($moduleName);

        //	Check Module Not Found
        if($moduleName && !$moduleFirst) {

            //	Return Error
            return $this->error("Module [{$moduleName}] is not registered to the system or not available. Please check for the existence");
        }

        //  Modules
        $modules = array();
        if(!$moduleName)
            $modules = app("laravel-modules")->modules();
        else
            $modules = array($moduleFirst);

        //	Return Message
        $returnInfo = true;
        $returnMessages = array();

        //  Loop Each
        foreach($modules as $module) {

            //	Switch Action
            switch (strtolower($action)) {

                //	Activate
                case 'activate':

                    //	Try the Activation
                    $response = $module->activate($force);

                    //	Check True
                    if ($response === TRUE) {

                        //	Set Message
                        $returnMessages[] = "Module [{$module->name()}] successfully activated";
                    } else {

                        //	Set Error
                        $returnInfo = false;

                        //	Messages
                        $messages = \Developeryamhi\LaravelAdmin\ModuleItem::activationResponseTerminal($module, $response);

                        //	Set Message
                        $returnMessages[] = implode(PHP_EOL, $messages);
                    }

                    break;

                //	Deactivate
                case 'deactivate':

                    //	Try the Deactivation
                    if($module->deactivate()) {

                        //	Set Message
                        $returnMessages[] = "Module [{$module->name()}] successfully deactivated.";
                    } else {

                        //  Set Return Info
                        $returnInfo = false;

                        //	Set Message
                        $returnMessages[] = "Module [{$module->name()}] deactivation failed. Module must be locked.";
                    }

                    break;

                //	Delete
                case 'delete':

                    //	Try the Delete
                    if($module->delete()) {

                        //	Set Message
                        $returnMessages[] = "Module [{$module->name()}] successfully deleted from the system.";
                    } else {

                        //  Set Return Info
                        $returnInfo = false;

                        //	Set Message
                        $returnMessages[] = "Module [{$module->name()}] deletion failed. Module must be locked or enabled.";
                    }

                    break;

                //	Sync
                case 'sync':

                    //	Try the Sync
                    $module->syncFromMeta();

                    //	Set Message
                    $returnMessages[] = "Module [{$module->name()}] info successfully synced to the system";
                    break;

                //	Default
                default:
                    return $this->info("Invalid Action [{$action}] requested for module [{$module->name()}].");
                    break;
            }
        }

        //  Check Modules
        if(sizeof($modules) > 0) {

            // Autoload classes
            $this->dumpAutoload();
        } else {

            //	Set Message
            $returnMessages[] = "No modules available in the system to process";
        }

        //	Check Info Message
        if ($returnMessages) {
            if ($returnInfo)
                return $this->info(implode(PHP_EOL, $returnMessages));
            else
                return $this->error(implode(PHP_EOL, $returnMessages));
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments() {
        return array(
            array('action', InputArgument::REQUIRED, 'Action to process on the module'),
            array('module', InputArgument::OPTIONAL, 'The name of module.')
        );
    }

    /**
     * Get the console options.
     * @return array
     */
    protected function getOptions() {
        return array(
            array('force', null, InputOption::VALUE_OPTIONAL, 'Force the command incase of dependencies', null)
        );
    }

}
