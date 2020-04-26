<?php

    require 'twitteroauth/autoload.php';
    require 'conf.php';
    require 'dbcon.php';

    use TwitterOAuth\TwitterOAuth;

    session_start();

    $cookie_name = COOKIE_NAME;
    $cookie_time = 86400*30;  //30 days

    function RandomString($length=10)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',ceil($length/strlen($x)))),1,$length);
    }

    function genrate_login_url()
    {
        // connect to twitter with our app creds
        $tw = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET);

        //include_once('proxy.php');

        // get a request token from twitter
        $req_token = $tw->oauth('oauth/request_token',['oauth_callback'=>OAUTH_CALLBACK]);
        // save twitter token info to the session for verify access token
        $_SESSION['oauth_token'] = $req_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $req_token['oauth_token_secret'];

        $url = $tw->url('oauth/authorize',['oauth_token'=>$req_token['oauth_token']]);

        return $url;
    }

    function handle_callback($verifier)
    {
        // setup connection to twitter with request token
        $tw = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET,$_SESSION['oauth_token'],$_SESSION['oauth_token_secret']);

        //include_once('proxy.php');
        
        // get an access token
        $access_token = $tw->oauth('oauth/access_token',['oauth_verifier'=>$verifier]);

        return $access_token;
    }

    function attempt_login($acc_token,$acc_secret)
    {
        $tw = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET,$acc_token,$acc_secret);

        //include_once('proxy.php');
        
        $user = $tw->get('account/verify_credentials',['include_email'=>'true']);

        if(property_exists($user,'errors')) {echo 'error';return FALSE;} // not loged in
            
        return TRUE;
    }

    function cookie_login()
    {
        global $db,$cookie_name;

        if(isset($_COOKIE[$cookie_name]))
        {
            $cookie_val = $_COOKIE[$cookie_name];
            $q = "SELECT access_token as token,access_secret as secret FROM users WHERE cookie='$cookie_val'";
            $data = $db->query($q)->fetch(PDO::FETCH_ASSOC);
            if($data)
            {
                if(attempt_login($data['token'],$data['secret']))
                {
                    $_SESSION['twitter'] = ['token'=>$data['token'],'secret'=>$data['secret']];
                    return TRUE;
                }
            }
        }

        return FALSE;
    }


    if(isset($_SESSION['twitter']))//is logged in by session
    {
        header('Location: ./index.php');
    }
    elseif(cookie_login())   //cookie login
    {
        header('Location: ./index.php');
    }
    elseif(isset($_GET['oauth_verifier']) && isset($_GET['oauth_token']) && isset($_SESSION['oauth_token'])) //from callback
    {
        if($_GET['oauth_token'] == $_SESSION['oauth_token']) //valid callback
        {
            $access = handle_callback($_GET['oauth_verifier']);
            $_SESSION['twitter'] = ['token'=>$access['oauth_token'],'secret'=>$access['oauth_token_secret']];
            
            do{
                $cv = RandomString(20);
                $q = "INSERT INTO users(cookie,access_token,access_secret,last_login) VALUES('$cv','".$access['oauth_token']."','".$access['oauth_token_secret']."','".time()."')";
            }while(!$db->query($q));

            setcookie($cookie_name,$cv,time()+$cookie_time,'/');
            
            header('Location: ./index.php');
        }
        else
            die('opps!, invalid callback. please refresh this page');
    }
    else //not log in
    {
        unset($_SESSION['twitter']);
        $loginurl = genrate_login_url();
        header('Location: '.$loginurl);
        // echo 'login url: <a target="_blank" href="'.$loginurl.'">login with twitter</a>';
    }
