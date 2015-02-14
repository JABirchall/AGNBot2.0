<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:25
 * @param TeamSpeak3_Node_Host $host
 */

function onSelect(TeamSpeak3_Node_Host $host)
{
    echo "[SIGNAL] Selected virtual server with ID {$host->serverSelectedId()} running on port {$host->serverSelectedPort()}\n";
}