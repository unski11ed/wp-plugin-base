<?php

namespace __PluginNamespace__\Utilities;

function show_paginator(){
    global $VIEWBAG;
    
    $page = $VIEWBAG['_page'];
    $maxPage = $VIEWBAG['_maxPage'];
    $urlBase = $VIEWBAG['_urlBase'];
    
    $prevPage = $page - 1;
    $nextPage = $page + 1;
    
    echo "<div class='ksk-paginator'>";
    if($page > 1){
        echo "<a href='$urlBase&p=1'><<</a>";
        echo "<a href='$urlBase&p={$prevPage}'><</a>";
    }
    for($i = 1; $i <= $maxPage; $i++){
        echo "<a href='$urlBase&p={$i}' class='".($page == $i ? 'active' : '')."'>$i</a>";
    }
    if($page < $maxPage){
        echo "<a href='$urlBase&p={$nextPage}'>></a>";
        echo "<a href='$urlBase&p={$maxPage}'>>></a>";
    }
    echo "</div>";
}
