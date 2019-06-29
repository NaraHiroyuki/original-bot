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
 
//地域ID 北見、札幌、盛岡、仙台、秋田、福島、前橋、千葉、東京、横浜、新潟、金沢、長野、岐阜、静岡、名古屋、京都、大阪、神戸、奈良、和歌山、鳥取、広島、松山、高知、福岡、長崎、熊本、宮崎、鹿児島、那覇のID
$ID = [
    "北見" => 013020, 
    "札幌" => 016010,
    "盛岡" => 030010,
    "仙台" => 040010,
    "秋田" => 050010,
    "福島" => 070010,
    "前橋" => 100010,
    "千葉" => 120010,
    "東京" => 130010,
    "横浜" => 140010,
    "新潟" => 150010,
    "金沢" => 170010,
    "長野" => 200010,
    "岐阜" => 210010,
    "静岡" => 220010,
    "名古屋" => 230010,
    "京都" => 260010,
    "大阪" => 270000,
    "神戸" => 280010,
    "奈良" => 290010,
    "和歌山" => 300010,
    "鳥取" => 310010,
    "広島" => 340010,
    "松山" => 380010,
    "高知" => 390010,
    "福岡" => 400010,   
    "長崎" => 420010,
    "熊本" => 430010,
    "宮崎" => 450010,
    "鹿児島" => 460010,
    "那覇" => 471010,
];

//地域IDを取得する
$return_message_text = "こんにちは!";
$areaID = $ID[$message_text];
if(empty($areaID)){
    $return_message_text .= "地域名を送信するとその地域の天気情報を返信します。";
    $return_message_text .= "調べられる地域は北見、札幌、盛岡、仙台、秋田、福島、前橋、千葉、東京、横浜、新潟、金沢、長野、岐阜、静岡、名古屋、京都、大阪、神戸、奈良、和歌山、鳥取、広島、松山、高知、福岡、長崎、熊本、宮崎、鹿児島、那覇です";
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
$today = $arr["forecasts"][0]["dateLabel"];
$weather = $arr["forecasts"][0]["telop"];
$tem_min = $arr["forecasts"][0]["temperature"]["min"]["celsius"];
$tem_max = $arr["forecasts"][0]["temperature"]["max"]["celsius"];

$tomorrow = $arr["forecasts"][1]["dateLabel"];
$to_weather = $arr["forecasts"][1]["telop"];
$to_tem_min = $arr["forecasts"][1]["temperature"]["min"]["celsius"];
$to_tem_max = $arr["forecasts"][1]["temperature"]["max"]["celsius"];

$day_after_tomorrow = $arr["forecasts"][2]["dateLabel"];
$af_weather = $arr["forecasts"][2]["telop"];
$af_tem_min = $arr["forecasts"][2]["temperature"]["min"]["celsius"];
$af_tem_max = $arr["forecasts"][2]["temperature"]["max"]["celsius"];

$today_info = "";
$tomorrow_info = "";
$day_after_tomorrow_info = "";

$date_info = [$today,$tomorrow,$day_after_tomorrow];
$weather_info = [$weather,$to_weather,$af_weather];
$tem_min_info = [$tem_min,$to_tem_min,$af_tem_min];
$tem_max_info = [$tem_max,$to_tem_max,$af_tem_max];
$information = [$today_info,$tomorrow_info,$day_after_tomorrow_info];
$length = count($information);
if(!empty($areaID)){
    for ($i=0;$i<$length;$i++){
        $information[$i] = "{$date_info[$i]}の天気は{$weather_info[$i]}です";
        if($weather_info[$i] == "晴れ"){
            $information[$i] .= "☀️";
        } elseif ($weather_info[$i] == "晴時々曇"){
            $information[$i] .= "🌤";
        } elseif ($weather_info[$i] == "曇時々雨"){
            $information[$i] .= "🌨";
        } elseif ($weather_info[$i] == "曇り"){
            $information[$i] .= "☁️";
        } elseif ($weather_info[$i]== "雨"){
            $information[$i] .= "☔️";
        } else {
            $information[$i] .= "。";
        }
        if (!empty($tem_min_info[$i])) {
          // 入っている処理
          $information[$i] .= "最低気温は{$tem_min_info[$i]}度です";
        }
        if (!empty($tem_max_info[$i])) {
          // 入っている処理
          $information[$i] .= "最高気温は{$tem_max_info[$i]}度です";
        }
    }
    for($i=0;$i<$length;$i++){
        $return_message_text .= $information[$i];
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