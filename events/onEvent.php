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

    //$server->client

}