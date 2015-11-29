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
    $totalPacketLoss = (float)$serverInfo["virtualserver_total_packetloss_total"]->toString() *100;

    if($totalPacketLoss >= 49.9999)
        $server->message("[COLOR=red][B]The server is for being DDOS'D! (Average packet loss {$totalPacketLoss}%)[/COLOR]");
    else if($totalPacketLoss >= 29.9999)
        $server->message("[COLOR=red][B]The server is experiencing alot of lagg. (Average packet loss {$totalPacketLoss}%)[/COLOR]");
    else if($totalPacketLoss >= 18.9999)
        $server->message("[COLOR=orange][B]The server is experiencing moderate lagg. (Average packet loss {$totalPacketLoss}%)[/COLOR]");
    else if($totalPacketLoss >= 9.9999)
        $server->message("[COLOR=orange][B]The server is experiencing minor lagg. (Average packet loss {$totalPacketLoss}%)[/COLOR]");

    $server->clientListReset();
    $server->channelListReset();
    //$channel = $server->channelGetByName("[cspacer0]Aggressive Gaming Teamspeak");
    //$online = $server->clientCount();
    //$channel->modify([
    //    "channel_description" => "Make sure you read the rules.\nUpdate information is in the info channel.\nDonating helps support the server costs\n\n[COLOR=Blue]Online information:\n\t[b]Users online: {$online}"
    //]);


    $staffList = $server->serverGroupClientList(162);
    $staffList += $server->serverGroupClientList(163);
    $staffList += $server->serverGroupClientList(185);
    $staffList += $server->serverGroupClientList(191);
    $staffList += $server->serverGroupClientList(164);
    $staffList += $server->serverGroupClientList(165);
    $staffList += $server->serverGroupClientList(166);
    $staffList += $server->serverGroupClientList(167);
    $staffList += $server->serverGroupClientList(183);
    $staffList += $server->serverGroupClientList(186);
    $staffList += $server->serverGroupClientList(167);
    $staffList += $server->serverGroupClientList(189);
    $staffList += $server->serverGroupClientList(198);
    $staffList += $server->serverGroupClientList(200);
    $staffList += $server->serverGroupClientList(201);
    $staffList += $server->serverGroupClientList(203);
    $staffList += $server->serverGroupClientList(171);
    try {$complaints = $server->complaintList();}catch(Exception $e){}

    foreach ($complaints as $complaint) {
        foreach ($staffList as $staff) {
            try {
                $staff = $server->clientGetByName($staff["client_nickname"]);
                $staff->message("[COLOR=blue][B]Hey {$staff["client_nickname"]}, [U]{$complaint['fname']}[/U] has complained about [U]{$complaint['tname']}[/U] for \"{$complaint['message']}\" at " . date('Y-m-d H:i:s', $complaint['timestamp']) . "[B][/COLOR]\n");
            }catch(Exception $e){
                //echo "[ERROR]  " . $e->getMessage() . "\nClient most likely not online\n";
            }
        }
        var_dump($complaint);
        $server->complaintDelete($complaint["tcldbid"],$complaint["fcldbid"]);
    }
}