<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:30
 * @param TeamSpeak3_Adapter_ServerQuery_Event $event
 * @param TeamSpeak3_Node_Host $host
 */

function onEvent(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
{
    echo "[SIGNAL] Received notification " . $event->getType() . "\n";

    $server = $host->serverGetByPort(9987);

    if($event->getType()->toString() === "cliententerview" && $event->getData()["client_unique_identifier"] != "ServerQuery"){
        //var_dump($event->getData());
        $channel = $server->channelGetByName("[cspacer0]Aggressive Gaming Teamspeak");
        $online = $server->clientCount();
        $channel->modify([
            "channel_description" => "Make sure you read the rules.\n
            Update information is in the info channel.\n
            Donating helps support the server costs\n
            [COLOR=Blue]Online information:\n
            [b]Users online:[/b] {$online}.\n\t
            [b]Last User connected:[/b] {$event->getData()["client_nickname"]}"
        ]);
    }

}

