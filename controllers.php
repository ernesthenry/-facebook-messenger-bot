<?php

class Users
{
    private $userID;

    public function __construct($userID)
    {
        $this->userID = $userID;
    }

    function user_profile($access_token) {

        $url = "https://graph.facebook.com/v2.6/".$this->userID."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=".$access_token;

        return RunCurlGet($url);
    }
}

class Templates
{

    protected $userID;

    public function __construct($userID)
    {
        $this->userID = $userID;
    }

    function msg_template($reply)
    {
        $jsonData = "{
		'recipient': {
			'id': $this->userID	
		},
		'message': {
		'text': '".addslashes($reply)."'
		}
	}";
        return $jsonData;
    }



    function images_template($imglink)
    {

        $image = "{
              'recipient':{
                'id':$this->userID
              },
              'message':{
                'attachment':{
                  'type':'image',
                  'payload':{
                    'url':'".addslashes($imglink)."'
                  }
                }
              }
            }";

        return $image;
    }

    function button_template($reply)
    {
        $jsonButtons = "{
	    'recipient': {
            'id': $this->userID
        },
        'message':{
            'attachment':{
                'type': 'template',
                'payload':{
                    'template_type': 'button',
                    'text': '".addslashes($reply)."',
                    'buttons':[
                                {
                                    'type': 'postback',
                                    'title': 'Going',
                                    'payload' : 'going'
                                },
                                {
                                    'type': 'postback',
                                    'title': 'Not sure',
                                    'payload': 'notsure'
                                },
                                {
                                    'type': 'postback',
                                    'title': 'Not going',
                                    'payload': 'notgoing'
                                }
                               ]
                            }
                          }
                        }
	                }";

        return $jsonButtons;
    }




}

