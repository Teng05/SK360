<?php

require_once('../classes/database.php');

$con = new database();

if (isset($_POST['phone_number'])) {
    $phone = $_POST['phone_number'];

    if ($con->isPhoneExists($phone)) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
}