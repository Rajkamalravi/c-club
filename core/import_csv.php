<?php

$file_to_import = array(
    //"interview" => "C:\Users\Lenovo\Downloads\csv_files\interview.csv", //done
    //"jobs-of-future" => "C:\Users\Lenovo\Downloads\csv_files\jobs_of_future.csv", //done
    //"branding" => "C:\Users\Lenovo\Downloads\csv_files\branding.csv", //done
    //"growth-mindset" => "C:\Users\Lenovo\Downloads\csv_files\growth-mindset.csv", //done
    //"resume" => "C:\Users\Lenovo\Downloads\csv_files\/resume.csv", //done
    //"networking" => "C:\Users\Lenovo\Downloads\csv_files\/networking.csv", //done
    //"job-search" => "C:\Users\Lenovo\Downloads\csv_files\job_search.csv", //done
    //"handling-change" => "C:\Users\Lenovo\Downloads\csv_files\handling-change.csv", //done
    //"conflict-management" => "C:\Users\Lenovo\Downloads\csv_files\conflict-management.csv", //done

    //"negotiation" => "C:\Users\Lenovo\Downloads\csv_files\/negotiation.csv", //done
    //"career-development" => "C:\Users\Lenovo\Downloads\csv_files\career-development.csv", //done
    //"leadership" => "C:\Users\Lenovo\Downloads\csv_files\leadership-development.csv", //done
    //"learning" => "C:\Users\Lenovo\Downloads\csv_files\learning.csv", //done
    //"mentor-coach" => "C:\Users\Lenovo\Downloads\csv_files\mentorship-coaching.csv", //done
    //"mindfulness" => "C:\Users\Lenovo\Downloads\csv_files\mindfulness.csv", //done
    //"future-of-work" => "C:\Users\Lenovo\Downloads\csv_files\/future-of-work.csv", //done
    //"organization" => "C:\Users\Lenovo\Downloads\csv_files\organization.csv", //done
    //"productivity" => "C:\Users\Lenovo\Downloads\csv_files\productivity.csv", //done
    ///"wellness" => "C:\Users\Lenovo\Downloads\csv_files\wellness.csv", //done
    //"wellness" => "C:\Users\devir\Downloads\Flash-Final_Wellness4.csv", //done
);

foreach($file_to_import as $k => $value){
    if (($open = fopen($value, "r")) !== FALSE) {
        while (($data = fgetcsv($open, 1000, ",")) !== FALSE)
        {
        $array[] = $data;
        }
        fclose($open);
    }
    foreach($array as $key => $val){
        if($val[1] != 'Title' && $val[1] != 0 && $val[1] != ''){
            $post_array = array(
                "title" => $val[1],
                "category" => $val[3],
                "type" => "flash", // reads for blogs, and flash for flashcards
                "global" => 1, // 1 for global, 0 for private
                "description" => $val[2],
                "media_type" => $k, // image,media, soundcloud, youtube, text
                "media_link" => "", // image,media, soundcloud, youtube, text
                "image" => "",
            );
            echo '<pre>'; print_r($post_array); exit();
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
            $url = "https://api.tao.ai/scripts/addblurb.php";
            echo file_get_contents( $url, false, $context );
        }
    }
}