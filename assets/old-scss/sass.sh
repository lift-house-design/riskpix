#!/bin/bash

start_sass()
{
	INPUT_FILE_LIST=`find . | grep '\.scss$' | grep -v '\./includes/'`
	for INPUT_FILE in $INPUT_FILE_LIST
	do
		OUTPUT_FILE=`echo "$INPUT_FILE" | sed -e 's/\.\(.*\/\)\(.*\)\.scss$/..\/css\1\2.css/'`
		sass --watch "$INPUT_FILE":"$OUTPUT_FILE" &
	done
}

stop_sass()
{
	ps -A | grep '\/usr\/bin\/sass' | awk '{print $1}' | xargs kill
}

if [ "$1" == 'start' ]
then
	start_sass > /dev/null 2>&1
	echo 'Started SASS:'
	find . | grep '\.scss$' | grep -v '\./includes/'
elif [ "$1" == 'stop' ]
then
	stop_sass > /dev/null 2>&1
	echo 'Stopped SASS'
fi