<?php
$callbackData = file_get_contents('php://input');
file_put_contents("mpesa_log.json", $callbackData . PHP_EOL, FILE_APPEND);
$data = json_decode($callbackData, true);
if(isset($data['Body']['stkCallback'])){
  $resultCode = $data['Body']['stkCallback']['ResultCode'];
  if($resultCode == 0){
    $amount = $data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
    $receipt = $data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
    $phone = $data['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
    file_put_contents("success.txt", "Paid $amount from $phone Receipt: $receipt".PHP_EOL, FILE_APPEND);
  }
}
header("Content-Type: application/json");
echo json_encode(["ResultCode"=>0, "ResultDesc"=>"Success"]);
?>
