 ```Module Manager PHP Edition

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

 Use `modmgr help [command]` or `modmgr [command] --help` to get the more details```