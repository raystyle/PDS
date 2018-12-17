# PDS - Payload Delivery Shelter   
PDS is the yin to the yang that is payload keying.   

#### Problem this addresses   
Your malicious URL's are accessed by the organizations security stack, various search engine bots (some legit), threat hunters, etc. How are you keeping the payload from being downloaded by these services?   

If your file is detected, can the SOC simply search for a hash or filename within your target environment?

This project's goal as follows.

1.) Keep payloads from bots/scrapers/threat hunters.   

2.) The security team may be able to get a payload.  Ensure that all targets have not received the same payload. Make them hunt a little (full packet capture) or a lot (no proxy logs, no full pcap).    

3.) Alert you to when someone gets your payload, AND when someone tries to get your payload which has already been moved. This can indicate someone is hunting within the org. 

The project is broken up into php_gates which you can pick and choose to make the download of your payload more restrictive, and a python script that handles the modification of your payload names/hashes, and  

## php_gates   
A variety of PHP gate files which allow you to restrict the download of your payload to your target only ideally. These files are just a reference and made for you to combine/modify to fit your environment.   

### Here are a few different common scenarios and the corresponding php gate:      
1.) ```id_switch.php```: Simple id switch to retrieve the payload. In practice, ```index.php?id=target``` redirects the user to the payload, any bots/scrapers that visit ```index.php``` get redirected to a benign page, or a 404 (default).    

2.) ```id_ip_gate.php```: Uses the id switch, but also restricts delivery to a target's Gateway IP/range, blocks for curl/wget and "bot" to mess with junior SOC monkeys who might try to get the payload.    

3.) ```useragent_gate.php```: Very wide open gate, just tries to block common spider and search engine User Agents.  If you must place our payload on a public file without additional gates this can help keep it from common threat hunting scrapers.          



## payload_changer.py     
No special dependancies   
Currently, the script does the following    

1.) Changes the payload name to append a random numeric string on a timer.  Comment this out if you do not want the stdout info each time the payload changes. You can easily modify this to another pseudo random naming convention.    
        Currently, you would say run the script with the -p flag and the original file name: ex ```-p companyrewards.hta```, and it would change to ```12314_companyrewards.hta``` then ```98908_companyrewards.hta```, etc, based on the seconds parameter ```-s``` value which dictates how often the payload and php gate are changed.    
 
 2.) Appends the payload file with a comment tag containing the filename and timestamp (can be useful if your file gets renamed and submitted to VT), but primarily servers to change the hash each time the name changes.    
 
 3.) Renames the reference inside the php gate file on the timer.  NOTE: you must change the file reference back to ```payloadinitalized``` if you kill the script and are restarting.  If you do not, it will not change the reference in the gate file once you restart.  You also must rename your payload back if you are killing and restarting the script.  i.e ```23432_payload.hta``` back to ```payload.hta``` and remove the appended comment tag inside the hta.    
 
 4.) Output of the payload hash and name are redirected to ```payload_hashes.txt``` in the same directory as ```payload_changer.py```.  Primarily created so you have details for reporting, but you can use this file to go backwards in the GET requests and hunt for which request resulted in your file ending up on VirusTotal. Can be useful for determining SOC tactics/response time if they are clueless enough to put things on VT.    

## Use example:   
You can run it via nginx or apache2 and tail the log files.   
However, running it via php's built in server works great since requests acts as an interupt when running as a background process.   

Download payload_changer.py to ```/tmp```   
Download chosen gate file to ```/tmp/html/``` (put the gate file in the same directory as the payload) 

Make sure that you do not have directory listing enabled on you html folder....   

```cd html```   

Run the php sever as a background process. Any requests will be an interrupt    

```php -S publicip:80 &```    

Go back one dir and run the python script.. make sure you are not putting it in your public html folder   

```cd ../```  

Recommend running under screen so you can detach (ctrl a ctrl d).  The php server will do interupts which works great to monitor during an engagement   

```screen python payload_changer.py -d html/ -p payload.hta -g gate.php -s 10```   

### Future Improvements    
Support for monitoring log files for successful downloads of payload   
Support for identifying suspicious requests (example: googlebot UA from a non Google IP)   

#### Contact  
Issues or feature requests   
Contact sam@sayen.io    



