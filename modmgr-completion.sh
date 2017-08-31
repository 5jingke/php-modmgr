#!/usr/bin/env bash

#
# 初始化modmgr的options
#
_modmgr_init() {
    #
    # 补全命令列表
    #
    _modmgrcmds=(help list l deploy d undeploy ud clean git clone show version v ver
        initialize init create mapadd map mapdel persistent pss elev-priv ep cwd exit
        remove rm disable enable auto-complete)

    #
    # 补全命令的选项列表
    #
    _modmgrglobaloptions=(--nocolor --nooutput --help)
    _modmgroption_help=()
    _modmgroption_=(--test --install-bash-completion)
    _modmgroption_list=(-a -s -l -o)
    _modmgroption_l=(-a -s -l -o)
    _modmgroption_deploy=(-f -a -c -y)
    _modmgroption_d=(-f -a -c -y)
    _modmgroption_undeploy=(-f -y -c)
    _modmgroption_ud=(-f -y -c)
    _modmgroption_clean=(-d -c)
    _modmgroption_git=(-y)
    _modmgroption_clone=(-f -n)
    _modmgroption_show=(-a -v)
    _modmgroption_version=(-s)
    _modmgroption_v=(-s)
    _modmgroption_ver=(-s)
    _modmgroption_initialize=()
    _modmgroption_init=()
    _modmgroption_create=()
    _modmgroption_mapadd=(--map -f)
    _modmgroption_map=(-s -a -b)
    _modmgroption_mapdel=()
    _modmgroption_persistent=(--admin-shell --cwd)
    _modmgroption_pss=(--admin-shell --cwd)
    _modmgroption_elev_priv=(--cwd)
    _modmgroption_ep=(--cwd)
    _modmgroption_cwd=()
    _modmgroption_exit=()
    _modmgroption_remove=(-d)
    _modmgroption_rm=(-d)
    _modmgroption_disable=()
    _modmgroption_enable=()
    _modmgroption_auto_complete=()
}

_modmgr_compoptnospace() {
    compopt -o nospace "modmgr"
    compopt -o nospace "mm"
    compopt -o nospace "mmm"
}


_modmgr_compoptspace() {
    compopt +o nospace "modmgr"
    compopt +o nospace "mm"
    compopt +o nospace "mmm"
}

#
# 处理命令选项
#
_modmgr_processingoption() {
    case $current in
    -*)
        local _name='${_modmgroption_'$currentCommand'[@]}'
        local _options=$(eval echo $_name)
        _options=("$_options ${_modmgrglobaloptions[@]}")
        _options=${_options[@]}
        COMPREPLY=($(compgen -W "${_options}" -- "${current}"))
        return 1
    esac
}

#
# 处理命令
#
_modmgr_processingcmd() {
    if [ "$argcount" -lt 2 ] ; then
        if [ "$current" = "" ] ; then
            COMPREPLY=("${_modmgrcmds[@]}")
        else
            local result=${_modmgrcmds[@]}
            COMPREPLY=($(compgen -W "$result" -- "$current"))
        fi
        return 1
    fi
}

