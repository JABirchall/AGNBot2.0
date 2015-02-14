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
        if($this->startsWith("!dchannel", $this->event['msg'])) $this->DonatorChannel();
    }

    public function ChannelMessageHandler()
    {
        //Channel message handler
    }

    public function PrivateMessageHandler()
    {
        $this->event->getData();
    }

    /**
     * Command Functiuons
     * Large blocks of code we dont want in the Handlers
     */

    private function DonatorChannel()
    {
        list($command, $user) = explode(' ', $this->event['msg']);
        if($user == 'help' || !$user){
            $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
            $Client->message("[color=red] Use !dchannel [username] ");
            return;
        }

        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        foreach ($this->Teamspeak3Host->clientList() as $Client) {
            if ($user === strtolower($Client)){
                $Client->message("[color=blue] Your channel is being created.");
                $Client->message("[color=blue] Channel name: {$user}.");
                $Client->message("[color=blue] Password: {$password}");
                var_dump($Client);
                break;
            }
        }
        // Create channel if no errors occurred
        try {
            $channel = $this->Teamspeak3Host->channelCreate([
                "channel_name" => $user,
                "channel_password" => $password,
                "channel_flag_permanent" => TRUE,
                "cpid" => 296,
                "channel_codec_quality" => 10
            ]);
        }catch(Exception $e){
            echo "[ERROR]  " . $e->getMessage() . "\n". $e->getTraceAsString() ."\n";
            return;
        }
        $Client->message("[color=blue] You are now Channel Admin!");
        $Client->setChannelGroup($channel, 5);
    }

    public function startsWith($pattern, $string)
    {
        return (substr($string, 0, strlen($pattern)) == $pattern) ? TRUE : FALSE;
    }


}