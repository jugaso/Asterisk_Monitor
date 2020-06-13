#!/bin/bash
#**********************************************************
#                                                         *
#       VERIFICAÇÃO DE STATUS DE CONTAS NO ASTERISK       *
#                                                         *
#**********************************************************
#  crontab -e
#  PATH=/usr/local/bin
#  0 8 * * 1-5 /usr/local/bin/sip_monitor.sh 2>/dev/null
#  Escrito por Júlio Gadioli Soares free to use!!!!

LOG_PID="/var/log/asterisk/pid_log"
LOG_SIP="/var/log/asterisk/accounts_log"
LOG_ONN="/var/log/asterisk/online_log"
LOG_OFF="/var/log/asterisk/offline_log"
PHP=`which php`
SENDMAIL="/usr/local/bin/send_email.php"
data=`date +%d/%m/%Y' '%T''`

if [ ! -f "$LOG_PID" ]; then `umask 0; touch $LOG_PID`; fi
if [ ! -f "$LOG_SIP" ]; then `umask 0; touch $LOG_SIP`; fi
if [ ! -f "$LOG_ONN" ]; then `umask 0; touch $LOG_ONN`; fi
if [ ! -f "$LOG_OFF" ]; then `umask 0; touch $LOG_OFF`; fi

PIDAST=`pidof asterisk`
PID_INFO=`cat $LOG_PID | awk 'NR==1{print $1}'`

re='^[0-9]+$'
if [[ $PIDAST =~ $re ]]; then
		`truncate -s 0 $LOG_PID`
		`pidof asterisk > $LOG_PID`
		`cat /dev/null > $LOG_SIP`
		`asterisk -x "sip show peers" 2>/dev/null | sed 1d | awk '{print $1}' | awk -F'/' '{print $1}' >> $LOG_SIP`
		tail -n 1 $LOG_SIP | wc -c | xargs -I {} truncate $LOG_SIP -s -{}
		IFS=''
		cat $LOG_SIP |
		while read -r sip
		do
		#Verificar se a conta está online
		RESIP=`asterisk -x "sip show peers" 2>/dev/null | grep -w OK | awk '{print $1}' | awk -F'/' '{print $1}' | grep "$sip"`
		if [ -z $RESIP ]; then
			declare regex="\s+$sip\s+"
			declare file_content=$( cat "${LOG_OFF}" )
			if [[ " $file_content " =~ $regex ]]
			    then
		        	continue
			else
		        `echo $sip $data>>${LOG_OFF}`
		        `sed -i -e "/$sip/d" $LOG_ONN`
				`$PHP $SENDMAIL $sip "((Offiline))"`
			fi
		else
			declare regex="\s+$sip\s+"
			declare file_content=$( cat "${LOG_ONN}" )
			if [[ " $file_content " =~ $regex ]]
			    then
		            	continue
			else
				`echo $sip $data>>${LOG_ONN}`
				`sed -i -e "/$sip/d" $LOG_OFF`
				`$PHP $SENDMAIL $sip "Online"`
			fi
		fi
		done
elif [[ $PID_INFO =~ "PIDOFF" ]]; then
	exit	
else
		`echo "PIDOFF" > $LOG_PID`;
		`$PHP $SENDMAIL "SERVIDOR ASTERISK" "OFFLINE"`
fi
exit
