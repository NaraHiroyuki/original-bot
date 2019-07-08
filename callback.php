<?php
 $accessToken = 'Ak437zeTmxtWY3WLe6hMgOm++kaItQngv2RiKgwqPhb2B88FkStdcbMh55WhuRmf/hNTz+XlgyteKUfNhNl9PJQcxADRxqKd4laB+Tp9PR0s2vajpKF0ixE7eDx7sG+tdlnj+08JG0L5Pik94FVATAdB04t89/1O/w1cDnyilFU=';
require_once("ID.php");
require_once("date_data.php");
require_once("site_data.php");
//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);
 
//取得データ
$replyToken = $json_object->{"events"}[0]->{"replyToken"};        //返信用トークン
$message_type = $json_object->{"events"}[0]->{"message"}->{"type"};    //メッセージタイプ
$message_text = $json_object->{"events"}[0]->{"message"}->{"text"};    //メッセージ内容
 
//メッセージタイプが「text」以外のときは何も返さず終了
if($message_type != "text") exit;

//地域IDを取得する
$return_message_text = "こんにちは!";
$areaID = $ID[$message_text];
if(empty($areaID)){
    $return_message_text .= "地域名を送信するとその地域の天気情報を返信します。";
    $return_message_text .= "調べられる地域は北見、札幌、盛岡、仙台、秋田、福島、前橋、千葉、東京、横浜、新潟、金沢、長野、岐阜、静岡、名古屋、京都、大阪、神戸、奈良、和歌山、鳥取、広島、松山、高知、福岡、長崎、熊本、宮崎、鹿児島、那覇です";
    $return_message_text .= "「○○県の観光」と送信するとその地域の観光スポットの情報を得られます。「○○県のデートスポット」と送信するとその地域のデートスポットの情報を得られます。";
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
      $return_message_text .= "\n"."最低気温は{$tem_min}度です。";
      if($tem_min < 0){
        $return_message_text .= "❄️";
    } elseif($tem_min >= 25){
        $return_message_text .= "😳";
    } elseif($tem_min >= 30){
        $return_message_text .= "😡";
    } elseif($tem_min >= 35){
        $return_message_text .= "🔥";
    }
    }
    if (!empty($tem_max)) {
      // 入っている処理
      $return_message_text .= "\n"."最高気温は{$tem_max}度です。";
      if($tem_max < 0){
        $return_message_text .= "❄️";
    } elseif($tem_max >= 25){
        $return_message_text .= "😳";
    } elseif($tem_max >= 30){
        $return_message_text .= "😡";
    } elseif($tem_max >= 35){
        $return_message_text .= "🔥";
    }
    }
    
    // 明日の情報
    if(!empty($today) || !empty($weather)){
        $return_message_text .= "\n"."{$tomorrow}の天気は{$to_weather}です";
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
            $return_message_text .= "\n"."最低気温は{$to_tem_min}度です。";
            if($to_tem_min < 0){
                $return_message_text .= "❄️";
            } elseif($to_tem_min >= 25){
                $return_message_text .= "😳";
            } elseif($to_tem_min >= 30){
                $return_message_text .= "😡";
            } elseif($to_tem_min >= 35){
                $return_message_text .= "🔥";
            }
        }
        if (!empty($to_tem_max)) {
            // 入っている処理
            $return_message_text .= "\n"."最高気温は{$to_tem_max}度です。";
            if($to_tem_max < 0){
                $return_message_text .= "❄️";
            } elseif($to_tem_max >= 25){
                $return_message_text .= "😳";
            } elseif($to_tem_max >= 30){
                $return_message_text .= "😡";
            } elseif($to_tem_max >= 35){
                $return_message_text .= "🔥";
            }
        }
    }
    //  明後日の情報
    if(!empty($today) || !empty($weather)){
        $return_message_text .= "\n"."{$day_after_tomorrow}の天気は{$af_weather}です";
        if($af_weather == "晴れ"){
            $return_message_text .= "☀️";
        } elseif ($af_weather == "晴時々曇"){
            $return_message_text .= "🌤";
        } elseif ($af_weather == "曇時々雨"){
            $return_message_text .= "☁️🌧";
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
            $return_message_text .= "\n"."最低気温は{$af_tem_min}度です。";
            if($af_tem_min < 0){
                $return_message_text .= "❄️";
            } elseif($af_tem_min >= 25){
                $return_message_text .= "😳";
            } elseif($af_tem_min >= 30){
                $return_message_text .= "😡";
            } elseif($af_tem_min >= 35){
                $return_message_text .= "🔥";
            }
        }
        if (!empty($af_tem_max)) {
            // 入っている処理
            $return_message_text .= "\n"."最高気温は{$af_tem_max}度です。";
            if($af_tem_max < 0){
                $return_message_text .= "❄️";
            } elseif($af_tem_max >= 25){
                $return_message_text .= "😳";
            } elseif($af_tem_max >= 30){
                $return_message_text .= "😡";
            } elseif($af_tem_max >= 35){
                $return_message_text .= "🔥";
            }
        }
    }
}

$site_ID = $site_data[$message_text];
$date_ID = $date_data[$message_text];

if(!empty($site_ID)){
    
    // カルーセルタイプ 
    $return_message_text = [ 
       'type' => 'template', 
       'altText' => 'カルーセル', 
       'template' => [
            'type' => 'carousel', 
            'columns' => [ 
               [ 
                   'title' => $message_text.'情報', 
                   'text' => 'じゃらんの情報に移動します',
                    'actions' => [
                       [ 
                           'type' => 'uri', 
                           'label' => $message_text.'情報へ',
                           'uri' => $site_ID
                       ] 
                   ] 
               ]
           ] 
       ] 
  ];
  send_carousel($accessToken, $replyToken, $return_message_text );
} elseif(!empty($date_ID)){
    
    // カルーセルタイプ 
    $return_message_text = [ 
       'type' => 'template', 
       'altText' => 'カルーセル', 
       'template' => [
            'type' => 'carousel', 
            'columns' => [ 
               [ 
                   'title' => $message_text.'情報', 
                   'text' => 'じゃらんの情報に移動します',
                    'actions' => [
                        [ 
                           'type' => 'uri', 
                           'label' => $message_text.'情報へ',
                           'uri' => $date_ID
                        ] 
                   ] 
               ]
           ] 
       ] 
  ];
  send_carousel($accessToken, $replyToken, $return_message_text );
} else {
    //返信実行
    send_messages($accessToken, $replyToken, $message_type, $return_message_text);
}

?>
<?php

//メッセージの送信
function send_messages($accessToken, $replyToken, $message_type, $return_message_text){
    
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

//カルーセルの送信
function send_carousel($accessToken, $replyToken, $return_message_text ){
    
    //ポストデータ
    $response = [ 'replyToken' => $replyToken, 'messages' => [$return_message_text] ]; 
    
    //curl実行
    error_log(json_encode($response)); 
    $ch = curl_init('https://api.line.me/v2/bot/message/reply'); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response)); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json; charser=UTF-8', 'Authorization: Bearer ' . $accessToken )); 
    $result = curl_exec($ch); error_log($result); 
    curl_close($ch);

}
?>

