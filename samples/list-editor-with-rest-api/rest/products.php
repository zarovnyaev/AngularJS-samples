<?php

include('api.php');
include('sample_api.php');
include('db/db.php');

try {
    $oSampleAPI = new SampleAPI();
    echo $oSampleAPI->process();
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(Array('error' => $e->getMessage()));
}
