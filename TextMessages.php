<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 08:51
 */

class TextMessages
{
    public $event;
    public $Teamspeak3Host;

    public function __construct(TeamSpeak3_Adapter_ServerQuery_Event $event, $host)
    {
        $this->event = $event;
        $this->Teamspeak3Host = $host;
    }

    public function ServerMessageHandler()
    {
        if($this->startsWith("!version", $this->event['msg'])) $this->Teamspeak3Host->message("IM AGNBot2.0 Not even versioned");
    }

    public function ChannelMessageHandler()
    {
        //Channel message handler
    }

    public function PrivateMessageHandler()
    {
        // Private message handler
    }

    public function startsWith($pattern, $string)
    {
        return (substr($string, 0, strlen($pattern)) == $pattern) ? TRUE : FALSE;
    }
}