<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 13/02/2015
 * Time: 06:23
 * @param TeamSpeak3_Node_Host $host
 */

function onLogin(TeamSpeak3_Node_Host $host)
{
    echo "[SIGNAL] Authenticated as user " . $host->whoamiGet("client_login_name") . "\n";
}