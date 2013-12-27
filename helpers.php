<?php

/**
 * translation helper function
 *
 *     __('Hello {{name}}', [ 'name' => 'John' ]);
 *     __('Hello %1',  'John' );
 *
 */
function __($msgId)
{
    $args = func_get_args();
    array_shift( $args ) ;
    $msg = _($msgId);
    if ( is_array($args[0]) ) {
        foreach( $args[0] as $key => $value ) {
            $msg = str_replace( '{{' . $key . '}}', $value, $msg);
        }
    } else {
        $id = 1;
        foreach ($args as $arg) {
            $msg = str_replace( "%$id" , $arg , $msg );
            $id++;
        }
    }
    return $msg;
}

