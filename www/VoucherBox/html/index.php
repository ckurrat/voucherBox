<!doctype html>
<html>
    <head>
        <title>Guest WiFi voucher generator
        </title>
    </head>
    <body>

<?php
session_start();
$username=($_SESSION["user"]);
?>

        <p style="text-align: center;">
            <span style="font-size:28px;"><strong>Welcome to the Guest WiFi voucher generator</strong>
            </span>
        </p>
        <p>&nbsp;
        </p>
        <p style="text-align: center;"><strong>This web site allows the provision of&nbsp;voucher codes for the Guest WiFi network.</strong>
        </p>
        <p>&nbsp;
        </p>
        <p>Follow the instructions below to have a new voucher generated and emailed the specified email.
        </p>
        <p>&nbsp;
        </p>
        <form name="voucherData" action="processVoucherRequest.php" method="post">
            <div><strong>Step 1.</strong>&nbsp;<strong>Select the WiFi voucher validity among the available options.</strong>&nbsp;
            </div>
            <div>
                <p>Note that the voucher will expire&nbsp;<u>in the specified time after its&nbsp;first usage</u>.
                </p>

                <p>
                    <input type="radio" name="cb_Validity" value="1" checked> One Day<br>
                    <input type="radio" name="cb_Validity" value="7"> One Week<br>
                    <input type="radio" name="cb_Validity" value="31" > One Month
                </p>

            </div><strong>Step 2. Specify your email address (@dummy.net) - or any @dummy.net valid email address</strong>
            <p>The WiFi voucher will be instantly sent via email to the specified recipient
            </p>
<?php
echo "<p>Voucher recipient:&nbsp;<input style='width:200px' name='s_recipientEmail' type='email' value='$username@dummy.net' /></p>";
?>
            <p>&nbsp;
            </p>
            <p><strong>Step 3. Check the information below and confirm</strong>
            </p>
            <p>
                <input name="b_Generate" type="submit" value="Generate" />
            </p>
        </form>
        <p>&nbsp;
        </p>
    </body>
</html>

