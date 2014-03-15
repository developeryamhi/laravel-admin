<div class="page-head">
    <h1 class="col-md-6"><?php echo trans("auth-module::label.manage_users"); ?></h1>
    <div class="page-head-actions col-md-6">
        <a href="<?php echo URL::route("create_user"); ?>" class="btn btn-sm btn-primary">
            <?php echo trans("auth-module::label.btn_add_user"); ?>
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
                <th class="hidden-xs"><?php echo trans("auth-module::label.group"); ?></th>
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
                <td class="hidden-xs"><?php echo $item->group()->group_description; ?></td>
                <?php app("events")->fire("module.auth.table.row", array($item)); ?>
                <td class="hidden-xs"><?php echo $item->enabledText(); ?></td>
                <td>
                    <a href="<?php echo URL::route("edit_user", array($item->id)); ?>" title="<?php echo trans("auth-module::label.action_edit"); ?>"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="<?php echo URL::route("delete_user", array($item->id)); ?>" title="<?php echo trans("auth-module::label.action_delete"); ?>" onclick="return confirm('<?php echo trans("auth-module::label.action_delete_confirm"); ?>');"><i class="glyphicon glyphicon-remove"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php echo $items->links(); ?>
</div>