
<?php
# Required File Includes
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");
$gatewaymodule = "aamarpay"; # Enter your gateway module name here replacing template
$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback
# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
$status = $_REQUEST["pay_status"];
$invoiceid = $_REQUEST["mer_txnid"];
$transid = $_REQUEST["epw_txnid"];


$amount = $_REQUEST["other_currency"];

$amount_rec = $_REQUEST["store_amount"];
$fee = $_REQUEST["epw_service_charge_usd"];
$reason = $_REQUEST["reason"];
$whcms_redirect_invoice = $_REQUEST["fail_url"];

$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing
checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does
if ($status == "Successful") {
  
   # Successful
	print "<center>Please Wait.....Processing....</center>";
    $description = "
	<br/>Status : <b style='color:#5d994f;'>".$status." </b>
	<br>Invoice ID: ".$mer_txnid."
	<br>Bank Transaction ID : ".$bank_txn."
	<br>Card Type : ".$card_type."
	<br>Card Number : ".$card_number."
	<br>Currency: ".$currency_merchant."
	<br>Transaction Time :  ".$pay_time."  
	";
    addInvoicePayment($invoiceid,$transid,$amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
	logTransaction($GATEWAY["name"],$_POST,"Successful"); # Save to Gateway Log: name, data array, status
	//header("Location: $whcms_redirect_invoice");
	?>
<html>
<head>
</head>
<body onLoad="document.send_process.submit();">
<form name="send_process" method="POST" action="<?php echo $whcms_redirect_invoice;?>">
</form>
</body>
</html>
<?php
exit;

} else {

	# Unsuccessful
    print "<center>Please Wait.....Processing....</center>";
    $description = "
	<br/>Status : <b style='color:#FF0000;'>".$status." </b>
	<br>Failed Reason : ".$reason."
	<br>Invoice ID: ".$mer_txnid."
	<br>Bank Transaction ID : ".$bank_txn."
	<br>Card Type : ".$card_type."
	<br>Card Number : ".$card_number."
	<br>Currency: ".$currency_merchant."
	<br>Transaction Time :  ".$pay_time."  
	";
 
    logTransaction($GATEWAY["name"],$_POST,"Unsuccessful"); # Save to Gateway Log: name, data array, status
	//header("Location:  ".$whcms_redirect_invoice."");
	?>
<html>
<head>
</head>
<body onLoad="document.send_process.submit();">
<form name="send_process" method="POST" action="<?php echo $whcms_redirect_invoice;?>">
</form>
</body>
</html>
<?php
					exit;
    

}
?>
