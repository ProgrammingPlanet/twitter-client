<?php
	
	require 'twitteroauth/autoload.php';
	require 'dbcon.php';
	require 'conf.php';

	use TwitterOAuth\TwitterOAuth;

	session_start();
	header('Content-Type: text/json');

	if(!isset($_REQUEST['op']) || !isset($_SESSION['twitter'])) 
		die(json_encode(['status'=>0,'msg'=>'login requred.']));

	$op = $_REQUEST['op'];
	$cookie_name = COOKIE_NAME;

	$tw = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET,$_SESSION['twitter']['token'],$_SESSION['twitter']['secret']);

	$tw->setTimeouts(10,15);

	if($op=='tweet')
	{
		$msg = $_REQUEST['msg'];

		die(json_encode($_FILES['imgs']));
		
		if(count($_FILES['imgs']['name'])>4)
			die(json_encode(['status'=>0,'msg'=>"can't upload more than 4 images."]));

		$medias = [];
		foreach($_FILES['imgs']['name'] as $i => $img)
		{
			$medias[] = $tw->upload('media/upload',['media'=>$_FILES['imgs']['tmp_name'][$i]])->media_id_string;
		}
		$tweet = $tw->post('statuses/update',['status'=>$msg,'media_ids'=>implode(',',$medias)]);

		if(isset($tweet->id))	//tweet sended successfully
		{

			$result = ['status'=>1,'id'=>$tweet->id_str,'username'=>$tweet->user->screen_name];
		}
		elseif(isset($tweet->errors)){
			$result = ['status'=>0,'msg'=>$tweet->errors[0]->message];
		}
		echo json_encode($result);
		// print_r($tweet);
	}

	if($op=='logout')
	{
		$cookie_val = $_COOKIE[$cookie_name];
		$q = "DELETE FROM users WHERE cookie='$cookie_val'";
		if($db->query($q)) echo json_encode(['status'=>1,'msg'=>'logout successfully.']);
		else echo json_encode(['status'=>0,'msg'=>'error, try again.']);
		unset($_SESSION['twitter'],$_COOKIE[$cookie_name]);
	}

	// $statuses = $tw->get("statuses/home_timeline", ["count" => 2, "exclude_replies" => true]);

	// $statues = $tw->post("statuses/update", ["status" =>$msg]);