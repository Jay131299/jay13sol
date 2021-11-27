<?php

/**
 * Class Mail random comics mail
 */
class Sendcomics                
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
     * Send Comic to all Subscribers
     */
    public function sendMailAllSubscribers()
    {
        $sql='SELECT * FROM subscriber_list';
        $queryRun = mysqli_query($this->connection,$sql);
        $fetchEmails = '';
        $protocol = '';
        try {
            if (!empty($_SERVER['SERVER_PROTOCOL']) && !empty($_SERVER['SERVER_NAME'])) {
                $protocol = explode("/",$_SERVER['SERVER_PROTOCOL']);
                $protocol[0] = strtolower($protocol[0]);
            }
            while ($fetchEmails = mysqli_fetch_array($queryRun)) {
                
                $imgUrl = $this->getComic();
                $filename = $imgUrl;
                $fname = 'xkcd.png';
                $subject = 'XKCD-Daily Comics';  
                $fromConfig = file_get_contents(__DIR__.'/fromConfig.json');
                $fromData = json_decode($json, true); 
                $from = $fromData['fromEmailAddress']['fromName'].'-'.$fromData['fromEmailAddress']['fromEmail'];
                
                $unSubToken = $fetchEmails['unsub'];
                $to = $fetchEmails['subs_email'];
                
                $headers = "From: $from"; 
                
                $semi_rand = md5(time()); 
                $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
                
                $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
                
                $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . 
                                "<html> 
                                <head> 
                                    <title>Good Day to You</title> 
                                </head> 
                                <body> 
                                    <h1>Here is your comic!!!</h1> 
                                    <h2>Have a fun and enjoy the comics</h2><br>
                                    <a href=\"".$protocol[0]."://".$_SERVER['SERVER_NAME']."/Unsubscribe.php?unsubtoken='$unSubToken'\" style='font-size:15px;'>Unsubscribe</a>
                                    <h1>Comic</h1><br>
                                    <img src='$imgUrl' alt='$imgUrl'/>
                                    </body>
                                </html>" . "\n\n"; 
                $message .= "--{$mime_boundary}\n";
                
                $message .= "--{$mime_boundary}\n";
                
                $data = file_get_contents($filename);
                $data = chunk_split(base64_encode($data));
                
                $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"".$fname."\"\n"."Content-Disposition: attachment;\n" . " filename=\"$fname\"\n"."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                $message .= "--{$mime_boundary}--\n";
             
                if (!mail($to,$subject,$message,$headers,"-f " . $from)) {
                    echo 'Mail not able to send due to technical issues, will fix it shortly!';
                } else {    
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                    exit;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Fetch Random comics images
     * 
     * @param string $email
     * @return string
     */
    public function getComic()
    {
        $metaData = get_headers('https://c.xkcd.com/random/comic/')[7];
        $imgUrl = explode(" ", $metaData);
        $jsonData = file_get_contents($imgUrl[1] . '/info.0.json');
        $jsonDecode = json_decode($jsonData);
        return $jsonDecode->img;
    }
}

$subs = new Sendcomics();
$subs->sendMailAllSubscribers();

