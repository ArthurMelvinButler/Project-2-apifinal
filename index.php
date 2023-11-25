<?php

header("Content-Type: application/json; charset=UTF-8");



require_once "./classes/class.request.php";


$request = new Request();

$rateLimitNotExceeded = $request->rateLimitCheck($_SERVER); //returns true if not exceeded

if ($rateLimitNotExceeded == 1) {
    $apiKeyCheck = $request->checkApiKey($_SERVER);

    if ($apiKeyCheck["key_id"] > 0 && !empty($apiKeyCheck["permissions"])) {
        $request->process($_SERVER, $apiKeyCheck["permissions"]);
    }else {
        http_response_code(401);
        die("Access Denied");
    }

}else if ($rateLimitNotExceeded == -1) {
    http_response_code(400);
    die("Incomplete Request");
} else  {
    http_response_code(429);
    die("Too many requests; Rate limit exceeded");
}