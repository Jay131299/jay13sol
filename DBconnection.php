<?php
//Database Connection
class DBconnection
{
    /**
     * @return Connection
     */
    function connect()
    {
        $connection=new mysqli("ServerName","UserId","Password","DataBaseName");
        return $connection;
    }
}
?>
