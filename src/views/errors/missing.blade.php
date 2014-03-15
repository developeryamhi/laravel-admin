@extends('laravel-admin::layouts.error')

@section('content')
<h1>404 Page not Found</h1>
<div class="well">
    The page you are looking for cannot be found. Perhaps you mistyped the link or the link has been removed.
    <br/><br/>
    Go back to <a href="<?php echo urlRoute("dashboard"); ?>">Admin Panel</a> or <a href="<?php echo urlRoute("home"); ?>">Homepage</a>.
</div>
@stop