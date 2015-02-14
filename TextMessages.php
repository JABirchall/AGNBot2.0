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
    public $BotChannel;

    public function __construct(TeamSpeak3_Adapter_ServerQuery_Event $event, $host)
    {
        $this->event = $event;
        $this->Teamspeak3Host = $host;
        $this->BotChannel = $this->Teamspeak3Host->channelGetByName("Public Bot Channel");
    }

    public function ServerMessageHandler()
    {
        if($this->startsWith("!version", $this->event['msg'])) $this->Teamspeak3Host->message("IM AGNBot2.0 Not even versioned");
        if($this->startsWith("!dchannel", $this->event['msg'])) $this->DonatorChannel();
    }

    public function ChannelMessageHandler()
    {
        if($this->startsWith("!tchannel", $this->event['msg'])) $this->TempChannel();
        if($this->startsWith("!help", $this->event['msg'])) $this->ChannelHelp();
    }

    public function PrivateMessageHandler()
    {
        $this->event->getData();
    }

    /**
     * Command Functiuons
     * Large blocks of code we dont want in the Handlers
     */

    private function ChannelHelp()
    {
        $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
        $Client->message("[color=green] [b]Available commands:");
        $Client->message("[color=green] [b]!tchannel[/b] - Request a temporary channel, will be deleted after 30 minutes.");
        $Client->message("[color=green] [b]!validate[/b] - Validate you forum login to get member permissions. (Not complete)");
    }

    private function TempChannel()
    {
        list($command, $name) = explode(' ', $this->event['msg']);
        $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
        $info = $ts3_Client->getInfo();
        if($info['client_servergroups'] == 8)
        {
            $this->BotChannel->message("[color=red][ERROR] You must be a member to request a temp channel");
            $this->BotChannel->message("[color=red] You can register on our website [url=http://aggressivegaming.org/index.php]http://aggressivegaming.org/index.php[/url]");
            return;
        }
        for($i = 0; $i <= count(@$_SESSION); $i++)
        {
            //if(isset($_SESSION[$i]['cid'])) echo "{$_SESSION[$i]['expire']} : ".time()."\n";

            if(isset($_SESSION[$i]['clid']) && $_SESSION[$i]['expire'] >= time()){
                if($_SESSION[$i]['clid'] == $Client["client_database_id"])
                {
                    $this->BotChannel->message("[color=red][ERROR] You can only request a channel once every 24 hours.");
                    return;
                }
            }
        }

        $timeObj = new DateTime();
        $timeObj->add(new DateInterval('PT1M'));
        try {
            $channel = $this->Teamspeak3Host->channelCreate([
                "channel_name" => "Temporary Channel: {$name}",
                "channel_flag_permanent" => TRUE,
                "channel_description" => "This is a temporary channel, No users have permissions in this channel.\nThis channel will be deleted at " . $timeObj->format('H:i:s O'),
                "cpid" => 296,
                "channel_codec_quality" => 5
            ]);
        }catch(Exception $e){
            //echo "[ERROR]  " . $e->getMessage() . "\n". $e->getTraceAsString() ."\n";
            $this->BotChannel->message("[color=red][ERROR] ".$e->getMessage());
            return;
        }
        $this->Teamspeak3Host->clientMove($this->Teamspeak3Host->whoamiGet("client_id"), $this->BotChannel = $this->Teamspeak3Host->channelGetById($channel));

        $Client->move($channel);

        $this->BotChannel->message("[color=blue] Channel created channel will expire in 60 seconds");

        $this->BotChannel = $this->Teamspeak3Host->channelGetByName("Public Bot Channel");
        $this->Teamspeak3Host->clientMove($this->Teamspeak3Host->whoamiGet("client_id"), $this->BotChannel);

        $_SESSION[] = ['cid' => $channel, 'expire' => $timeObj->getTimestamp()];

        //var_dump($_SESSION);
    }

    public function DeleteTempChannel($channel)
    {
        try {
            $this->BotChannel = $this->Teamspeak3Host->channelGetById($channel);
            //$this->Teamspeak3Host->clientMove($this->Teamspeak3Host->whoamiGet("client_id"), $channel);
            //$this->BotChannel->message("[color=red]This temporary channel has expired, If you wish to have a permanent channel you can donate.");

            foreach ($this->BotChannel->clientList() as $Client) {
                $Client->kick(TeamSpeak3::KICK_CHANNEL, "Temporary channel expired!");
                $Client->message("[color=red]This temporary channel has expired, If you wish to have a permanent channel you can donate.");
                $_SESSION[] = ['clid' => $Client["client_database_id"], 'expire' => time()+86400];
            }
            $this->BotChannel->delete();
            $this->BotChannel = $this->Teamspeak3Host->channelGetByName("Public Bot Channel");
        }catch(Exception $e){
            $this->BotChannel->message("[color=red][ERROR] ".$e->getMessage());
            return;
        }
    }

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