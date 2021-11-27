<?php

/**
 * Class send verification mail to subscriber
 */
class Subscriber
{

    /**
     * @var DBconnection
     */
    public $connection;
    
    /**
     * @var Token
     */
    protected $token;
    
    public function __construct() {
        
        include __DIR__.'/DBconnection.php';
        include __DIR__.'/Token.php';
        
        $conn = new DBconnection();
        $this->connection = $conn->connect();
        
        $this->token = new Token();   
    }
    
    /**
     * Check Email existens
     * 
     * @param string $email 
     * @return Boolean
     */
    public function checkEmailExist($email)
    {
        $checkEmailQuery = $this->connection->prepare('SELECT subs_id FROM subscriber_list WHERE subs_email = ?');
        $checkEmailQuery->bind_pram('s',$email);
        $runQuery = $checkEmailQuery->execute();
        if ($runQuery) {
            $result = $checkEmailQuery->get_result();
            if ($result->fetch_assoc() != null) {
                return false;
            } else {
                return true;
            }
        } 
    }
    
    /**
     * Send verification mail
     * 
     * @param string $email
     */
    public function sendVerificationMail($email)
    {
        
        if ($this->checkEmailExist($email)) {
            $token = $this->token->getToken($email);
            $protocol = '';
            try {
                if (!empty($_SERVER['SERVER_PROTOCOL']) && !empty($_SERVER['SERVER_NAME'])) {
                    $protocol = explode('/',$_SERVER['SERVER_PROTOCOL']);
                    $protocol[0] = strtolower($protocol[0]);
                }

                setcookie('subscribed','You Have to confirm your Subscription by verification mail!',time() + 3, '/');
                $to = $email;
                $subject = 'Email Verification XKCD Comics';
                $content = "
                    <center><h1>Greetings....</h1>                
                    <h3>Hope you are doing well and safe in pandemic situation....</h3></center>
                    <p>Please verify your email address by cliking on below link..</p>
                    <a href=\"".$protocol[0]."://".$_SERVER['SERVER_NAME']."='$token'\" style='font-size:15px;'>Verify Email</a>";
            
                $fromConfig = file_get_contents(__DIR__.'/fromConfig.json');
                $fromData = json_decode($json, true);

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From : '.$fromData['fromEmailAddress']['fromName'].'-'.$fromData['fromEmailAddress']['fromEmail'];

                $sent = mail($to,$subject,$content,$headers);
                if (!$sent) {
                    setcookie('subscribed','There is an problem in sending verification mail, We will send you mail very soon!',time() + 3, '/');
                    if (!empty($_SERVER['HTTP_REFERER'])) {
                        header('Location:'.$_SERVER['HTTP_REFERER']);
                        exit;
                    }
                    
                } else {    
                    if (!empty($_SERVER['HTTP_REFERER'])) {
                        header('Location:'.$_SERVER['HTTP_REFERER']);
                        exit;
                    }
                }
            }
            catch (Exception $e) {
                echo $e->getMessage(); 
            }
        } else {
            setcookie('subscribed','You Have already subscribed successfully the XKCD Comics!',time() + 3, '/');
            header('Location:'.$_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    /**
     * Server Side Validation
     * 
     * @param string $email
     */
    public function Validation($email)
    {
        if (preg_match("/^[a-zA-Z0-9\-_]+(\.[a-zA-Z0-9\-_]+)*@[a-z0-9]+(\-[a-z0-9]+)*(\.[a-z0-9]+(\-[a-z0-9]+)*)*\.[a-z]{2,4}$/",$email)) {
            $this->sendVerificationMail($email);
            
        } else {
            echo 'Please enter valid email address';
        }
    }
}
if (!empty($_POST['email'])) {
    $subsEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subs = new Subscriber();
    $subs->Validation($subsEmail);
}