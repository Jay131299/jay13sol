<?php

/**
 * Class Token generation
 */
class Token 
{
    /**
     * Generate ramdom token for verification 
     * 
     * @param string $email
     * @return string $accessToken
     */
    public function getToken($email)
    {
        $accessToken = bin2hex(random_bytes(18));
        $accessToken .= time().date('Ymd',time());
        setcookie('token',$accessToken,time() + (60*60), '/');
        setcookie('user',$email,time() + (60*60), '/');
        return $accessToken;
    }
}