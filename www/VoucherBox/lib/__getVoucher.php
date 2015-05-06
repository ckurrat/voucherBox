#!/usr/bin/php -q
<?php
require 'tVoucher.php'; 
require '../etc/config.php';

function print_help_message() {
	echo "Usage: $argv[0] [-v validity_in_days] [-u email_Address] [-h]\n\n";
}

$voucherValidity = 7; 
$userEmail = "voucherTest@unicef.org";

$cmdlineOptions = getopt("v:h"); 

foreach (array_keys($cmdlineOptions) as $opt) 
	switch ($opt) {
	case 'v':
		$voucherValidity = $cmdlineOptions['v'];
		break;
	case 'u':
		$userEmail = $cmdlineOptions['u'];
		break;
	case 'h':
		print_help_message();
		exit(1);
}

$parts = explode("@", $userEmail);
$userID = strtolower( $parts[0] );

$objVoucher = new tVoucher($dbOptions); 
$aVoucher = $objVoucher->retrieve($voucherValidity, $userID);

echo "<$aVoucher>\n";
?>
