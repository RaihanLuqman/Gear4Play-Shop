<?php
session_start();

$response = [
    'logged_in' => isset($_SESSION['id_user'])
];

echo json_encode($response);
?>
