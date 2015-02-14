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

}