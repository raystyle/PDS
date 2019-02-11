#!/bin/bash

nginx_logfile="/var/log/nginx/access.log"
apache_logfile="/var/log/apache2/access.log"


if [ -f $nginx_logfile ]; then
    echo "NGINX access.log found and being used."
    logfile=${nginx_logfile}
elif [ -f ${apache_logfile} ]; then
    echo "No NGINX found, defaulting to Apache access.log file."
    logfile=${apache_logfile}
else
    echo "No NGINX or Apache access.log found... Enter it with abs path now."
    read -p 'logfile: ' logfile  
    if [ -f ${logfile} ]; then
        echo "Found your log file: ${logfile}"
    else
        echo "Sorry, file was not found"
    fi
fi

echo "---------------------------PDS 1.1 Logmon----------------------------"

#   whois $ip_located | egrep -i "CustName\|OrgTechName\|OriginAS\|address\|descr" 


#tail logfile running in background with grep for hta, piped to a file
# THAT file is checked for md5 changes

hitsfile="payload_hits.txt"
touch ${hitsfile}
OLD=`md5sum ${hitsfile}`
NEW=${OLD}

#tail the log file to look for hta hits
tail -f ${logfile} | grep --line-buffered "hta HTTP/1.1" >> ${hitsfile} &

echo -e "\e[32m[+]\e[0m Monitoring for HTA access attempts" 
while true;
do
    if [ "${OLD}" == "${NEW}" ]
    then   
        sleep 1
        NEW=`md5sum ${hitsfile}`
    else
        servercode="Not a typical server code... Investigate!"
        iphit=`tail -n 1 ${hitsfile} | grep --line-buffered -E -o "([0-9]{1,3}[\.]){3}[0-9]{1,3}"`
        filehit=`tail -n 1 ${hitsfile} | grep --line-buffered -o -P "(?<=GET|HEAD).*(?=.hta)"`
        servercode=`tail -n 1 ${hitsfile} | grep --line-buffered -o -E "200|304|404"`

        #maybe add in the future....
        #whoishit=`whois $iphit | grep --line-buffered -E -o "CustName\|OrgTechName\|OriginAS\|address\|descr"`
        time=`date`
        echo -e "\e[1mIP: ${iphit}\e[0m" 
        echo "TIMESTAMP: ${time}"
        echo "FILE: ${filehit}.hta"
        
        if [ "${servercode}" == "200" ]
        then
            echo -e "CODE: \e[32m${servercode}\e[0m"
            echo -e "\e[5mALERT: Someone at ${iphit} has your payload\e[0m"
        else
            echo -e "CODE: \e[31m${servercode}\e[0m"
        fi
        echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
        OLD=`md5sum ${hitsfile}`
    fi
done
