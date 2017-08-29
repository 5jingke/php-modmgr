_complete_calc() {
    # echo "${COMP_WORDS[@]}"
    # local cur prev opts base
    # COMPREPLY=()
    # cur="${COMP_WORDS[COMP_CWORD]}"
    # prev="${COMP_WORDS[COMP_CWORD-1]}"
    str=`modmgr auto-complete -- ${COMP_WORDS[*]:1}`
    str=`echo "$str" | sed "s/^[ \s]\{1,\}//g;s/[ \s]\{1,\}$//g"`

    if [ "$str" != ""  ] ; then
        if [ "${str:0:1}" != "(" ] ; then
            COMPREPLY="$str"
        else
            COMPREPLY=(${str:1})
        fi
    fi
}

complete -F _complete_calc mm
complete -F _complete_calc modmgr
complete -F _complete_calc mmm