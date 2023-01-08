#!/bin/bash

cd `dirname $0`
echo "Usage: $0 [redirect_url_in_addressbar]"
echo

. .env

SCOPE=chat:write,channels:history,files:write

echo "https://slack.com/oauth/v2/authorize?scope=$SCOPE&client_id=$CLIENT"

if [ "$1" != "" ]; then
	CODEURL=$1
	CODE=`echo "$1" | sed "s/.*code=//"`
	echo "curl -F code=$CODE -F client_id=$CLIENT -F client_secret=$SECRET https://slack.com/api/oauth.v2.access"
	curl -F code=$CODE -F client_id=$CLIENT -F client_secret=$SECRET https://slack.com/api/oauth.v2.access >| access_token.json
	cat access_token.json
fi
