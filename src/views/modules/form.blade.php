{{ Form::open(array('route' => 'add_module_process', 'files' => true)) }}
    <h1 class="form-title"><?php echo trans("laravel-admin::label.title_add_module"); ?></h1>

    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                {{ Form::label('logo', trans("laravel-admin::label.module_file")) }}
                <div class="input-group">
                    <input type="text" class="form-control selected-file" readonly="" />
                    <span class="input-group-btn">
                        <span class="btn btn-primary btn-file">
                            Browse {{ Form::file('module_file', array('class' => 'form-control', 'accept' => 'application/zip')) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    {{ Form::submit(trans("laravel-admin::label.btn_import_module"), array('class' => 'btn btn-primary')) }}
    {{ HTML::linkRoute("modules", trans("laravel-admin::label.action_cancel"), null, array("class" => "btn btn-link")) }}
{{ Form::close() }}