#!/usr/bin/php -q
<?php

require 'tVoucher.php';
require '../etc/config.php';

function print_help_message()
{
	echo "Usage: $argv[0] {-n [number to generate]} {-v [validity in days]} {-l [voucher string lenght]} -h\n\n";
}

$nVouchers = 1;
$voucherValidity = 7;
$voucherLenght = 8;

$cmdlineOptions = getopt("n:v:l:h");

foreach (array_keys($cmdlineOptions) as $opt) {
	switch ($opt) {
	case 'n':
		$nVouchers = $cmdlineOptions['n'];
		break;
	case 'v':
		$voucherValidity = $cmdlineOptions['v'];
		break;
	case 'l':
		$voucherLenght = $cmdlineOptions['l'];
		break;
	case 'h':
		print_help_message();
		exit(1);
	}
}

$objVoucher = new tVoucher($dbOptions);
$objVoucher->generate($nVouchers, $voucherValidity, $voucherLenght);

?>
