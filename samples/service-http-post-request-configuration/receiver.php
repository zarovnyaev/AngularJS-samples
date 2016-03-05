<?php

// Save post data to the file
file_put_contents(
    'post-' . date("y-m-d_H-i-s") . '.txt', 
    file_get_contents("php://input")
);
