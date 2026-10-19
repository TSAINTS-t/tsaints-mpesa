<?php
// TSAINTS M-Pesa STK Push
$consumerKey = "YOUR_CONSUMER_KEY";
$consumerSecret = "YOUR_CONSUMER_SECRET";
$shortcode = "174379";
$passkey = "YOUR_PASSKEY";
$callbackUrl = "https://tsaints-t.github.io/tsaints-mpesa/callback.php";

function getToken(){
  global $consumerKey,$consumerSecret;
  $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
  $credentials = base64_encode($consumerKey.":".$consumerSecret);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic ".$credentials]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  return json_decode($response)->access_token;
}

if(isset($_POST['phone'], $_POST['amount'])){
  $phone = $_POST['phone'];
  $amount = $_POST['amount'];
  $token = getToken();
  $timestamp = date("YmdHis");
  $password = base64_encode($shortcode.$passkey.$timestamp);
  
  $data = [
    "BusinessShortCode"=>$shortcode,
    "Password"=>$password,
    "Timestamp"=>$timestamp,
    "TransactionType"=>"CustomerPayBillOnline",
    "Amount"=>$amount,
    "PartyA"=>$phone,
    "PartyB"=>$shortcode,
    "PhoneNumber"=>$phone,
    "CallBackURL"=>$callbackUrl,
    "AccountReference"=>"TSAINTS",
    "TransactionDesc"=>"Payment"
  ];
  
  $ch = curl_init("https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest");
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token","Content-Type: application/json"]);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  echo $result;
}
?>
<form method="POST">
Phone: <input name="phone" value="2547..." required><br>
Amount: <input name="amount" value="1" required><br>
<button type="submit">Pay with M-Pesa</button>
</form>
