#!/bin/bash
if [ ! -f $OPENSHIFT_DATA_DIR/last_run ]; then
	touch $OPENSHIFT_DATA_DIR/last_run
fi
if [[ $(find $OPENSHIFT_DATA_DIR/last_run -mmin +1) ]]; then #run every 2 mins
	rm -f $OPENSHIFT_DATA_DIR/last_run
	touch $OPENSHIFT_DATA_DIR/last_run
	curl http://fundvis-ywng.rhcloud.com/api/extracter/stock_extract_batch1 
	curl http://fundvis-ywng.rhcloud.com/api/extracter/stock_extract_batch2
	curl http://fundvis-ywng.rhcloud.com/api/extracter/stock_extract_batch3
fi  