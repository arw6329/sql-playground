#!/bin/sh
while read -r file; do tr -dc '[:alnum:]' < /dev/urandom | head -c40 > "secrets/$file.txt"; done <<A
postgres12pwd
postgres13pwd
postgres14pwd
postgres15pwd
postgres16pwd
mysql8.4pwd
mysql8.0pwd
oracle23aipwd
oracle21cpwd
A
