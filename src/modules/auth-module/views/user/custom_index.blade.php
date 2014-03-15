<div class="page-head">
    <h1 class="col-md-6"><?php echo $group->getLang("list_page_title");?></h1>
    <div class="page-head-actions col-md-6">
        <a href="<?php echo URL::route($group->getRoute("create")); ?>" class="btn btn-sm btn-primary">
            <?php echo $group->getLang("btn_add_new");?>
        </a>
    </div>
    <div class="clearfix"></div>
</div>

<div>
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th><?php echo trans("auth-module::label.id"); ?></th>
                <th class="hidden-sm hidden-xs"><?php echo trans("auth-module::label.full_name"); ?></th>
                <th><?php echo trans("auth-module::label.username"); ?></th>
                <th><?php echo trans("auth-module::label.email"); ?></th>
                <?php app("events")->fire("module.auth.{$group->group_name}.table.head"); ?>
                <?php app("events")->fire("module.auth.table.head"); ?>
                <th class="hidden-xs"><?php echo trans("auth-module::label.status"); ?></th>
                <th><?php echo trans("auth-module::label.user_actions"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item) { ?>
            <tr>
                <td><?php echo $item->id; ?></td>
                <td class="hidden-sm hidden-xs"><?php echo $item->full_name; ?></td>
                <td><?php echo $item->username; ?></td>
                <td><?php echo $item->email; ?></td>
                <?php app("events")->fire("module.auth.{$group->group_name}.table.row", array($item)); ?>
                <?php app("events")->fire("module.auth.table.row", array($item)); ?>
                <td class="hidden-xs"><?php echo $item->enabledText(); ?></td>
                <td>
                    <a href="<?php echo URL::route($group->getRoute("edit"), array($item->id)); ?>" title="<?php echo $group->getLang("action_edit"); ?>"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="<?php echo URL::route($group->getRoute("delete"), array($item->id)); ?>" title="<?php echo $group->getLang("action_delete"); ?>" onclick="return confirm('<?php echo $group->getLang("action_delete_confirm"); ?>');"><i class="glyphicon glyphicon-remove"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php echo $items->links(); ?>
</div>