<?php app("events")->fire("admin.dashboard.pre"); ?>

<div id="dashboard-widgets">
    <?php

    //  Get Registered Dashboard Widgets
    $widgets = app("events")->fire("admin.dashboard.widgets");

    //  Loop Each Widgets
    foreach($widgets as $widget) {

        //  Check for Full Inner Width Request
        $full_inner_width = (isset($widget["full_inner_width"]) && (bool)$widget["full_inner_width"] == true ? true : false);

        //  Get Widget State
        $widget_state = (isset($widget["widget_state"]) ? 'panel-' . $widget["widget_state"] : '');

        //  Col Class
        $column_class = (isset($widget["columns"]) && $widget["columns"] < 4 ? 'col-md-' . ($widget["columns"] * 4) : 'col-md-4');
    ?>
    <div class="<?php echo $column_class; ?> dashboard-widget-holder">
        <div class="panel panel-default <?php echo $widget_state; ?> dashboard-widget-item" id="<?php $widget["id"]; ?>">
            <div class="panel-heading">
                <?php echo $widget["title"]; ?>
                <?php if(isset($widget["actions"]) && sizeof($widget["actions"]) > 0) { ?>
                <div class="panel-actions pull-right">
                    <?php foreach($widget["actions"] as $action) { ?>
                    <a href="<?php echo (isset($action["link"]) ? $action["link"] : 'javascript:void(0);'); ?>" title="<?php echo @$action["title"]; ?>" id="<?php echo @$action["id"]; ?>" class="widget-action-item <?php echo @$action["class"]; ?>"><i class="glyphicon <?php echo $action["icon"]; ?>"></i></a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <div class="panel-body <?php echo ($full_inner_width ? 'no-padding' : ''); ?>"><?php echo call_user_func_array($widget["callback"], array()); ?></div>
            <?php if(isset($widget["footer"])) { ?>
            <div class="panel-footer"><?php echo $widget["footer"]; ?></div>
            <?php } ?>
        </div>
    </div>
    <?php
    }
    ?>
    <div class="clearfix"></div>
</div>

<?php app("events")->fire("admin.dashboard.post"); ?>