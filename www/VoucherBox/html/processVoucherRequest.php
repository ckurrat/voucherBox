<?php

require '../lib/tVoucher.php'; 
require '../lib/PHPMailerAutoload.php';

require '../etc/config.php';


$objVoucher = new tVoucher($dbOptions); 
$objMailer = new PHPMailer;
// Enable verbose debug output
//$objMailer->SMTPDebug = 3;

function died($error) {
	// your error code can go here
	echo "We are very sorry, but there were error(s) found with the form you submitted. ";
	echo "These errors appear below.<br /><br />";
	echo $error."<br /><br />";
	echo "Please go back and try again.<br /><br />";
	die();
}

function clean_string($string) {
	$bad = array("content-type","bcc:","to:","cc:","href");
	return str_replace($bad,"",$string);
}

function chkValidity($validity) {
    $arrVal =  array(1, 7, 31);
    if (in_array($validity, $arrVal)) {
        return true;
    }
    return false;
}

function chkEmail($email) {

	// if strng is null it is definitively an invalid email address
	if(!$email) {
		return false;
	}

	// check existence of a single @ in the string
	$num_at = count(explode( '@', $email )) - 1;
	if($num_at != 1) {
		return false;
	}

	// check existence of invalid chars in the string
	if(strpos($email,';') || strpos($email,',') || strpos($email,' ')) {
		return false;
	}

	// check string format
	if(!preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email)) {
		return false;
	}

	return true;
}

$validity = isset($_REQUEST['cb_Validity']) ? trim($_REQUEST['cb_Validity']) : ""; // required
$email_to = isset($_REQUEST['s_recipientEmail']) ? trim($_REQUEST['s_recipientEmail']) : ""; // required

if (!chkEmail($email_to) || !chkValidity($validity)) 
{
    echo "Invalid information provided, please <a href='javascript:window.history.back()'>try again</a>";
    exit;
}

if ( !file_exists($email_template) ) {
	died('The site is not properly configured, please notify the site administrator.');
}

$parts = explode("@", $email_to);
$userID = strtolower( $parts[0] );

$aVoucher = $objVoucher->retrieve($validity, $userID);

if (!$aVoucher) {
 	died('No vouchers available, please notify the site administrator.');
}

$email_message = file_get_contents($email_template, FILE_USE_INCLUDE_PATH);

$email_message = $newString = str_replace("__VCODE__", $aVoucher, $email_message);
$email_message = $newString = str_replace("__VALIDITY__", $validity, $email_message);
$email_message = $newString = str_replace("__REQUESTOR__", $email_to, $email_message);

$objMailer->isSMTP();                                     // Set mailer to use SMTP
$objMailer->Host = $smtpRelay; 		                      // Specify main SMTP server
$objMailer->Port = 25;                                    // TCP port to connect to

$objMailer->From = $email_from;
$objMailer->FromName = "WiFi Voucher Service";
$objMailer->addAddress($email_to);                        // Name is optional
$objMailer->addBCC($email_admin);

$objMailer->isHTML(true);                                  // Set email format to HTML

$objMailer->Subject = $email_subject;
$objMailer->Body    = $email_message;
$objMailer->AltBody = 'You need to use a mail client supporting HTML to read this message.';

if(!$objMailer->send()) {
	died('Message could not be sent, please notify the site administrator.');
} else {
	echo "<br>The WiFi voucher has been emailed to the provided address.";
	exit;
}
?>

