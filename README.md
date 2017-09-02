### Module Manager PHP Edition
此工具可以方便管理项目中的模块的安装和卸载。 例如，管理Magento的模块等
此工具参考于linux下的modman工具: https://github.com/colinmollenhour/modman/

在工具的项目目录下有三个可执行文件， 分别是mm， mmm和modmgr。mm是modmgr的缩写命令，其运行输出会带有颜色。mmm 是不具有颜色输出的，等同于mm命令加上--nocolor选项， 适用于Windows的cmd.exe。

为方便使用，此工具还提供了bash的命令补全脚本。在linux下执行 
```sudo modmgr --install-bash-completion```
即可安装命令补全脚本, 在新的shell会话中即可生效。命令补全mm, mmm和modmgr。如果补全无法安装，可以手动安装项目目录下的modmgr-completion.sh。

在Windows系统中，命令补全只支持git-bash.exe，需要安装git和git-bash.exe(在安装git的时候可以选择安装git-bash)。命令补全也只能在git-bash.exe或者bash.exe中工作。在执行```modmgr --install-bash-completion```的时候, 如果是在win8或者更高的系统中同时开启了UAC。则需要管理员权限运行才能安装补全。提升权限可以直接通过命令进行：```modmgr ep cmd```
注意,提升git-bash.exe的权限方法可以用: ```modmgr ep gitbash```，但是需要将git-bash.exe的目录路径添加到系统环境变量PATH中。

添加环境变量的方法：

按下```WIN+R```组合键打开运行 > 输入 ```SystemPropertiesAdvanced``` > 点击右下方```环境变量```按钮 > 在下方的```系统变量```区域中找到```Path``` > ```编辑``` 然后加入git-bash.exe的目录路径即可

具体的用法参考```modmgr --help```



This tool makes it easy to manage the deployment and undeployment of modules in your project. Such as manage the modules of Magento.
Influenced by the original modman at https://github.com/colinmollenhour/modman/

There are three executable files, mm, mmm and modmgr in the root directory of this tools. mm is the abbreviation for modmgr, its output is colored. mmm output without color, it is equivalent to the implementation of mm with the --nocolor option.

For easy to use, this tool also provides bash command completion script.Execute in linux
```sudo modmgr --install-bash-completion```
You can install the command completion script, in the new shell session to take effect.If the completion can not be installed, you can manually install the project directory modmgr-completion.sh.

On Windows systems, command completion only supports git-bash.exe, and you need to install git and git-bash.exe (you can choose to install git-bash when installing git). Command completion can only work in git-bash.exe or bash.exe. In the implementation of ```modmgr - install-bash-completion```, if it is in the win8 or higher system at the same time open the UAC. You need to run with administrator privileges to install the completion. Raise permissions can be done directly through the command: ```modmgr ep cmd```
Note that the elevated git-bash.exe permission method can be used: ```modmgr ep gitbash```, but you need to add the git-bash.exe directory path to the system environment variable PATH.

To add an environment variable:

Press the ```WIN + R key``` combination to open the run > enter ```SystemPropertiesAdvanced``` and then press Enter > click the lower right of the ```environment variable``` button > in the bottom of the ```system variable area``` to find ```Path``` > ```edit``` and then add git-bash.exe directory path


Specific usage reference ```modmgr --help```


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
     Short option only use single letter and it can be made up of one or more '-', 
               such as '-abcd' equivalent to '-a' '-b' '-c' '-d'.
     Short option only use as a bool value.

     Global options bellow:

     --nocolor: Outputs text without color
     --nooutput: Don't output anything
     --help: Show help documentation of a command

     Empty command options:
      You can use the following options like `modmgr --install-bash-completion`

     --install-bash-completion: Install bash completion. (In Windows system, only support for git-bash.exe)
     --test:Used to check whether the program is installed. Always return 1


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

     空命令选项:
     你可以类似这样 `modmgr --install-bash-completion` 使用下面的选项

     --install-bash-completion: 安装bash命令补全(在windows系统中只支持git-bash.exe)
     --test: 测试此工具是否安装, 总是返回1


 执行 `modmgr help [command]` 或者 `modmgr [command] --help` 命令可以获取更多详细信息

```