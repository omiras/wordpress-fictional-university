#!/bin/bash
file="debug.log"

while :
   do :
   if [ -e "$file" ]; then
       echo -e "\a"
       cat $file
       rm $file
       exit 1
   fi 
   done
