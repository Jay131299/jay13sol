<?php

/**
 * Class Unsubscribe the user
 */
class Unsubscribe
{

    /**
     * @var DBconnection
     */
    protected $connection;

    public function __construct() {
        
        include __DIR__.'/DBconnection.php';
        $conn = new DBconnection();
        $this->connection = $conn->connect();
    }
    
    /**
     * Unsubscribe the user
     * 
     * @param string $token
     */
    public function unSub($token)
    {
        try {
            $deleteSub = $this->connection->prepare('DELETE FROM subscriber_list WHERE unsub=?');
            $deleteSub->bind_param('s',$token);
            $execute = $deleteSub->execute();
            if ($execute) {
                setcookie('subscribed','You Have successfully Unsubscription the Xkcd Comics...',time() + 3, '/');
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                    exit;
                }
            }
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!empty($_GET['unsubtoken'])) {
    
    $data = str_replace("'", "", $_GET['unsubtoken']);
    $subs = new Unsubscribe();
    $subs->unSub($data);
}