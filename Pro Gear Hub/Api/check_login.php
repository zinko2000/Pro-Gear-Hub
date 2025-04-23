<?php
require_once '../config.php';
require_once '../auth/auth_functions.php';

header('Content-Type: application/json');

$response = [
    'loggedIn' => isLoggedIn(),
    'username' => isLoggedIn() ? $_SESSION['username'] : null
];

echo json_encode($response);
?>