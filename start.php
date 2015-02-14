<?php
//Includes

require_once("libraries/TeamSpeak3/TeamSpeak3.php");
require_once("TeamspeakBot.php");
require_once("events/events.php");
require_once("TextMessages.php");

$Bot = NEW Teamspeak3Bot("142.4.205.65","AGNbot","dBcGwFR3", "AGNBot2.0");
$TS3Host = $bot->getTeamspeak3Host();
$msg = new TextMessages($event, $TS3Host);


$Bot->Start();

