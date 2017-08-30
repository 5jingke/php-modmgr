#!/usr/bin/env bash

#
# 初始化modmgr的options
#
_modmgr_initoptions() {
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

#
# 处理命令
#
_modmgr_initcmds() {
    _modmgrcmds=(help list l deploy d undeploy ud clean git clone show version v ver 
    initialize init create mapadd map mapdel persistent pss elev-priv ep cwd exit 
    remove rm disable enable auto-complete)
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

_modmgr_completion_list() {
    if [ "$argcount" -lt 3 ] ; then
        COMPREPLY=($(compgen -W "$(_modmgr_listmodules)" -- "$current"))
        return 1
    fi
}

_modmgr_completion() {
    #       
    # completion 参数初始化  
    #
    current="${COMP_WORDS[COMP_CWORD]}"
    currentCommand="${COMP_WORDS[1]}"
    argcount=$(expr ${#COMP_WORDS[@]} - 1)
    
    cwd=$(pwd)
    
    if [ "${currentCommand:0:1}" = "-" ] ; then
        currentCommand=""
    fi
    
    #       
    # 处理选项    
    #
    _modmgr_initoptions
    _modmgr_processingoption
    if [ "$?" = "1" ] ; then
        return
    fi
    
    #       
    # 处理命令    
    #
    _modmgr_initcmds
    _modmgr_processingcmd
    if [ "$?" = "1" ] ; then
        return
    fi
    
    modmgrpath=$(_modmgr_findmodman "$cwd")
    _modmgr_completion_list
}


complete -F _modmgr_completion modmgr
complete -F _modmgr_completion mm
complete -F _modmgr_completion mmm