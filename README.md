 ```
Module Manager PHP Edition

 Usage: modmgr [command] [commandArguments] [options] [-- [commandArguments]]

 command:
     MODMGR supported commands bellow:

       help        Show detailed help documentation for a command
       list        List the modules that matching wildcard
       l           List the modules that matching wildcard
       deploy      Deploy modules which matches the wildcard
       d           Deploy modules which matches the wildcard
       undeploy    Undeploy modules which matches the wildcard
       ud          Undeploy modules which matches the wildcard
       clean       Clean broken symbocli links and empty directory tree
       git         Run git command in module path
       clone       Use `git clone` command to clone remote repository into .modman directory
       show        Show item value of modmgr application
       version     Show modmgr version
       v           Show modmgr version
       ver         Show modmgr version
       initialize  Initialize the directory
       init        Initialize the directory
       create      Create a module
       mapadd      Add a mapping to the module mapping file
       map         Show mappings of modules
       mapdel      Delete a mapping record of a module
       persistent  Into persistent mode.
       pss         Into persistent mode.
       elev-priv   Elevate privileges of shell application
       ep          Elevate privileges of shell application
       cwd         Show current working directory
       exit        Exit persistent mode.
       remove      Remove a module from module directory
       rm          Remove a module from module directory
       disable     Disable a module
       enable      Enable a module

 commandArguments:
     Any charater after '--' will be processed as argumens
     e.g. `modmgr git westernunion -- remote -v`

 options:
     Long option can receive one or more values, such as --file path.
     Short option only use single letter and it can be made up of one or more '-', such as '-abcd' equivalent to '-a' '-b' '-c' '-d'.
     Short option only use as a bool value.

     Global options bellow:

     --nocolor: Outputs text without color
     --nooutput: Don't output anything
     --help: Show help documentation of a command

 Use `modmgr help [command]` or `modmgr [command] --help` to get the more details
```

```
模块管理器 PHP版

 用法: modmgr [命令] [命令参数] [选项] [-- [命令参数]]

 命令:
     MODMGR 支持的参数如下:

       help        显示modmgr帮助或者某个命令的帮助
       list        显示符合要求的模块
       l           显示符合要求的模块
       deploy      部署匹配的模块或者部署全部模块
       d           部署匹配的模块或者部署全部模块
       undeploy    卸载模块
       ud          卸载模块
       clean       清理项目中无效的链接或者空目录
       git         在模块路径下执行git命令
       clone       使用git命令克隆到.modman
       show        显示modmgr的环境值
       version     显示版本号
       v           显示版本号
       ver         显示版本号
       initialize  初始化目录
       init        初始化目录
       create      创建一个模块
       mapadd      添加映射到模块中
       map         显示模块中的映射
       mapdel      删除模块中的一个映射
       persistent  常驻模式
       pss         常驻模式
       elev-priv   提升命令行权限
       ep          提升命令行权限
       cwd         显示当前工作目录
       exit        退出常驻模式
       remove      移除模块
       rm          移除模块
       disable     禁用模块
       enable      启用模块

 命令参数:
     所有在 '--' 后面的参数和选项将会被处理为命令参数
     例如: `modmgr git westernunion -- remote -v`

 选项:
     长选项可以接收多个值, 例如 --file path.
     短选项只是用一个字符, 可以用一个或者多个'-'组成, 例如: '-abc' 等价于 '-a' '-b' '-c'.
     短选项只用来作为布尔值使用

     全局选项如下:

     --nocolor: 不输出颜色代码
     --nooutput: 关闭输出
     --help: 显示某个命令的帮助文档

 执行 `modmgr help [command]` 或者 `modmgr [command] --help` 命令可以获取更多详细信息

```