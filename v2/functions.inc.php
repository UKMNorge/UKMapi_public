<?php

function abort( $error, $error_no ) {
    echo json_encode( [
        'success' => false,
        'error_message' => $error,
        'error_code' => $error_no
    ] );
    die();
}