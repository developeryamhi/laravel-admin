{{ Form::model($item, array('route' =>'update_my_account', 'files' => true)) }}
    <h1 class="form-title"><?php echo trans("auth-module::label.title_update_account"); ?></h1>

    <div class="panel">
        <div class="panel-body">
            <ul id="employee-nav-tabs" class="nav nav-tabs">
                <li class="active"><a href="#tab-user-info" data-toggle="tab"><?php echo trans("auth-module::label.tab_user_info"); ?>&nbsp;&nbsp;<span class="badge btn-danger"></span></a></li>
                <?php app("events")->fire("module.auth.form.tabs.titles", array($item)); ?>
                <?php app("events")->fire("module.auth.account.form.tabs.titles", array($item)); ?>
            </ul>
            <div id="user-tabs" class="tab-content">
                <div class="tab-pane fade in active" id="tab-user-info">
                    <div class="row">
                        <div class="col-md-5">
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
                                {{ Form::password('password', array('placeholder' => 'e.g. ******', 'class' => 'form-control')) }}
                                <span class="help-block">Not Required if you do not want to change password</span>
                            </div>
                            <div class="form-group">
                                {{ Form::label('password2', trans("auth-module::label.confirm_password")) }}
                                {{ Form::password('password2', array('placeholder' => 'e.g. ******', 'equalTo' => '#password', 'class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <?php app("events")->fire("module.auth.form.tabs.panes", array($item)); ?>
                <?php app("events")->fire("module.auth.account.form.tabs.panes", array($item)); ?>
            </div>
        </div>
    </div>
    <div class="form-actions">
        {{ Form::submit(trans("auth-module::label.btn_update_account"), array('class' => 'btn btn-primary')) }}
    </div>
{{ Form::close() }}