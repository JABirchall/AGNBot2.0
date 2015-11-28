<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:21
 * @param $seconds
 * @param TeamSpeak3_Adapter_ServerQuery $adapter
 * @throws TeamSpeak3_Adapter_Exception
 * @throws TeamSpeak3_Adapter_ServerQuery_Exception
 */

function onTimeout($seconds, TeamSpeak3_Adapter_ServerQuery $adapter)
{
    if($adapter->getQueryLastTimestamp() < time()-300) $adapter->request("clientupdate");
    $server = $adapter->getHost()->serverGetByPort(9987);
    $serverInfo = $server->getInfo();
    $totalPacketloss = (float)$serverInfo["virtualserver_total_packetloss_total"]->toString() *100;

    if($totalPacketloss >= 49.9999)
        $server->message("[COLOR=red][B]The server is for being DDOS'D! (Average packet loss {$totalPacketloss}%[/COLOR]");
    else if($totalPacketloss >= 29.9999)
        $server->message("[COLOR=red][B]The server is experiencing alot of lagg. (Average packet loss {$totalPacketloss}%[/COLOR]");
    else if($totalPacketloss >= 18.9999)
        $server->message("[COLOR=orange][B]The server is experiencing moderate lagg. (Average packet loss {$totalPacketloss}%[/COLOR]");
    else if($totalPacketloss >= 9.9999)
        $server->message("[COLOR=orange][B]The server is experiencing minor lagg. (Average packet loss {$totalPacketloss}%)[/COLOR]");
}