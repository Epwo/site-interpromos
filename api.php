<?php
    require_once 'resources/config.php';
    require_once 'resources/database.php';
    require_once LIBRARY_PATH . '/redirect.php';
    require_once LIBRARY_PATH . '/exceptions.php';

    $pathInfo = explode('/', trim($_SERVER['PATH_INFO'], '/\\'));

    header('content-type: application/json; charset=utf-8');

    $db = new Database();

    function getAuthorizationToken(): ?string{
    
        $headers = getallheaders();
    
        $authorization = $headers['Authorization'];
    
        if (!isset($authorization)) {
            APIErrors::invalidHeader();
        }
    
        $authorization = explode(' ', trim($authorization), 2)[1];
    
        if (empty($authorization)) {
            APIErrors::invalidGrant();
        }
        return $authorization;
    }
    
    class APIErrors{
    
        public static function invalidGrant()
        {
            http_response_code(400);
            die(json_encode(array(
                'error' => 'invalid_grant',
                'error_description' => 'The authorization code is invalid or expired.'
            )));
        }
    
        public static function invalidHeader()
        {
            http_response_code(400);
            die(json_encode(array(
                'error' => 'invalid_header',
                'error_description' => 'The request is missing the Authorization header or the Authorization header is invalid.'
            )));
        }
    
        public static function invalidRequest()
        {
            http_response_code(400);
            die(json_encode(array(
                'error' => 'invalid_request',
                'error_description' => 'The request is missing a parameter, uses an unsupported parameter, uses an invalid parameter or repeats a parameter.'
            )));
        }
    
        public static function invalidCredential()
        {
            http_response_code(400);
            die(json_encode(array(
                'error' => 'invalid_credential',
                'error_description' => 'The request has error(s) in the credentials gave.'
            )));
        }
    
        public static function internalError()
        {
            http_response_code(500);
            die();
        }
    }

    switch ($pathInfo[0] . $_SERVER['REQUEST_METHOD']) {
        default:
		http_response_code(404);
		die();
    }
?>