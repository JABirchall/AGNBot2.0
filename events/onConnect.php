<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:17
 * @param TeamSpeak3_Adapter_ServerQuery $adapter
 */

function onConnect(TeamSpeak3_Adapter_ServerQuery $adapter)
{
    echo "[INFO] Successfully connected to {$adapter->getHost()}";
}