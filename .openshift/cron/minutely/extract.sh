#!/bin/bash

minute=$(date +%M)
if [[ $minute =~ .*[05] ]] then
    curl http://fundvis-ywng.rhcloud.com/api/extracter/stock_extract 
fi


