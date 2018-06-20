<?php

require "controllers.php";

$access_token="Access Token";

if(isset($_REQUEST['hub_challenge']))
{
    $challenge = $_REQUEST['hub_challenge'];
    $token = $_REQUEST['hub_verify_token'];
}

$Token ="Token";

if($token =="Token")
{
    echo $challenge;
}

$input = json_decode(file_get_contents('php://input'),true);
$postback = $input['entry'][0]['messaging'][0]['postback'];
$attachments = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload'];
$type = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['type'];
$quickreply = $input['entry'][0]['messaging'][0]['message']['quick_reply'];
$userID = $input['entry'][0]['messaging'][0]['sender']['id'];
$message = $input['entry'][0]['messaging'][0]['message']['text'];

MessagePostBack($message, $postback, $attachments, $quickreply, $type);



function MessagePostBack($message, $postback, $attachments, $quickreply, $type)
{
    global $access_token, $userID;

    $template = new Templates($userID);
    $user = new Users($userID);

    $u_profile = json_decode($user->user_profile($access_token), true);
    $fname = $u_profile['first_name'];
    $reply1 = 'Hi ' . $fname . ', We shall be glad to see you.';
    $reply2 = 'Hi ' . $fname . ', You\'ll miss alot if you don\'t come';
    $reply3 = 'Hi ' . $fname . ', next time maybe.';

    if (isset($postback) && !empty($postback)) {

        switch ($postback['payload']) {
            case 'going':

                send_message($reply1);

                break;
            case 'notsure':

                send_message($reply2);

                break;
            case 'notgoing':

                send_message($reply3);
                break;

    }
    } elseif (isset($attachments) && !empty($attachments)) {
        if($type=="image" )
        {
            $imageurl = $attachments['url'];
            RunCurlPost($template->images_template($imageurl));
            send_message("Is this the image that you sent?");
            return;
        }

    } elseif (isset($message) && !empty($message))
    {
        CheckInput($message, $fname, $userID);
        return;
    }


}

function CheckInput($message, $name, $userID )
{
    $template = new Templates($userID);
    $reply = "Didn't quite get that. Click Options on the menu to get a list of options";

    if (preg_match('/(.*?)(hi|hello|helo|alo|hey|hallo|halo)/', strtolower($message))) {
        $replyh = "Hi " . $name . " how may I be of service?";

        send_message($replyh);

    }elseif ($message=="buttons")
    {
        RunCurlPost($template->button_template("Are you comming"));
    }
}


function send_message($reply)
{   global $userID;
    $template = new Templates($userID);
    RunCurlPost($template->msg_template($reply));
    return;
}

function RunCurlPost($json)
{
    global $access_token;
    $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: 	 application/json']);
    curl_exec($ch);
    curl_close($ch);
    return;
}


function RunCurlGet($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: 	 application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
}

function http_requests($url)
{
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    return $data;
}
