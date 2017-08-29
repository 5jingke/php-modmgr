_modmgr_completion_calculator() {
    args=""
    
    for ((i=1; i<${#COMP_WORDS[*]}; i++))  
    do  
        findres=$(echo ${COMP_WORDS[i]} | grep " ")
        if [ "$findres" != "" ] ; then
          args="$args \"${COMP_WORDS[i]}\""
        else
            if [ "${COMP_WORDS[i]}" = "" ] ; then
                args="$args \"${COMP_WORDS[i]}\""
            else
                args="$args ${COMP_WORDS[i]}"
            fi
        fi
    done

    result=`modmgr auto-complete -- $args`
#    result=`echo "$result" | sed "s/^[ \s]\{1,\}//g;s/[ \s]\{1,\}$//g"`
    
    if [ "$result" != ""  ] ; then
        if [ "${result:0:1}" == "(" ] ; then
            COMPREPLY=(${result:1})
        else
            if [ "$result" != "" ] ; then
                COMPREPLY="$result"
            fi
        fi
    fi
}

modmgrcheck=`modmgr --test`

if [ "$modmgrcheck" = "1" ] ; then

    complete -F _modmgr_completion_calculator modmgr
    complete -F _modmgr_completion_calculator mm
    complete -F _modmgr_completion_calculator mmm

fi