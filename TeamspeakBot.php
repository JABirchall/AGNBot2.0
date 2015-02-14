<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 05:51
 */

class Teamspeak3Bot extends TeamSpeak3 {

    protected $IP;
    protected $Username;
    protected $Password;
    public $Teamspeak3Host;
    public $BotChannel;
    public $ClientList;
    public $WhoAmI;
    public $Connected = false;

    public function __construct($ip, $username, $password, $name = NULL)
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
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyTextmessage", "onTextmessage");
            TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyServerselected", "onSelect");
            /* register for all events available */
            $this->Teamspeak3Host->notifyRegister("server");
            $this->Teamspeak3Host->notifyRegister("channel");
            $this->Teamspeak3Host->notifyRegister("textserver");
            $this->Teamspeak3Host->notifyRegister("textchannel");
            $this->Teamspeak3Host->notifyRegister("textprivate");



            $this->BotChannel = $this->Teamspeak3Host->channelGetByName("[cspacer632]Bot House(Admins only)");
            $this->Teamspeak3Host->clientMove($this->Teamspeak3Host->whoamiGet("client_id"), $this->BotChannel);

            $this->Connected = true;
        }
        catch(Exception $e)
        {
            $this->Connected = false;
            die("[ERROR]  " . $e->getMessage() . "\n". $e->getTraceAsString() ."\n");
        }
    }

    public function Start()
    {
        $this->BotChannel->message("AGNBot 2.0 Starting!");
        if($this->Connected === true) $this::Run();

    }

    public function Run()
    {
        while($this->Connected === true) $this->Teamspeak3Host->getAdapter()->wait();
    }

    public function ChannelMessage($msg)
    {
        $this->BotChannel->message($msg); // Fatal error: Call to a member function message() on a non-object in
                                         //C:\Users\DrWhat\Documents\AGNBot2.0\TeamspeakBot.php on line 72
    }

    public function ServerMessage($msg)
    {
        $this->Teamspeak3Host->message($msg); // Fatal error: Call to a member function message() on a non-object in
        //C:\Users\DrWhat\Documents\AGNBot2.0\TeamspeakBot.php on line 72
    }

    /**
     * @return TeamSpeak3_Adapter_Abstract
     */
    public function getTeamspeak3Host()
    {
        return $this->Teamspeak3Host;
    }



} 