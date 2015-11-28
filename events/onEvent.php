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

    for($i = 0; $i <= count(@$_SESSION); $i++)
    {
        //if(isset($_SESSION[$i]['cid'])) echo "{$_SESSION[$i]['expire']} : ".time()."\n";

        if(isset($_SESSION[$i]['cid']) && $_SESSION[$i]['expire'] <= time()){
            $tempchannel = $_SESSION[$i];
            unset($_SESSION[$i]);
            echo "[INFO] Channel ID {$tempchannel['cid']} has expired";
            $msg = new TextMessages($event, $host->serverGetByPort(9987));
            $msg->DeleteTempChannel($tempchannel['cid']);
        }
    }


    $server = $host->serverGetByPort(9987);

    //$server->client

}