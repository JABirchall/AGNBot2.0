<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:24
 * @param TeamSpeak3_Adapter_ServerQuery_Event $event
 * @param TeamSpeak3_Node_Host $host
 */

function onTextMessage(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
{
    echo "[SIGNAL] Client " . $event["invokername"] . " sent textmessage: " . $event["msg"] . "\n"; // Why you d

    $msg = new TextMessages($event, $host->serverGetByPort(9987));
    //$msg->event = $event;


    //var_dump($event->getData());

    switch(@$msg->event->getData()["targetmode"])
    {
        case 1: echo "[DEBUG] Private message\n"; $msg->PrivateMessageHandler();break;
        case 2: echo "[DEBUG] Channel message\n"; $msg->ChannelMessageHandler();break;
        case 3: echo "[DEBUG] Server message\n"; $msg->ServerMessageHandler();break;
        default: return;
    }

    //$event->getData();

}

