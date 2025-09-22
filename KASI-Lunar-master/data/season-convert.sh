#!/bin/bash

function so24 {
	case "$1" in
		"소한") return 0 ;;
		"대한") return 1 ;;
		"입춘") return 2 ;;
		"우수") return 3 ;;
		"경칩") return 4 ;;
		"춘분") return 5 ;;
		"청명") return 6 ;;
		"곡우") return 7 ;;
		"입하") return 8 ;;
		"소만") return 9 ;;
		"망종") return 10 ;;
		"하지") return 11 ;;
		"소서") return 12 ;;
		"대서") return 13 ;;
		"입추") return 14 ;;
		"처서") return 15 ;;
		"백로") return 16 ;;
		"추분") return 17 ;;
		"한로") return 18 ;;
		"상강") return 19 ;;
		"입동") return 20 ;;
		"소설") return 21 ;;
		"대설") return 22 ;;
		"동지") return 23 ;;
	esac

	return 127
}


t=$'\t'
while read line
do
	[[ ${line} =~ ^[[:space:]]*$|^# ]] && continue

	[[ ${line} =~ ^2[0-9]{3}$ ]] && {
		x=0
		y=${line}
		cat <<-EOL
			${t}${t}[$y] => array (
		EOL
		continue
	}

	read se mt dy h m <<< "${line}"

	[[ ${#mt} == 1 ]] && mt="0${mt}"
	[[ ${#dy} == 1 ]] && dy="0${dy}"
	[[ ${#h}  == 1 ]] && h="0${h}"
	[[ ${#m}  == 1 ]] && m="0${m}"

	so24 ${se}
	so24n=$?

	#echo "$se($so24n) $y-$mt-$dy $h:$m"
	#cat <<-EOL
	#stamp="\$( date +"%s" -d "$y-$mt-$dy $h:$m:00" )"
	#EOL
	stamp="$( date +"%s" -d "$y-$mt-$dy $h:$m:00" )"

	#cat <<-EOL
	#	        [$se] => array ("$y-$mt-$dy $h:$m", "${stamp}"),
	#EOL
	#cat <<-EOL
	#	        [$so24n] => ${stamp},
	#EOL

	(( x > 0 && x % 7 == 0 )) && echo
	(( x % 7 == 0 )) && echo -en "\t\t\t"
	echo -n "${stamp}, "

	if (( so24n == 23 )); then
		echo
		echo -e "\t\t),"
	fi

	let "x++"

done < ${1:-./season.txt}
