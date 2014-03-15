<?php

//  Register Form Custom Input Type
Form::macro('custom', function($type, $key, $val, $attrs = array())
{
    $attrs_str = array();
    foreach($attrs as $attr_key => $attr_val)
        $attrs_str[] = $attr_key . '="' . $attr_val . '"';
    return '<input type="' . $type . '" name="' . $key . '" id="' . $key . '" value="' . $val . '" ' . implode(" ", $attrs_str) . ' />';
});