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

    public function __construct(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Server $host)
    {
        $this->event = $event;
        $this->Teamspeak3Host = $host;
        $this->BotChannel = $this->Teamspeak3Host->channelGetByName("[ Private Administration Room + Bots ]");
    }

    public function ServerMessageHandler()
    {
        if($this->startsWith("!version", $this->event['msg'])) $this->Teamspeak3Host->message("I'm AGNBot2 : ".VERSION);
        if($this->startsWith("!dchannel", $this->event['msg'])) $this->DonatorChannel();
        if($this->startsWith("!clientinfo", $this->event['msg'])) $this->ClientInfo();
        if($this->startsWith("!jail", $this->event['msg'])) $this->Jail();
        if($this->startsWith("!help", $this->event['msg'])) $this->help();
    }

    public function ChannelMessageHandler()
    {
        if($this->startsWith("!tchannel", $this->event['msg'])) $this->TempChannel();
        if($this->startsWith("!help", $this->event['msg'])) $this->ChannelHelp();
    }

    public function PrivateMessageHandler()
    {
        if($this->startsWith("!help", $this->event['msg'])) $this->PrivateHelp();
    }

    /**
     * Command Functions
     * Large blocks of code we dont want in the Handlers
     */

    private function ChannelHelp()
    {
        $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
        $Client->message("[color=green] [b]----- Channel Commands -----");
        $Client->message("[color=green] [b]Command !tchannel:[/b] Request a temporary channel, will be deleted after 30 minutes.");
        $Client->message("[color=green] [b]Command !validate:[/b] Validate you forum login to get member permissions. (Not complete)");
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

    private function Jail()
    {
        $jail = "~JAIL~";
        echo "Called Jail()\n";
        list($command, $user) = explode(' ', $this->event['msg']);
        try {
            $suspect = $this->Teamspeak3Host->clientGetByName($user);
            $admin = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
            $suspect->addServerGroup(169);
            $suspect->move($this->Teamspeak3Host->channelGetByName($jail));
            $admin->move($this->Teamspeak3Host->channelGetByName($jail));
            $suspect->poke("[COLOR=red][b] You have been put in jail by {$this->event['invokername']}");
            $suspect->message("[COLOR=red][b] You have been put in jail by {$this->event['invokername']}");
            $suspect->message("[COLOR=red][b] They will explain what you have done wrong and decide the best course of action.");
            $suspect->message("[COLOR=red][b] If you think you have been wrongly jailed or thought the outcome was hursh, Please post an appeal on out forums.");
            $suspect->message("[COLOR=red][b] [url]http://aggressivegaming.org[/url] Don't complain to other staff members on teamspeak.");
            $suspect->message("[COLOR=red][b] Due to the shortage of robots, Some of our staff are human and may act unpredictably when abused");
        }catch(Exception $e){
            echo $e->getMessage();
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

    private function startsWith($pattern, $string)
    {
        return (substr($string, 0, strlen($pattern)) == $pattern) ? TRUE : FALSE;
    }

    private function ClientInfo()
    {
        list($command, $user) = explode(' ', $this->event['msg']);
        $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
        $info = $this->Teamspeak3Host->clientInfoDb($this->Teamspeak3Host->clientFindDb($user));

        $Client->message("[COLOR=blue][B]{$user}: Database ID  {$info["client_database_id"]}[/COLOR]");
        $Client->message("[COLOR=blue][B]{$user}: Unique ID  {$info["client_unique_identifier"]}[/COLOR]");
        $Client->message("[COLOR=blue][B]{$user}: Joined  ".date("F j, Y, g:i a",$info["client_created"])."[/COLOR]");
        $Client->message("[COLOR=blue][B]{$user}: Last connection  ". date("F j, Y, g:i a",$info["client_lastconnected"])."[/COLOR]");
        $Client->message("[COLOR=blue][B]{$user}: Total connections  {$info["client_totalconnections"]}[/COLOR]");
        $Client->message("[COLOR=blue][B]{$user}: Client description  {$info["client_description"]}[/COLOR]");
        $Client->message("[COLOR=blue][B]{$user}: Last IP  {$info["client_lastip"]}[/COLOR]");
    }

    private function help()
    {
        $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
        $Client->message("[color=green] [b]----- Server Commands -----");
        $Client->message("[color=green] [b]Command !help:[/b] Display this help message.");
        $Client->message("[color=green] [b]Command !dchannel:[/b] Create a donator channel for a user.");
        $Client->message("[color=green] [b]Command !version:[/b] Display the bot version.");
        $Client->message("[color=green] [b]Command !clientinfo:[/b] View the collected information of a user online Or offline.");
        $Client->message("[color=green] [b]----- Channel Commands -----");
        $Client->message("[color=green] [b]Command !tchannel:[/b] Request a temporary channel, will be deleted after 30 minutes.");
        $Client->message("[color=green] [b]Command !validate:[/b] Validate you forum login to get member permissions. (Not complete)");
        $Client->message("[color=green] [b]Command !help:[/b] Display channel only help message.");
        $Client->message("[color=green] [b]----- Private Commands -----");
        $Client->message("[color=green] [b]Command !help:[/b] Display private message only help message.");
    }

    private function PrivateHelp()
    {
        $Client = $this->Teamspeak3Host->clientGetByName($this->event['invokername']);
        $Client->message("[color=green] [b]----- Server Commands -----");
        $Client->message("[color=green] [b]Command !help:[/b] Display this help message.");
    }

    public function sendBotChannelMessage($message)
    {
        $this->BotChannel->message($message);
    }

}