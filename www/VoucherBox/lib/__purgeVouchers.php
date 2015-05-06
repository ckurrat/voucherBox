#!/usr/bin/php -q
<?php
require 'tVoucher.php'; 
require '../etc/config.php';

$objVoucher = new tVoucher($dbOptions); 

$nVoucherBeforePurging = $objVoucher->count();

$objVoucher->purgeExpired();

$nVoucherAfterPurging = $objVoucher->count();
$nInUseVouchers = $objVoucher->countInUse();
$nOneDayVouchersAfterPurging = $objVoucher->countAvailable(1);
$nOneWeekVouchersAfterPurging = $objVoucher->countAvailable(7);
$nOneMonthVouchersAfterPurging = $objVoucher->countAvailable(31);

echo "\n";
echo "Vouchers in the database: \n";
echo "\n";
echo "    Total: $nVoucherAfterPurging (Purged: ".  ($nVoucherBeforePurging - $nVoucherAfterPurging) .")\n";
echo "   In use: $nInUseVouchers\n";
echo "  One Day: $nOneDayVouchersAfterPurging\n";
echo " One Week: $nOneWeekVouchersAfterPurging\n";
echo "One Month: $nOneMonthVouchersAfterPurging\n";
echo "\n";
?>
