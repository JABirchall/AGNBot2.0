<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 05:51
 */
define("VERSION", "1.2.1 ALPHA1");
class Teamspeak3Bot extends TeamSpeak3 {

    protected $IP;
    protected $Username;
    protected $Password;
    public $Teamspeak3Host;
    public $BotChannel;
    public $ClientList;
    public $WhoAmI;
    public $Connected = false;

    public function __construct($ip, $username, $password, $name = "Bot")
    {
        $this->IP = $ip;
        $this->Username = $username;
        $this->Password = $password;

        try
        {
            /* connect to server, login and get TeamSpeak3_Node_Host object by URI */
            $this->Teamspeak3Host = $this::factory("serverquery://{$this->Username}:{$this->Password}@{$this->IP}:10011/?server_port=9987&blocking=0&nickname={$name}");
            /* subscribe to various events */
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("serverqueryConnected", "onConnect");
            //TeamSpeak3_Helper_Signal::getInstance()->subscribe("serverqueryCommandStarted", "onCommand");
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("serverqueryWaitTimeout", "onTimeout");
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyLogin", "onLogin");
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyEvent", "onEvent");
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyTextmessage", "onTextMessage");
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyServerselected", "onSelect");
            /* register for all events available */
            $this->Teamspeak3Host->notifyRegister("server");
            $this->Teamspeak3Host->notifyRegister("channel");
            $this->Teamspeak3Host->notifyRegister("textserver");
            $this->Teamspeak3Host->notifyRegister("textchannel");
            $this->Teamspeak3Host->notifyRegister("textprivate");

            $this->BotChannel = $this->Teamspeak3Host->channelGetByName("[ Private Administration Room + Bots ]");
            $this->Teamspeak3Host->clientMove($this->Teamspeak3Host->whoamiGet("client_id"), $this->BotChannel);

            $_SESSION[] = $this->Connected = true;
            echo "[INFO] Connected\n";
        }
        catch(Exception $e)
        {
            $this->Connected = false;
            echo "Failed\n";
            die("[ERROR]  " . $e->getMessage() . "\n". $e->getTraceAsString() ."\n");
        }
    }

    public function Start()
    {
        $this->BotChannel->message("AGNBot 2.0 Starting!");
        while(true)
            $this->Run();
    }

    public function Run()
    {
        $this->Teamspeak3Host->getAdapter()->wait();
    }

    public function ChannelMessage($msg)
    {
        $this->BotChannel->message($msg);
    }

    public function ServerMessage($msg)
    {
        $this->Teamspeak3Host->message($msg);

    }

    public function privateMessage($msg)
    {
        //$this->Teamspeak3Host->client;
    }

    /**
     * @return TeamSpeak3_Adapter_Abstract
     */
    public function getTeamspeak3Host()
    {
        return $this->Teamspeak3Host;
    }



} 