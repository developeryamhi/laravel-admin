{{ Form::model($item, array('route' => ($newItem ? 'save_permission' : array('update_permission', $item->id)))) }}
    <h1 class="form-title"><?php echo trans("auth-module::label.title_" . ($newItem ? "create_permission" : "update_permission")); ?></h1>

    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                {{ Form::label('permission_key', trans("auth-module::label.permission")) }}
                {{ Form::text('permission_key', Input::old('permission_key'), array('placeholder' => 'e.g. manage_xxx', 'class' => 'alphaDash form-control required unique')) }}
                {{ Form::hidden('permission_id', $item->id) }}
            </div>
            <div class="form-group">
                {{ Form::label('permission_description', trans("auth-module::label.group_desc")) }}
                {{ Form::text('permission_description', Input::old('permission_description'), array('placeholder' => 'e.g. Manage XXX Permission', 'class' => 'form-control')) }}
            </div>
        </div>
    </div>
    <div class="form-actions form-actions-simple">
        {{ Form::submit(trans("auth-module::label.btn_save_permission"), array('class' => 'btn btn-primary')) }}
        {{ HTML::linkRoute("permissions", trans("auth-module::label.action_cancel"), null, array("class" => "btn btn-link")) }}
    </div>
{{ Form::close() }}