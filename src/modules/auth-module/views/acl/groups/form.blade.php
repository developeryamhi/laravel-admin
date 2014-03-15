{{ Form::model($item, array('route' => ($newItem ? 'save_group' : array('update_group', $item->id)))) }}
    <h1 class="form-title"><?php echo trans("auth-module::label.title_" . ($newItem ? "create_group" : "update_group")); ?></h1>

    <div class="panel">
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-group-info" data-toggle="tab"><?php echo trans("auth-module::label.tab_group_info"); ?>&nbsp;&nbsp;<span class="badge btn-danger"></span></a></li>
                <li><a href="#tab-group-permissions" data-toggle="tab"><?php echo trans("auth-module::label.tab_group_permissions"); ?>&nbsp;&nbsp;<span class="badge btn-danger"></span></a></li>
            </ul>
            <div id="user-tabs" class="tab-content">
                <div class="tab-pane fade in active" id="tab-group-info">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                {{ Form::label('group_name', trans("auth-module::label.group")) }}
                                {{ Form::text('group_name', Input::old('group_name'), array('placeholder' => 'e.g. admin', 'class' => 'alphaDash form-control required unique')) }}
                                {{ Form::hidden('group_id', $item->id) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('group_description', trans("auth-module::label.group_desc")) }}
                                {{ Form::text('group_description', Input::old('group_description'), array('placeholder' => 'e.g. Administrator', 'class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in" id="tab-group-permissions">
                    <div class="row">
                        <?php

                            //  Get Assigned Permissions Ids
                            $assignedPermissionsIds = $item->permissionIds();

                            //  Get All Available Permissions
                            $availablePermissions = Developeryamhi\AuthModule\PermissionItem::get();

                            //  Loop Each Available Permissions
                            foreach($availablePermissions as $permission) {
                        ?>
                        <div class="col-md-3">
                            <label title="<?php echo $permission->permission_description; ?>" class="inline-choice-label">
                                {{ Form::checkbox("permissions[]", $permission->id, in_array($permission->id, $assignedPermissionsIds), ($permission->isForDashboard() ? array("disabled", "disabled") : array())) }}
                                <?php echo $permission->permission_key; ?>
                            </label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        {{ Form::submit(trans("auth-module::label.btn_save_group"), array('class' => 'btn btn-primary')) }}
        {{ HTML::linkRoute("groups", trans("auth-module::label.action_cancel"), null, array("class" => "btn btn-link")) }}
    </div>
{{ Form::close() }}