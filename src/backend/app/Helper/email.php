<?php
function send_email($path, $from,  $destination, $data){
    $raw = file_get_contents($path);
    
    foreach($data as $key=>$val){
        $raw = str_replace("{".$key."}", $val, $raw);
    }
    
    $items = explode(";;", $raw);
    
    $subject = $items[0];
    $content = $items[1];

    $headers[] = "MIME-Version: 1.0";
    $headers[] = "From: $from";
    $headers[] = 'Content-type: text/html';
    $headers[] = "X-Priority: 1 (Highest)";
    $headers[] = "X-MSMail-Priority: High";
    $headers[] = "Importance: High";

    //var_dump(get_defined_vars());
    
    wp_mail($destination, $subject, $content, $headers );
}

