<?php
require __DIR__ . '/vendor/autoload.php'; // THIS IS CRUCIAL

use PHPMailer\PHPMailer\PHPMailer;

echo class_exists(PHPMailer::class) ? "PHPMailer is working!" : "PHPMailer not found";