{{ Form::model($item, array('route' => ($newItem ? 'save_user' : array('update_user', $item->id)), 'files' => true)) }}
    <h1 class="form-title"><?php echo trans("auth-module::label.title_" . ($newItem ? "create_user" : "update_user")); ?></h1>

    <div class="panel">
        <div class="panel-body">
            <ul id="employee-nav-tabs" class="nav nav-tabs">
                <li class="active"><a href="#tab-user-info" data-toggle="tab"><?php echo trans("auth-module::label.tab_user_info"); ?>&nbsp;&nbsp;<span class="badge btn-danger"></span></a></li>
                <?php app("events")->fire("module.auth.form.tabs.titles", array($item)); ?>
            </ul>
            <div id="user-tabs" class="tab-content">
                <div class="tab-pane fade in active" id="tab-user-info">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                {{ Form::label('group_id', trans("auth-module::label.group")) }}
                                {{ Form::select('group_id', \Developeryamhi\AuthModule\GroupItem::hasNoInterface()->isNotHidden()->lists('group_description', 'id'), Input::old('group_id', $item->group_id), array('class' => 'required form-control')) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('full_name', trans("auth-module::label.full_name")) }}
                                {{ Form::text('full_name', Input::old('full_name'), array('placeholder' => 'e.g. John F. Rehn', 'class' => 'alpha form-control required')) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('nicename', trans("auth-module::label.nicename")) }}
                                {{ Form::text('nicename', Input::old('nicename'), array('placeholder' => 'e.g. John', 'class' => 'form-control')) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('email', trans("auth-module::label.email")) }}
                                {{ Form::text('email', Input::old('email'), array('placeholder' => 'e.g. jhon@example.com', 'class' => 'form-control email required unique')) }}
                                {{ Form::hidden('user_id', $item->id) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('username', trans("auth-module::label.username")) }}
                                {{ Form::text('username', Input::old('username'), array('placeholder' => 'e.g. john.rehn', 'class' => 'form-control required unique')) }}
                                {{ Form::hidden('user_id', $item->id) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('password', trans("auth-module::label.password")) }}
                                {{ Form::password('password', array('placeholder' => 'e.g. ******', 'class' => 'form-control' . ($newItem ? ' required' : ''))) }}
                                <?php if(!$newItem) { ?>
                                <span class="help-block">Not Required if you do not want to change password</span>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                {{ Form::label('password2', trans("auth-module::label.confirm_password")) }}
                                {{ Form::password('password2', array('placeholder' => 'e.g. ******', 'equalTo' => '#password', 'class' => 'form-control' . ($newItem ? ' required' : ''))) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('enabled', trans("auth-module::label.enabled")) }}<br/>
                                {{ Form::hidden('enabled', 0) }}
                                {{ Form::checkbox('enabled', 1, Input::old('enabled') == 1, array('class' => 'nice_switch')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <?php app("events")->fire("module.auth.form.tabs.panes", array($item)); ?>
            </div>
        </div>
    </div>
    <div class="form-actions">
        {{ Form::submit(trans("auth-module::label.btn_save_user"), array('class' => 'btn btn-primary')) }}
        {{ HTML::linkRoute("users", trans("auth-module::label.action_cancel"), null, array("class" => "btn btn-link")) }}
    </div>
{{ Form::close() }}