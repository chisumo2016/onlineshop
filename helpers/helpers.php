<?php
//Print Error Message
function display_errors($errors){
    $display = '<ul class="bg-danger">';
    foreach ($errors as $error){
        $display .= '<li class="text-center">'.$error.'</li>';
    }
    $display  .='</ul>';
    return $display;
}

//SANITIZE
function sanitize($dirty){
    return htmlentities($dirty, ENT_QUOTES, "UTF-8");
}

function money($number){
    return '£'.number_format($number,2);
}