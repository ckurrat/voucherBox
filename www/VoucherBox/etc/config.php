<?php

$smtpRelay = "smtp.dummy.net";
$email_from = "noreply@dummy.net";
$email_subject = "WiFi Voucher";
$email_template = "emailTemplate.html";
$email_admin = "admin@dummy.net";

$dbOptions = array(
                'db_host' => 'localhost',
                'db_user' => 'radius',
                'db_pass' => 'radiuspassword',
                );

// use a FQDN where possible
$hostAddress = $_SERVER['SERVER_ADDR'];

?>
