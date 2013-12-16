#!/bin/bash

REMHOST="Your Remote Host"
REMDIR="/Directory/On/Remote/Host"
REMUSER="usernameGoesHere" 

rsync -av --delete  ./ $REMUSER@$REMHOST:$REMDIR/
