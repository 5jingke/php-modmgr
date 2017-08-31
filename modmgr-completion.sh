#!/usr/bin/env bash
#
# Colpletion for modmgr
# Jinko Wu <jk@5jk.me>
# https://gitee.com/jinko/php-modmgr
#

#
# 初始化modmgr的options
#
_modmgr_init() {
    #
    # 补全命令列表
    #
    _modmgrcmds=(help list l deploy d undeploy ud clean git clone show version v ver
        initialize init create mapadd map mapdel persistent pss elev-priv ep cwd exit
        remove rm disable enable auto-complete update)

    #
    # 补全命令的选项列表
    #
    _modmgrglobaloptions='--nocolor --nooutput --help'
    _modmgroption_help=''
    _modmgroption_='--test --install-bash-completion'
    _modmgroption_list='-a -s -l -o'
    _modmgroption_l='-a -s -l -o'
    _modmgroption_deploy='-f -a -c -y'
    _modmgroption_d='-f -a -c -y'
    _modmgroption_undeploy='-f -y -c'
    _modmgroption_ud='-f -y -c'
    _modmgroption_clean='-d -c'
    _modmgroption_git='-y'
    _modmgroption_clone='-f -n'
    _modmgroption_show='-a -v'
    _modmgroption_version='-s'
    _modmgroption_v='-s'
    _modmgroption_ver='-s'
    _modmgroption_initialize=''
    _modmgroption_init=''
    _modmgroption_create=''
    _modmgroption_mapadd='--map -f'
    _modmgroption_map='-s -a -b'
    _modmgroption_mapdel=''
    _modmgroption_persistent='--admin-shell --cwd'
    _modmgroption_pss='--admin-shell --cwd'
    _modmgroption_elev_priv='--cwd'
    _modmgroption_ep='--cwd'
    _modmgroption_cwd=''
    _modmgroption_exit=''
    _modmgroption_remove='-d'
    _modmgroption_rm='-d'
    _modmgroption_disable=''
    _modmgroption_enable=''
    _modmgroption_auto_complete=''
    _modmgroption_update='-n'
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
        local _name='_modmgroption_'$currentCommand
        local options=$(eval printf '%s' '$'"$_name")
        options="$options ${_modmgrglobaloptions[@]}"
        COMPREPLY=($(compgen -W "$options" -- "${current}"))
        return 1
    esac
}

#
# 处理命令
#
_modmgr_processingcmd() {
    if [ "$argpos" = 1 ] ; then
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

    for dir in $(ls -F | grep "/$")
    do
        dir=${dir/\//}
        dir=${dir/ /\\ }
        result="$result
$dir"
    done
    cd "$cwd"
    echo $result
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
# 完成指定参数位置为2的模块名补全
#
_modmgr_completemodulepos() {
    local IFS=$'\n'

    if [ "$argpos" = 2 ] ; then
        _modmgr_completemodulename
        return 1
    fi
}

#
# 模块名补全
#
_modmgr_completemodulename() {
    local IFS=$'\n'

    if [ ! -e "$modmgrpath" ] ; then
        return 1
    fi

    cd "$modmgrpath/.modman"
    local result=""

    for dir in $(ls -F | grep "/$")
    do
        dir=${dir//\//}
        result="$result"$'\n'"$dir"
    done
    cd "$cwd"

    local results=$(compgen -W "$result" -- "$current")
    results=${results// /\\ }
    COMPREPLY=($results)
}



#
# 过滤 -- 参数
#
_modmgr_processingemptyoption() {
    for((i=1; i<${#COMP_WORDS[*]}; i++))
    do
        if [ "${COMP_WORDS[i]:0:1}" = "-" ] ; then
            argpos=$(expr $argpos - 1)
        fi

        if [ "${COMP_WORDS[i]}" = "--" ] ; then
            if [ "$i" != "$COMP_CWORD" ] ; then
                nooption="1"
            fi
        fi
    done

    if [ "$prev" = "--" ] ; then
        prev="${COMP_WORDS[COMP_CWORD-2]}"
        pprev="${COMP_WORDS[COMP_CWORD-3]}"
    fi
}

#
# 完成文件或目录名
# 参数1: 文件类型 f:文件, d:目录, 为空表示全部
#
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
        if [ ! -d "$dir" ] ; then
            return 1
        fi

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

        results="$results"$'\n'"${file// /\ }"
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
    COMPREPLY=(${results})
}

#
#  补全命令Handle
#

#
# list command 补全
#
_modmgr_completion_list() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}
_modmgr_completion_l() {
    _modmgr_completion_list
    return $?
}

#
# git 补全
#
_modmgr_completion_git() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# deploy 补全
#
_modmgr_completion_deploy() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

_modmgr_completion_d() {
    _modmgr_completion_deploy
    return $?
}

#
# undeploy 补全
#
_modmgr_completion_undeploy() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}
_modmgr_completion_ud() {
    _modmgr_completion_undeploy
    return $?
}

#
# mapdel 补全
#
_modmgr_completion_mapdel() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# mapdel 补全
#
_modmgr_completion_map() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# elev-priv 补全
#
_modmgr_completion_elev_priv() {
    if [ "$argpos" = 2 ] ; then
        COMPREPLY=(compgen -W "powershell cmd gitbash" -- "$current")
        return 1
    fi
}
_modmgr_completion_ep() {
    _modmgr_completion_elev_priv
    return $?
}

#
# remove 补全
#
_modmgr_completion_remove() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}
_modmgr_completion_rm() {
    _modmgr_completion_remove
    return $?
}

#
# enable 补全
#
_modmgr_completion_enable() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# disable 补全
#
_modmgr_completion_disable() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi
}

#
# help 补全
#
_modmgr_completion_help() {
    if [ "$argpos" = 2 ] ; then
        local result=${_modmgrcmds[@]}
        COMPREPLY=($(compgen -W "$result" -- "$current"))
    fi
}

#
# clean 补全
#
_modmgr_completion_clean() {
    if [ "$argpos" = 2 ] ; then
        _modmgr_completefilename
        return 1
    fi
}

#
# show 补全
#
_modmgr_completion_show() {
    if [ "$argpos" = 2 ] ; then
        COMPREPLY=(compgen -W "module-path project-path script-path" -- "$current")
        return 1
    fi
}

#
# mapadd 补全
#
_modmgr_completion_mapadd() {
    _modmgr_completemodulepos
    if [ "$?" = "1" ] ; then
        return 1
    fi

    #
    # 第三个参数
    #
    if [ "$argpos" = 3 ] ; then
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
    argpos=$(expr ${#COMP_WORDS[@]} - 1)
    nooption=""

    #
    # 过滤 -- 参数
    #
    _modmgr_processingemptyoption
    cwd=$(pwd)

    #
    # 空命令
    #
    if [ "${currentCommand:0:2}" = "--" ] ; then
        currentCommand=""
    fi

    currentCommand=${currentCommand//-/_}

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