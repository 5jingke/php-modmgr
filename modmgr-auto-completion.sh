#!/usr/bin/env bash
_modmgr_completion_calculator() {
    args=""
    current="${COMP_WORDS[COMP_CWORD]}"

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

    if [ "$result" != ""  ] ; then
        resulttype=${result:0:1}
        result=${result:1}
echo "$resulttype"
        if [ "$resulttype" == "(" ] ; then
            COMPREPLY=(${result})
        elif [ "$resulttype" == "#" ] ; then
            if [ "$result" != "" ] ; then
                COMPREPLY="$result"
            fi
        elif [ "$resulttype" == "F" ] ; then

            compopt -o filenames
            local files=()

            if [ "$current" = "" ] ; then
                files=(""*)
            else
                files=("${current}/"*)
            fi

#            echo ${files[@]// /\ };
            COMPREPLY=( "${files[@]// /\ }" )
            return 0
        fi
    fi
}

modmgrcheck=`modmgr --test`

if [ "$modmgrcheck" = "1" ] ; then

    complete -F _modmgr_completion_calculator modmgr
    complete -F _modmgr_completion_calculator mm
    complete -F _modmgr_completion_calculator mmm

fi