<?php
if ( isset( $_POST ) ){
    $uploaddir = TAOH_PLUGIN_PATH.'/cache/bulk/';
    if (!file_exists($uploaddir)) {
        mkdir( $uploaddir,0777,false );
    }
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

   // echo"==========".$uploadfile;

    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        //$filename = $uploaddir.'leadership.php';
        if (file_exists($uploadfile)) {

            $extension = pathinfo($uploadfile, PATHINFO_EXTENSION);
            //echo"==========".$extension;
            $type = $_POST['type'];
            $global = $_POST['global'];


            if($extension == 'php'){

                header('Content-type: application/json');

                $json = file_get_contents($uploadfile);

                //print_r( $json);
                $json_de = json_decode($json);

                //print_r( $json_de);

                foreach($json_de as $key => $value){

                    foreach($value as $k=>$v){
                        if($k == 'title') $title = $v;
                        if($k == 'category') $category = $v;
                        if($k == 'pod') $pod = $v;
                        if($k == 'body') $body = $v;
                        if($k == 'excerpt') $excerpt = $v;
                        if($k == 'visiblity') $visiblity = $v;
                        if($k == 'blog_type') $blog_type = $v;
                        if($k == 'subtitle') $subtitle = isset($v) ? $v : '';
                        if($k == 'media_link') $media_link = isset($v) ? $v : '';
                        if($k == 'media_url') $media_url = isset($v) ? $v : '';
                        if($k == 'media_type') $media_type = isset($v) ? $v : '';
                        if($k == 'source_name') $source_name = isset($v) ? $v : '';
                        if($k == 'source_url') $source_url = isset($v) ? $v : '';
                        if($k == 'via_name') $via_name = isset($v) ? $v : '';
                        if($k == 'via_url') $via_url = isset($v) ? $v : '';
                    }
                    $user_id = rand(1,2); $acc_id = 22; $tao_acc_user_id = 23927;
                    /*if($user_id == 1){
                        $tao_acc_user_id = 23964;
                        $acc_id = 79;
                    }else if($user_id == 2){
                        $tao_acc_user_id = 23922;
                        $acc_id = 16;
                    }else{
                        $tao_acc_user_id = 23944;
                        $acc_id = 56;
                    }*/
                    $post_array = array(
                        "title" => $title,
                        "tao_acc_user_id" => $tao_acc_user_id,
                        "acc_id" => $acc_id,
                        "category" => strtolower($category),
                        "type" => $type, // reads for blogs, and flash for flashcards
                        //"global" => $gl23918obal, // 1 for global, 0 for private
                        "global" => 0,
                        "description" => $body,
                        "excerpt" => $excerpt,
                        "visiblity" => $visiblity,
                        "blog_type" => $blog_type,
                        "subtitle" => $subtitle,
                        "media_link" => $media_link,
                        "media_url" => $media_url,
                        "media_type" => $media_type,
                        "source_name" => $source_name,
                        "source_url" => $source_url,
                        "via_name" => $via_name,
                        "via_url" => $via_url,
                        "media_type" => $pod,

                    );
                    //echo '<pre>'; print_r($post_array); exit();
                    $postdata = http_build_query( $post_array );
                    $opts = array(
                    'http' => array(
                                'method'  => 'POST',
                                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                                'content' => $postdata,
                        ),
                        "ssl" => array(
                                "verify_peer" => false,
                                "verify_peer_name" => false,
                        ),
                    );
                    $context  = stream_context_create( $opts );
                   //
                    $url = "https://api.tao.ai/scripts/addblurb.php";
                   //$url = TAOH_API_PREFIX."/scripts/addblurb.php";
                   //echo"=========".$url;
                   echo "\r\n".file_get_contents( $url, false, $context );


                }

            }
            else{
                $row = 1;
                $marray = $array = array();
                $handle = fopen($uploadfile, 'r');

                if ($handle !== FALSE) {
                    while (($data = fgetcsv($handle, 0, ',')) !== FALSE) {
                        //Print_r($data);
                    // echo"<br>===========".$row;
                        if ($row == 1) {
                            //print_r($data);
                            $num = count($data);
                            for ($i = 0; $i < $num; $i++) {
                            // echo"==========".$data[$i];
                                array_push($array, $data[$i]);
                            }
                        }
                        else {
                            $c = 0;
                            //print_r($array);
                            //print_r($data);
                            foreach ($array as $key) {

                                $marray[$row - 1][$key] = $data[$c];
                                $c++;
                            }

                        }
                        $row++;
                    }

                }


                foreach($marray as $key => $value){

                    //print_r($value);
                    foreach($value as $k=>$v){
                        //echo "<br>=========".$v;
                        if($k == 'title') $title = $v;
                        if($k == 'category') $category = $v;
                        if($k == 'pod') $pod = $v;
                        if($k == 'body') $body = $v;
                        if($k == 'excerpt') $excerpt = $v;
                        if($k == 'visiblity') $visiblity = $v;
                        if($k == 'blog_type') $blog_type = $v;
                        if($k == 'subtitle') $subtitle = isset($v) ? $v : '';
                        if($k == 'media_link') $media_link = isset($v) ? $v : '';
                        if($k == 'media_url') $media_url = isset($v) ? $v : '';
                        if($k == 'media_type') $media_type = isset($v) ? $v : '';
                        if($k == 'source_name') $source_name = isset($v) ? $v : '';
                        if($k == 'source_url') $source_url = isset($v) ? $v : '';
                        if($k == 'via_name') $via_name = isset($v) ? $v : '';
                        if($k == 'via_url') $via_url = isset($v) ? $v : '';

                    }
                    $post_array = array(
                        "title" => $title,
                        "category" => $category,
                        "type" => $type, // reads for blogs, and flash for flashcards
                        "global" => $global, // 1 for global, 0 for private
                        "description" => $body,
                        "excerpt" => $excerpt,
                        "visiblity" => $visiblity,
                        "blog_type" => $blog_type,
                        "subtitle" => $subtitle,
                        "media_link" => $media_link,
                        "media_url" => $media_url,
                        "media_type" => $media_type,
                        "source_name" => $source_name,
                        "source_url" => $source_url,
                        "via_name" => $via_name,
                        "via_url" => $via_url,
                        //"media_type" => $pod,

                    );
                    header('Content-type: application/json');
                    //echo '<pre>'; print_r($post_array); exit();
                    $postdata = http_build_query( $post_array );
                    $opts = array(
                    'http' => array(
                                'method'  => 'POST',
                                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                                'content' => $postdata,
                        ),
                        "ssl" => array(
                                "verify_peer" => false,
                                "verify_peer_name" => false,
                        ),
                    );
                    //echo '<pre>'; print_r($opts); exit();

                    $context  = stream_context_create( $opts );
                   //
                    $url = "https://api.tao.ai/scripts/addblurb.php";
                   //$url = TAOH_API_PREFIX."/scripts/addblurb.php";
                   //echo"=========".$context;
                   echo "\r\n".file_get_contents( $url, false, $context );

                }

            }

        }
    } else {
        echo "Possible file upload attack!\n";
    }
}
?>
