@extends('laravel-admin::layouts.error')

@section('content')
<h1>Application Error</h1>
<div class="well">
    <blockquote style="border-color:red;background:#ffdbdb;">
        <strong><?php echo $exception->getMessage(); ?></strong> on line <strong><?php echo $exception->getLine(); ?></strong> at file <strong><?php echo $exception->getFile(); ?></strong>.
    </blockquote>
    <?php echo '<blockquote style="border-color:lightblue;">' . implode('</blockquote><blockquote style="border-color:lightblue;">', explode(PHP_EOL, $exception->getTraceAsString())) . "</blockquote>"; ?>
</div>
@stop