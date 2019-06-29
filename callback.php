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

if(!empty($areaID)){
    // 今日の情報
    if (!empty($today) || !empty($weather)){
        $return_message_text .= "{$today}の天気は{$weather}です";
    } else {
        $return_message_text .= "申し訳ありません。現在この地域の天気情報を取得できません🙇‍";
    }
    if($weather == "晴れ"){
        $return_message_text .= "☀️";
    } elseif ($weather == "晴時々曇"){
        $return_message_text .= "🌤";
    } elseif ($weather == "曇時々雨"){
        $return_message_text .= "🌨";
    }elseif ($weather == "曇のち雨"){
        $return_message_text .= "☁️→☂️";
    }elseif ($weather == "曇り"){
        $return_message_text .= "☁️";
    } elseif ($weather == "雨"){
        $return_message_text .= "☔️";
    } else {
        $return_message_text .= "。";
    }
    if (!empty($tem_min)) {
      // 入っている処理
      $return_message_text .= "最低気温は{$tem_min}度です";
    }
    if (!empty($tem_max)) {
      // 入っている処理
      $return_message_text .= "最高気温は{$tem_max}度です";
    }
    
    // 明日の情報
    if(!empty($today) || !empty($weather)){
        $return_message_text .= "{$tomorrow}の天気は{$to_weather}です";
        if($to_weather == "晴れ"){
            $return_message_text .= "☀️";
        } elseif ($to_weather == "晴時々曇"){
            $return_message_text .= "🌤";
        } elseif ($to_weather == "曇時々雨"){
            $return_message_text .= "🌨";
        }elseif ($to_weather == "曇のち雨"){
            $return_message_text .= "☁️→☂️";
        }elseif ($to_weather == "曇り"){
            $return_message_text .= "☁️";
        } elseif ($to_weather == "雨"){
            $return_message_text .= "☔️";
        } else {
            $return_message_text .= "。";
        }
        if (!empty($to_tem_min)) {
            // 入っている処理
            $return_message_text .= "最低気温は{$to_tem_min}度です";
        }
        if (!empty($to_tem_max)) {
            // 入っている処理
            $return_message_text .= "最高気温は{$to_tem_max}度です";
        }
    }
    //  明後日の情報
    if(!empty($today) || !empty($weather)){
        $return_message_text .= "{$day_after_tomorrow}の天気は{$af_weather}です";
        if($af_weather == "晴れ"){
            $return_message_text .= "☀️";
        } elseif ($af_weather == "晴時々曇"){
            $return_message_text .= "🌤";
        } elseif ($af_weather == "曇時々雨"){
            $return_message_text .= "🌨";
        }elseif ($af_weather == "曇のち雨"){
            $return_message_text .= "☁️→☂️";
        }elseif ($af_weather == "曇り"){
            $return_message_text .= "☁️";
        } elseif ($af_weather == "雨"){
            $return_message_text .= "☔️";
        } else {
            $return_message_text .= "。";
        }
        if (!empty($af_tem_min)) {
            // 入っている処理
            $return_message_text .= "最低気温は{$af_tem_min}度です";
        }
        if (!empty($af_tem_max)) {
            // 入っている処理
            $return_message_text .= "最高気温は{$af_tem_max}度です";
        }
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