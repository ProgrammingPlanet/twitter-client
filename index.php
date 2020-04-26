<?php

	require 'twitteroauth/autoload.php';
	require 'conf.php';

	use TwitterOAuth\TwitterOAuth;

	session_start();

	$loggedin = TRUE;

	if(!isset($_SESSION['twitter']))
	{
		$loggedin = FALSE;
	}
	else
	{
		$tw = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET,$_SESSION['twitter']['token'],$_SESSION['twitter']['secret']);

		$user = $tw->get('account/verify_credentials',['include_email'=>'true']);
	}

		

	$msg = "hey, i'm the robot 🤖 of him, don't take it too serious.";

	

	// echo '<a href="ajax.php?op=logout">logout</a><br>';

	

	// print_r($user); exit;

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Twitter For </title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="assets/bootstrap.min.css">
	<script type="text/javascript" src="assets/jquery.min.js"></script>
	<script type="text/javascript" src="assets/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/sweetalert.min.js"></script>
	<style>
		.btn-img {
		  position: absolute;
		  z-index: 1;
		  padding: 0px 0.2rem;
		  line-height: 1.2rem;

		  background-color: yellow;
		}
	</style>
</head>
<body>
	<br>
	<div class="container ">
		<?php if(!$loggedin): ?>
		<div class="text-center my-5">
			<a href="auth.php">
				<img src="assets/twitter.png" class="img-thumbnail" style="height:50px;">
			</a>
		</div>
		<?php else: ?>
		<div class="container py-3 bg-dark text-white" id="header"><!--  -->
			<div class="row">
				<div class="col-md-6 my-2">
					<div class="row">
						<div class="col-3 col-sm-2 col-md-2">
							<img src="<?=$user->profile_image_url?>" class="rounded-circle">
						</div>
						<div class="col-9 col-sm-10 col-md-10 text-truncate">
							<small>
								<strong><?=$user->name?></strong><br>
								<a href="http://twitter.com/<?=$user->screen_name?>" target="_blank">@<?=$user->screen_name?></a>
							</small>
						</div>
					</div>
				</div>
				<div class="col-md-6 my-2">
					<div class="row">
						<div class="col-8">
							<small>
								<strong>Followers : </strong><?=$user->followers_count?><br>
								<strong>Following : </strong><?=$user->friends_count?>
							</small>
						</div>
						<div class="col-4 my-auto">
							<button class="btn btn-sm btn-outline-danger float-right" onclick="logout()">logout</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container my-2 border" id="body">
			<div class="row">
				<div class="col-md-8 mx-auto">
					<div class="container my-1">
						<div id="images" class="row">
							<form id="imgsform" class="col-11 px-0">
								<!-- <div class="col-2 px-0 mx-1">
									<span class="btn btn-img bg" onclick="$(this).parent().remove()">x</span>
									<img src="http://placehold.it/120x150" class="img-fluid">
								</div> -->
							</form>
							<div class="col-1">
								<input type="file" class="d-none" id="tmpfile" onchange="imgsview($(this)[0])">
								<button class="btn btn-info rounded-circle py-1" onclick="$('#tmpfile').click()">
									+
								</button>
							</div>
						</div>
						
					</div>
					<div class="text-center my-2">
						<textarea class="form-control my-2" cols="31" rows="7" id="tweettext"></textarea>
						<button class="btn btn-sm px-4 btn-outline-success" onclick="sendtweet(this)">tweet</button>
					</div>
				</div>
				<div class="col-md-4 border-left">
					
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<img src="" id="i123">
</body>
</html>

<script>
	
	function sendtweet(btnobj)
	{
		var msg = $('#tweettext');
		var fd = new FormData($('#imgsform')[0]); 
 
		fd.append('op','tweet');
		fd.append('msg',msg.val());

		$(btnobj).text('processing...').prop('disabled',true);

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: fd,
		  	processData: false,
		  	contentType: false,
			success:function(result){
				// return console.log(result);
				if(result.status)
				{
					swal.fire({
					  title: 'Tweet Sended.',
					  html: `<a href="https://twitter.com/${result.username}/status/${result.id}" target="_blank">See On Twitter</a>`,
					  icon: 'success'
					});
					msg.val('');
				}
				else{
					swal.fire({
					  title: 'Error Occured.',
					  text: result.msg,
					  icon: 'error'
					});
				}
			},
			error:function(a){
				console.log(a);
			},
			complete:function(){
				$(btnobj).text('tweet').prop('disabled',false);
			}
		});
	}

	function logout()
	{
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {op: 'logout'},
			success:function(result){
				// console.log(result);
				if(result.status) window.location.reload();
			},
			error:function(a,b,c){
				console.log(a,b,c);
			},
			complete:function(){
				// console.log('complete');
			}
		});
	}

	function imagesPreview(input,imgobj)
    {
        if(input.files && input.files[0]){
            var reader = new FileReader();
            reader.onload = function(e){
                imgobj.attr('src',e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function imgsview(obj)
    {
    	if($(obj)[0].files[0])
    	{
    		var el = $.parseHTML(`<div class="col-2 px-0 mx-1 d-inline-block">
						<span class="btn btn-img rounded-circle m-1" onclick="$(this).parent().remove()">x</span>
						<img src="" class="img-thumbnail">
					</div>`);
			
    		var f = $(obj).clone().attr({name:'imgs[]',class:'d-none'}).removeAttr('id onchange');

    		imagesPreview(f[0],$(el).find('img'));
    		$('#imgsform').append($(el).append(f));
    	}
    }

</script>