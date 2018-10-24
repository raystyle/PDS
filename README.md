# PDS - Payload Delivery Shelter
-----------------------------------------------
TLDR; This aims to be a collection of server side scripts to keep your malcode from getting
in the hands of a SOC, VirusTotal, or the other various threat hunting services out there.

In addition, this includes script(s) to automate the name and hash modifications of your payload (only HTA supported initially) on a timer of your choosing which may help evade some junior SOC folks doing hash and name searches in their SIEM.

Nothing new here, just using the same methods that criminals have been using forever.

## php_gates
---------------------------------------------------
A variety of PHP gate files which allow you to restrict the download of your payload for two scenarios.

    1.) You know the target Gateway IP/range (id_ip_gate.php)
        blocks are are in there for curl/wget and "bot" to mess with junior SOC monkeys who might try to get the payload.  Payload will still be accessible if they are inside the target network and use a real browser UA via wget/curl on the gate file with the proper id parameter. If you are using payload_changer.py with the gate they won't be able to get the payload directly however.
    2.) You don't know, or you don't want to risk the target opening work email from another network and not getting the payload (useragent_gat.php)
    3.) Uber simple id switch so to view the payload (id_switch.php)

## payload_changer.py
---------------------------------------------------
No special dependancies
Currently, the script does the following

1.) Changes the payload name to append a random numeric string on a timer.  Comment this out if you do not want the stdout info each time the payload changes. You can easily modify this to another pseudo random naming convention.
        Currently, you would say '-p companyrewards.hta' and it would change to 12314_companyrewards.hta then 98908_companyrewards.hta, etc, based on the seconds parameter '-s' value
 2.) Appends the payload file with a comment tag containing the filename and timestamp (can be useful if your file gets renamed and submitted to VT), but primarily servers to change the hash each time the name changes.
 3.) Renames the reference inside the php gate file on the timer.  NOTE: you must change the file reference back to "payloadinitalized" if you kill the script and are restarting.  If you do not, it will not change the reference in the gate file once you restart.  You also must rename your payload back if you are killing and restarting the script.  i.e 23432_payload.hta back to payload.hta and remove the appended comment tag inside the hta.
 4.) Outputs the payload hash and name to payload_hashes.txt in the same directory as payload_changer.py.  Primarily created so you have details for reporting, but you can use this file to go backwards in the GET requests and hunt for which request resulted in your file ending up on VirusTotal. Can be useful for determining SOC tactics/response time if they are clueless enough to put things on VT.

Use example:
---------------------------------------------------
You can run it via nginx or apache2 and tail the log files.
However, running it via php's built in server works great since requests acts as an interupt when running as a background process.

Download payload_changer.py to /tmp
Download chosen gate file to /tmp/html/ (put the gate file in the same directory as the payload) 

#make sure that you do not have directory listing enabled on you html folder....

`cd html`

#run the php sever as a background process. Any requests will be an interrupt

`php -S publicip:80 &`

#go back one dir and run the python script.. make sure you are not putting it in your public html folder

`cd ../`

#recommend running under screen so you can detach (ctrl a ctrl d).  The php server will do interupts which works great to monitor during an engagement

`screen python payload_changer.py -d html/ -p payload.hta -g gate.php -s 10`

# Future Improvements
---------------------------------------------------
Support for monitoring log files for successful downloads of payload
Support for identifying suspicious requests (example: googlebot UA from a non Google IP)

---------------------------------------------------
Issues or feature requests
Contact sam@sayen.io