#
# 列出存在的模块
#
_modmgr_listmodules() {
    if [ ! -e "$modmgrpath" ] ; then
        return 1
    fi

    cd "$modmgrpath/.modman"
    local result=""

    for dir in $(ls | grep "/$")
    do
        dir=${dir/\//}
        dir=${dir/ /\ }
        result="$result $dir" #先判断是否是目录，然后再输出
    done
    echo $result
    cd "$cwd"
}

#
# 查找modman目录
#
_modmgr_findmodman() {
    if [ -e "$1/.modman" ] ; then
        echo $1
    else
        local parent=$(dirname "$1")

        if [ "$parent" = "$1" ] ; then
            return 1
        fi

        _modmgr_findmodman "$parent"
        return $?
    fi
}

#
# 完成模块名补全
#
_modmgr_completemodulename() {
    if [ "$argcount" -lt 3 ] ; then
        COMPREPLY=($(compgen -W "$(_modmgr_listmodules)" -- "$current"))
        return 1
    fi
}

#
# 过滤 -- 参数
#
_modmgr_processingemptyoption() {
    for((i=1; i<${#COMP_WORDS[*]}; i++))
    do
        if [ "${COMP_WORDS[i]}" = "--" ] ; then
            if [ "$i" != "$COMP_CWORD" ] ; then
                nooption="1"
                break
            fi
        fi
    done

    if [ "$prev" = "--" ] ; then
        prev="${COMP_WORDS[COMP_CWORD-2]}"
        pprev="${COMP_WORDS[COMP_CWORD-3]}"
    fi
}

_modmgr_completefilename() {
    _modmgr_compoptnospace
    local currentIFS=$IFS
    local _files=() dir="." IFS=$'\n'

    if [ "${current:$currentLen-1}" = "/" ] ; then
        dir=$current
    else
        dir=$(dirname "$current")
    fi

    dir=${dir//\\ / }

    if [ "$dir" != "." ] ; then
        cd "$dir"
    else
        dir=""
    fi

    if [ "$dir" != "" ] ; then
        dir="${dir%%/}/"
    fi

    #
    # 列出文件
    #
    if [ "$1" = "f" ] ; then
        _files=($(find * -maxdepth 0 -type f 2>/dev/null))
    elif [ "$1" = "d" ] ; then
        _files=($(find * -maxdepth 0 -type d 2>/dev/null))
    else
        _files=($(find * -maxdepth 0 2>/dev/null))
    fi

    cd "$cwd"

    if [ ${#_files[@]} = 0 ] ; then
        if [ "$current" != "" ] ; then
            COMPREPLY="$current "
        fi

        return 1
    fi

    local _basename=${current##*/}
    local results=() ri=0 len=${#_basename}

    for((i=0; i<${#_files[@]}; i++))
    do
        local file=${_files[i]}

        if [ -d "$file" ] ; then
            file=$file"/"
        fi

        results="$results
${file// /\ }"
    done

    compresults=($(compgen -W "$results" -- "$_basename"))

    #
    # 单独一个匹配项
    #
    if [ ${#compresults[@]} = 1 ] ; then
        local file="${dir// /\\ }${compresults[0]// /\\ }"

        if [ "$current" == "$file" ] ; then
            file="$file "
        fi

        COMPREPLY=$file
        return 1
    fi

    results=$(compgen -W "$results" -- "$_basename")
    COMPREPLY=(${results// /\\ })
}

#
#  补全命令Handle
#

#
# list command 补全
#
_modmgr_completion_list() {
    _modmgr_completemodulename
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# git command 补全
#
_modmgr_completion_git() {
    _modmgr_completemodulename
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# mapadd command 补全
#
_modmgr_completion_mapadd() {
    _modmgr_completemodulename
    if [ "$?" = "1" ] ; then
        return 1
    fi

    #
    # 第三个参数
    #
    if [ "$argcount" -lt 4 ] ; then
        _modmgr_completefilename
        return 1
    fi

    if [ "$prev" = "--map" ] ; then
        _modmgr_completefilename
        return 1
    fi

    if [ "$pprev" = "--map" ] ; then
        _modmgr_completefilename
        return 1
    fi
}

_modmgr_completion_test() {
    _modmgr_completefilename
    return 1
}



#
# completion主程序
#
_modmgr_completion() {
    _modmgr_compoptspace
    #
    # completion 参数初始化
    #
    current="${COMP_WORDS[COMP_CWORD]}"
    currentLen="${#current}"
    prev="${COMP_WORDS[COMP_CWORD-1]}"
    pprev="${COMP_WORDS[COMP_CWORD-2]}"
    currentCommand="${COMP_WORDS[1]}"
    argcount=$(expr ${#COMP_WORDS[@]} - 1)
    nooption=""

    #
    # 过滤 -- 参数
    #
    _modmgr_processingemptyoption
    cwd=$(pwd)

    #
    # 空命令
    #
    if [ "${currentCommand:0:1}" = "-" ] ; then
        currentCommand=""
    fi

    #
    # 如存在 -- 参数, 后续不需要补全选项
    #
    if [ -z "$nooption" ] ; then
        #
        # 处理选项
        #
        _modmgr_init
        _modmgr_processingoption
        if [ "$?" = "1" ] ; then
            return
        fi
    fi

    #
    # 处理命令
    #
    _modmgr_processingcmd
    if [ "$?" = "1" ] ; then
        return
    fi

    #
    # 查找modmgr项目目录
    #
    modmgrpath=$(_modmgr_findmodman "$cwd")
    compcall="_modmgr_completion_$currentCommand"

    #
    # 分配给各个命令执行补全
    #
    if [ "$(type -t $compcall)" = "function" ] ; then
        $compcall
    fi
}

_modmgr_handlecompltion() {
    complete -F _modmgr_completion "modmgr"
    complete -F _modmgr_completion "mm"
    complete -F _modmgr_completion "mmm"
}

_modmgr_handlecompltion