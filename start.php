<?php
//Includes

require_once("libraries/TeamSpeak3/TeamSpeak3.php");
require_once("TeamspeakBot.php");
require_once("events/events.php");
require_once("TextMessages.php");

$Bot = NEW Teamspeak3Bot("127.0.0.1","Username","Password", "AGNBot2.0");
//$TS3Host = $Bot->getTeamspeak3Host();
//$msg = new TextMessages($event, $TS3Host);

$Bot->Start();

