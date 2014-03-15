{{ Form::open(array('route' => \Developeryamhi\AuthModule\UserItem::loginRouteArray(true))) }}
    <h1><?php echo trans("auth-module::label.title_login"); ?></h1>
    <p>
        {{ Form::label('username', trans("auth-module::label.username")) }}
        {{ Form::text('username', Input::old('username'), array('placeholder' => 'username', 'class' => 'form-control', 'autofocus' => 'autofocus')) }}
        {{ $errors->first('username', '<p class="input-attachment-msg alert-danger">:message</p>') }}
    </p>
    <p>
        {{ Form::label('password', trans("auth-module::label.password")) }}
        {{ Form::password('password', array('placeholder' => 'password', 'class' => 'form-control')) }}
        {{ $errors->first('password', '<p class="input-attachment-msg alert-danger">:message</p>') }}
    </p>
    <p>{{ Form::submit(trans("auth-module::label.btn_login"), array('class' => 'btn btn-primary')) }}</p>
{{ Form::close() }}