# PDS - Payload Delivery Shelter   
PDS is the yin to the yang that is payload keying. Payload keying helps to avoid payload execution in sandboxes and on systems that you are not targeting. PDS helps to keep the payload from reaching systems it shouldn't in the first place. It's aim is to make the SOC work. e.g. file names change, and hashes change regularly

Obvious Blindside:  If an org has full packet capture they can carve out the payload rather than attempting to go back and get it off of your server. At that point you are relying on payload keying to not burn your infrastructure and payload.

#### Problem this addresses   
Your malicious URL's are accessed by the organizations security stack, search engine bots (some legit, some not), VirusTotal, AV vendors, threat hunters, etc. These scripts and utils are made with the purpose of minimizing the exposure of your payload.   


This project's goal is as follows:

1.) Keep payloads from bots/scrapers/threat hunters.   

2.) The security team may be able to get a payload.  Ensure that all targets have not received the same payload. 

3.) Alert you to when someone gets your payload, AND when someone tries to get a previous payload which has been renamed. This can indicate someone is hunting within the org and can allow you to uncover hunting infrastructure such as Digital Ocean droplets being used by the SOC. 

The project is broken up into php_gates which you can pick and choose to make the download of your payload more restrictive,  python script (payload_changer.py) that handles the modification of your payload names/hashes, and a monitoring script (logmon.sh) to alert for payload downloads and failed attempts.

## php_gates   
A variety of PHP gate files which allow you to restrict the download of your payload to your target only ideally. These files are just a reference and made for you to combine/modify to fit your environment.   

### Here are a few different common scenarios and the corresponding php gate:      
1.) ```id_switch.php```: Simple id switch to retrieve the payload. In practice, ```index.php?id=target``` redirects the user to the payload, any bots/scrapers that visit ```index.php``` get redirected to a benign page, or a 404 (default).    

2.) ```id_ip_gate.php```: Uses the id switch, but also restricts delivery to a target's Gateway IP/range, blocks for curl/wget and "bot" to mess with junior SOC monkeys who might try to get the payload.    

3.) ```useragent_gate.php```: Very wide open gate, just tries to block common spider and search engine User Agents.  If you must place our payload on a public file without additional gates this can help keep it from common threat hunting scrapers.          

## payload_changer.py     
Desc: Python script to change your payload's name and hash on a timer along with the php gate that drops the hta.  Records name and hash changes to a file for reporting or tracing.

No special dependancies   
Currently, the script does the following:    

1.) Changes the payload name to append a random numeric string on a timer.  Comment this out if you do not want the stdout info each time the payload changes. You can easily modify this to another pseudo random naming convention.    
        Currently, you would say run the script with the ```-p``` flag and the original file name: ex ```-p companyrewards.hta```, and it would change to ```12314_companyrewards.hta``` then ```98908_companyrewards.hta```, etc, based on the seconds parameter ```-s``` value which dictates how often the payload and php gate are changed.    
 
 2.) Appends the payload file with a comment tag containing the filename and timestamp (can be useful if your file gets renamed and submitted to VT), but primarily servers to change the hash each time the name changes.    
 
 3.) Renames the reference inside the php gate file on the timer.  NOTE: you must change the file reference back to "placeholder" if you kill the script and are restarting.  If you do not, it will not change the reference in the gate file once you restart.  You also must rename your payload back if you are killing and restarting the script.  i.e ```23432_payload.hta``` back to ```payload.hta``` and remove the appended comment tag inside the hta.    
 
 4.) Output of the payload hash and name are redirected to ```payload_hashes.txt``` in the same directory as ```payload_changer.py```.  Primarily created so you have details for reporting, but you can use this file to go backwards in the GET requests and hunt for which request resulted in your file ending up on VirusTotal. Can be useful for determining SOC tactics/response time if they are clueless enough to put things on VT.    

 ## logmon.sh     
Desc: Shell script to monitor for payload downloads and interrupt you with the basic info.   

No special dependancies   
I Recommend running the script out of the same directory as payload_changer.py, but the paths are all absolute.

1.) The script currently just monitors for access attempts to .hta payloads, but you can manually change it to whatever extension you want.  
2.) Doesn't recquire args to run.  Looks for nginx access.log first, then apache, then prompts if it can't find them.
2.) Run it as a background job (&) and let it interrupt you when someone downloads your payload.    
3.) Writes all the full web requests for HTA's to ```payload_hits.txt``` in the same directory if you want to check UA's after an alert
4.) Use payload_changer.py's output payload_hashes.txt to retro hunt on VTI and track down the exact recquest that put it on there


## Use example:   
Download ```payload_changer.py``` to ```/var/www/```   
Download chosen gate file to ```/var/www/html/example/``` as something like ```download.php```   
        Ensure that the gate has the value "placeholder" in it which will be changed to the payloads by ```payload_changer.py```   
Download your hta payload to ```/var/www/html/example/``` as something like ```secureform.hta```      

I recommend running ```payload_changer.py``` under screen so you can detach (ctrl a ctrl d) and reattach (```screen -ls, screen -r <pid>```).   
```screen python payload_changer.py -d /var/www/html/example/ -p secureform.hta -g gate.php -s 10```   

Run the Log Monitoring script as a background process.   
```./logmon.sh&```

### Future Improvements    
[done v1.1 1/4/19] Support for monitoring log files for successful downloads of payload   
Support for identifying suspicious requests (example: googlebot UA from a non Google IP)   

#### Contact  
Issues or feature requests   
Contact sam at sayen.io    



