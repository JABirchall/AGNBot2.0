<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:17
 */

function onConnect(TeamSpeak3_Adapter_ServerQuery $adapter)
{
    echo "[INFO] Successfully connected to {$adapter->getHost()}";
    Teamspeak3Bot::$Connected = true;
}