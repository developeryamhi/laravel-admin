<div class="page-head">
    <h1 class="col-md-6"><?php echo trans("laravel-admin::label.manage_modules"); ?></h1>
    <div class="page-head-actions col-md-6">
        <a href="<?php echo URL::route("add_module"); ?>" class="btn btn-sm btn-primary">
            <?php echo trans("laravel-admin::label.btn_add_module"); ?>
        </a>
        <a href="<?php echo URL::route("sync_modules"); ?>" class="btn btn-sm btn-danger">
            <i class="glyphicon glyphicon-refresh"></i>&nbsp;
            <?php echo trans("laravel-admin::label.btn_sync_modules"); ?>
        </a>
        <a href="<?php echo URL::route("scan_modules"); ?>" class="btn btn-sm btn-danger">
            <i class="glyphicon glyphicon-search"></i>&nbsp;
            <?php echo trans("laravel-admin::label.btn_scan_modules"); ?>
        </a>
    </div>
    <div class="clearfix"></div>
</div>

<p><?php echo trans("laravel-admin::message.total_modules_available", array("total" => $items->getTotal())); ?></p>

<div>
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th><?php echo trans("laravel-admin::label.name"); ?></th>
                <th class="hidden-xs"><?php echo trans("laravel-admin::label.namespace"); ?></th>
                <th><?php echo trans("laravel-admin::label.version"); ?></th>
                <th class="hidden-xs hidden-sm"><?php echo trans("laravel-admin::label.dependencies"); ?></th>
                <th><?php echo trans("laravel-admin::label.enabled"); ?></th>
                <th class="hidden-xs"><?php echo trans("laravel-admin::label.locked"); ?></th>
                <th class="hidden-xs"><?php echo trans("laravel-admin::label.order"); ?></th>
                <th><?php echo trans("laravel-admin::label.module_actions"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item) { ?>
            <tr>
                <td><span title="<?php echo $item->description; ?>"><?php echo $item->name; ?></span></td>
                <td class="hidden-xs"><?php echo $item->module; ?></td>
                <td><?php echo $item->version; ?></td>
                <td class="hidden-xs hidden-sm"><?php echo ($item->dependencyTexts() ? implode("<br/>", $item->dependencyTexts()) : '-'); ?></td>
                <td><?php echo $item->enabledText(); ?></td>
                <td class="hidden-xs"><?php echo $item->lockedText(); ?></td>
                <td class="hidden-xs"><?php echo $item->order_index; ?></td>
                <td>
                    <?php if(!$item->isActivated()) { ?>
                    <?php if($item->hasDependencies()) { ?>
                    <a href="<?php echo URL::route("force_activate_module", array($item->id)); ?>" title="<?php echo trans("laravel-admin::label.action_activate_force"); ?>" onclick="return confirm('<?php echo trans("laravel-admin::label.action_activate_module_forced_confirm"); ?>');"><i class="glyphicon glyphicon-ok"></i></a>&nbsp;&nbsp;
                    <?php } ?>
                    <a href="<?php echo URL::route("activate_module", array($item->id)); ?>" title="<?php echo trans("laravel-admin::label.action_activate"); ?>" onclick="return confirm('<?php echo trans("laravel-admin::label.action_activate_module_confirm"); ?>');"><i class="glyphicon glyphicon-ok-circle"></i></a>
                    <a href="<?php echo URL::route("delete_module", array($item->id)); ?>" title="<?php echo trans("laravel-admin::label.action_delete"); ?>" onclick="return confirm('<?php echo trans("laravel-admin::label.action_delete_module_confirm"); ?>');"><i class="glyphicon glyphicon-remove"></i></a>
                    <?php } else { ?>
                    <?php if(!$item->isLocked()) { ?>
                    <a href="<?php echo URL::route("deactivate_module", array($item->id)); ?>" title="<?php echo trans("laravel-admin::label.action_deactivate"); ?>" onclick="return confirm('<?php echo trans("laravel-admin::label.action_deactivate_module_confirm"); ?>');"><i class="glyphicon glyphicon-ban-circle"></i></a>
                    <?php } else { echo "-"; }?>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php echo $items->links(); ?>
</div>