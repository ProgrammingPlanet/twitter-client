<?php
	
	require 'twitteroauth/autoload.php';
    require 'conf.php';

    use TwitterOAuth\TwitterOAuth;

	$tw = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET,ACCESS_TOKEN,ACCESS_TOKEN_SECRET);

    // $media1 = $tw->upload('media/upload',['media'=>'assets/twitter.png'])->media_id_string;
    // $media2 = $tw->upload('media/upload',['media'=>'assets/twitter1.png']);

    if(isset($_POST['s']))
    {
        print_r($_FILES);
        exit;
    }

    /*$parameters = [
        'status' => "i'm robot of him.",
        'media_ids' => implode(',', [$media1->media_id_string, $media2->media_id_string])
    ];*/

    // print_r($media1);

    // $result = $tw->post('statuses/update', $parameters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>sdfsdf</title>
    <script type="text/javascript" src="assets/jquery.min.js"></script>
</head>
<body>
    <!-- <input type="file" id="tmpfile"> -->

    <form id="imagesform0" action="./test.php" method="post" enctype="multipart/form-data">
        <input type="file" name="x[]">
        <input type="file" name="x[]">
        <input type="submit" name="s">
    </form>
    <br>
    <div id="imgs">
        
    </div>
    <img src="" id="i1" width="300">
</body>
</html>
<script>

    function imagesPreview(input,imgobj)
    {
        if(input.files && input.files[0]){
            var reader = new FileReader();
            reader.onload = function (e) {
                imgobj.attr('src',e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    /*function imagesPreview(inputobj,imgobj)
    {

        if(input.files)
        {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                        var image = new Image();
                        image.width = 250;
                        image.src    = event.target.result;
                        $(placeToInsertImagePreview).appendChild(image);
                    // $($.parseHTML('<img style="width:250px">')).attr('src',event.target.result).appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }

    }*/

    $('#imagesform').on('change',function(){
        // imagesPreview(this, '.gallery');
        $.each(this,function(i,v){
            // console.log(v,$('#i1'));
            imagesPreview(v,$('#i1'));

        })
        

    });

    $('#tmpfile').on('change',function(){

        var f = $(this).clone();
        f.attr('id','tmp1');
        
        $("#imagesform").append(f);

    })
    /*var img = $("#firstDiv").children("img").clone();
    $("#secondDiv").append(img);*/

</script>