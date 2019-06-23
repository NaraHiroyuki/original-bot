<?php
 
$accessToken = 'UqaD6EQOTUlLPtAnbn3BRaIOgV0+UJIAnzRCSV7vVNOB9222WnNDpY8zYGwSGufm/hNTz+XlgyteKUfNhNl9PJQcxADRxqKd4laB+Tp9PR385lPYM1malypo9aadrOAVznQUMmzlYrmWYYfwfALRLAdB04t89/1O/w1cDnyilFU=';
 
//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);
 
//取得データ
$replyToken = $json_object->{"events"}[0]->{"replyToken"};        //返信用トークン
$message_type = $json_object->{"events"}[0]->{"message"}->{"type"};    //メッセージタイプ
$message_text = $json_object->{"events"}[0]->{"message"}->{"text"};    //メッセージ内容
 
//メッセージタイプが「text」以外のときは何も返さず終了
if($message_type != "text") exit;
 
//地域ID 前橋,千葉,東京,福岡
$ID = [100010,120010,130010,400010];
$maebashi = "前橋の天気";
$chiba = "千葉の天気";
$toukyou = "東京の天気";
$hukuoka = "福岡の天気";

//地域IDを取得する
$areaID = "";
$return_message_text = "";
if($maebashi == $message_text){
    $areaID = $ID[0];
} elseif ($chiba == $message_text){
    $areaID = $ID[1];
} elseif ($tukoyou == $message_text){
    $areaID = $ID[2];
} elseif ($hukuoka == $message_text){
    $areaID = $ID[3];
} else {
    $return_message_text = "「" . $message_text . "」じゃねーよｗｗｗ";
}

$url = "http://weather.livedoor.com/forecast/webservice/json/v1?city=$areaID";

//cURLセッションを初期化する
$ch = curl_init();
 
//URLとオプションを指定する
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//URLの情報を取得する
$res =  curl_exec($ch);
 
$arr = json_decode($res,true);

//結果を表示する
$date = $arr["forecasts"][0]["dateLabel"];
$weather = $arr["forecasts"][0]["telop"];
$tem_min = $arr["forecasts"][0]["temperature"]["min"]["celsius"];
$tem_max = $arr["forecasts"][0]["temperature"]["max"]["celsius"];
if(!empty($areaID)){
  $return_message_text = "{$date}の天気は{$weather}です。";
  if (!empty($tem_min)) {
    // 入っている処理
    $return_message_text .= "最低気温は{$tem_min}です。";
  }
  if (!empty($tem_max)) {
    // 入っている処理
    $return_message_text .= "最高気温は{$tem_max}です。";
  }
}
//返信実行
sending_messages($accessToken, $replyToken, $message_type, $return_message_text);
?>
<?php

//メッセージの送信
function sending_messages($accessToken, $replyToken, $message_type, $return_message_text){
    
    //レスポンスフォーマット
    $response_format_text = [
        "type" => $message_type,
        "text" => $return_message_text
    ];
 
    //ポストデータ
    $post_data = [
        "replyToken" => $replyToken,
        "messages" => [$response_format_text]
    ];
 
    //curl実行
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
}

?>