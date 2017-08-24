<?php
/**
 * Module Manager PHP Edition
 * @autor JinkoWu
 * @email jk@5jk.me
 * -------------------------------------------------------------------------
 * @modmgr-help
 * Module Manager PHP Edition
 *
 * Usage: modmgr [command] [commandArguments] [options] [-- [commandArguments]]
 *
 * command:
 *     MODMGR support commands bellow:
 *
 * {$commands}
 *
 * commandArguments:
 *     Any charater after '--' will be processed as argumens
 *
 * options:
 *     Long option can receive one or more values, such as --file path.
 *     Short option only use single letter and it can be made up of one or more '-', such as '-abcd' Equivalent to '-a' '-b' '-c' '-d'.
 *     Short option only use as a bool value.
 *
 *     There are global option bellow:
 *     --nocolor: Any out without color
 *     --nooutput: Don't output any message
 *     --help: Show help documentation of a command
 *
 * Use `modmgr help [command]` or `modmgr [command] --help` to get the more details
 *
 *
 * @modmgr-help-help
 * @d Show detailed help documentation for a command
 *
 * Usage: modmgr help [command]
 *
 * Such as `modmgr help deploy`
 *
 *
 * @modmgr-help-list
 * @d List the modules that matching wildcard
 *
 * Usage: modmgr list [wildcard] [-as]
 *        modmgr l [wildcard] [-as]
 *
 * wildcard:
 *     This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character
 *     If [wildcard] is not specified, the default is '*'
 *
 *     e.g. `modmgr list "mod_ave*"` matchs all modules that begin with 'mod_ave'
 *
 * options:
 *     -a: List all modules.
 *         If this option is not specified, only available modules will be listed
 *     -s: Simple mode.
 *
 *
 * @modmgr-help-deploy
 * @d Deploy modules which matches the wildcard
 *
 * Usage: modmgr deploy [wildcard] [-facy]
 *        modmgr d [wildcard] [-facy]
 *
 * wildcard:
 *     This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character
 *     If [wildcard] is not specified, the default is '*'
 *
 * options:
 *     -f: Force deploy. Remove the files, folders, and symbolic links that existed before deployment
 *     -a: Using absolute path to create symbolic links. This option is invalid if '-c' is specified
 *     -c: Copy files or folders directly instead of creating symbolic links
 *     -y: Does not display confirm message when operating on multiple modules
 *
 *
 * @modmgr-help-undeploy
 * @d Undeploy modules which matches the wildcard
 *
 * Usage: modmgr undeploy [wildcard] [-fcy]
 *        modmgr ud [wildcard] [-fcy]
 *
 * wildcard:
 *     This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character.
 *     If [wildcard] is not specified, the default is '*'
 *
 * options:
 *     -f: Force deploy. Remove the files, folders, and symbolic links that existed before deployment
 *     -c: Remove files, folders or symbocli links that mapping in module
 *         If this option is not specified, only symbocli links will be removed
 *     -y: Does not display confirm message when operating on multiple modules
 *
 *
 * @modmgr-help-clean
 * @d Clean broken symbocli links and empty directory tree
 *
 * Usage: modmgr clean [path] [-d]
 *
 * path:
 *     Specify a path to do cleaning
 *
 * option:
 *     -d: Remove empty tree at the same time
 *
 *
 * @modmgr-help-git
 * @d Run git command in module path
 *
 * Usage: modmgr git [wildcard] [-y] [gitArguments] [-- gitArguments]
 *
 * wildcard:
 *     This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character.
 *     This argument can't be empty. You can use '*' to filter out all modules
 *
 * option:
 *     -y: Does not display confirm message when operating on multiple modules
 *
 * gitArguments:
 *     Pass to the git such as pull or push and so on.
 *
 *
 * @modmgr-help-clone
 * @d Use `git clone` command to clone remote repository into .modman directory
 *
 * Usage: modmgr clone [gitRepoUri] [-fn]
 *
 * gitRepoUri:
 *    Git remote repository such as ssh://xxx or https://xxxx
 *
 * option:
 *     -f: Force clone. Remove exists folder from .modman directory
 *     -n: Only clone repository and don't deploy module.
 *         If this option is not specified, the module just cloned will be deployed immediately
 *
 *
 * @modmgr-help-show
 * @d Show item value of modmgr application
 *
 * Usage: modmgr show [item] [-av]
 *
 * item:
 *     Item key to show. If this arguments missing all item key will be showed
 *
 * option:
 *     -a: Show all item key
 *     -v: Show item value, must be used with '-a' at the same time
 *
 *
 * @modmgr-help-version
 * @d Show modmgr version
 *
 * Usage: modmgr version [-s]
 *        modmgr ver [-s]
 *        modmgr v [-s]
 *
 * option:
 *     -s: Simple mode
 *
 *
 * @modmgr-help-initialize
 * @d Initialize the directory
 *
 * Usage: modmgr initialize
 *        modmgr init
 *
 *
 * @modmgr-help-mapadd
 * @d Add a mapping to the module mapping file
 *
 * Usage: modmgr mapadd [module] [path] [-f] [--map source [target]]
 *
 * module:
 *     Module which you wish to operate
 *
 * path:
 *     The file or folder will be copyed to module directory
 *
 * optins:
 *     -f: Force add. Recover exists mapping record
 *     --map: Custom mapping. If this option missing, the source and target will use the value of 'path' argument
 *
 *  e.g. modmgr mapadd test app/1.txt --map app/t.txt app/t2.txt -f
 *  e.g. modmgr mapadd test app/1.txt --map app/t2.txt -f
 *  e.g. modmgr mapadd test app/1.txt -f
 *
 *
 * @modmgr-help-map
 * @d Show mappings of modules
 *
 * Usage: modmgr map [wildcard] [-as]
 *
 * Mapping status format is [deployed+undeployed=total]
 * '(D)' flag means the reocrd of mapping was deployed
 *
 * wildcard:
 *     This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character.
 *     If [wildcard] is not specified, the default is '*'
 *
 * option:
 *     -a: Show mappings of all module
 *     -s: Simple mode. Only show mapping status of module
 *
 *
 * @modmgr-help-mapdel
 * @d Delete a mapping record of a module
 *
 * Usage: modmgr mapdel [mapping-id]
 *
 * mapping-id:
 *     It is a number and show in `modmgr map` command.
 *
 *
 * @modmgr-help-persistent
 * @d Into persistent mode.
 *
 * Usage: modmgr persistent [--admin-shell shellapp] [--cwd path]
 *        modmgr pss [--admin-shell shellapp] [--cwd path]
 *
 * In persistent mode, you can type any command without 'modmgr'
 *
 * option:
 *     --admin-shell: Use new shell application to elevate privileges. Allow 'cmd', 'powershell' and 'gitbash'.
 *                    This option only available in Window System. If you need to use gitbash, you must add the
 *                    git-bash.exe parent path to you 'path' of environment variable.
 *     --cwd: Set the working directory
 *
 *
 * @modmgr-help-elevate-privileges
 * @d Elevate privileges of shell application
 *
 * Usage: modmgr elevate-privileges [shell] [--cwd path]
 *        modmgr ep [shell] [--cwd path]
 *
 * shell:
 *     Use new shell application to elevate privileges. Allow 'cmd', 'powershell' and 'gitbash'.
 *     This argument only available in Window System. If you need to use gitbash, you must add the
 *     git-bash.exe parent path to you 'path' of environment variable.
 *
 * option:
 *     --cwd: Set the working directory
 *
 *
 * @modmgr-help-cwd
 * @d Show current working directory
 *
 * Usage: modmgr cwd
 *
 *
 * @modmgr-help-exit
 * @d Exit persistent mode.
 *
 * This command only available in persistent mode.
 *
 *
 * @modmgr-help-create
 * @d Create a module
 *
 * Usage: modmgr craete [moduleName]
 *
 * moduleName:
 *     Specified module name
 *
 *
 * @modmgr-help-remove
 * @d Remove a module from module directory
 *
 * Usage: modmgr remove [wildcard] [-d]
 *        modmgr rm [moduleName] [-d]
 *
 * moduleName:
 *     This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character.
 *     This argument can't be empty. You can use '*' to filter out all modules
 *
 * option:
 *     -d: Undeploy before remove
 *
 *
 * @modmgr-help-disable
 * @d Disable a module
 *
 * Usage: modmgr disable [wildcard]
 *
 * wildcard:
 *      This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character.
 *      This argument can't be empty. You can use '*' to filter out all modules
 *
 *
 * @modmgr-help-enable
 * @d Enable a module
 *
 * Usage: modmgr enable [wildcard]
 *
 * wildcard:
 *      This argument use to filter modules. [wildcard] use '*' to match any character, use '?' to match one character.
 *      This argument can't be empty. You can use '*' to filter out all modules
 *
 *
 */
define('MODMGR_VERSION', '0.1.0');
define('ERROR_REPORTING', E_ALL ^ E_NOTICE ^ E_STRICT);
define('MODMGR_DIR_NAME', '.modman');
define('MODMGR_MAPPING_NAME', 'modman');
define('MODMGR_DISABLED', '.modmgr.disabled');

error_reporting(ERROR_REPORTING);
require_once 'lib.php';

/**
 * Class App
 */
class App extends BaseApp
{
    /**
     * 数组键为支持的命令，值为该命令支持的选项
     *  长选项需要使用完整的名称， 如 ['--file'， '--dir'] 对应参数中的 --file, --dir
     *  短选项不使用 ‘-’ 如 ['a', 's'] 对应参数的 -as。
     *  短选项只允许单个字符。由一个或者多个‘-’组成
     *  如 -abcd 等价于 -a -b -c -d。短选项只是用作布尔值
     *
     * The key of array is supported commands in application and value is support options in the command.
     *  Long option must fill with full name, such as ['--file'， '--dir'] is corresponding to '--file', '--dir' in parameter.
     *  Short option does not use '-', such as ['a', 's'] is corresponding to '-as' in parameter.
     *  Short option only use single letter and it can be made up of one or more '-', such as '-abcd' Equivalent to '-a' '-b' '-c' '-d'.
     *  Short option only use as a bool value.
     * @var array
     */
    protected $_commondList = [
        /**
         * @see _command_help
         */
        'help' => [],
        '' => 'help',

        /**
         * @see _command_list
         */
        'list' => ['a', 's'],
        'l' => 'list',

        /**
         * @see _command_deploy
         */
        'deploy' => ['f', 'a', 'c', 'y'],
        'd' => 'deploy',

        /**
         * @see _command_undeploy
         */
        'undeploy' => ['f', 'y', 'c'],
        'ud' => 'undeploy',

        /**
         * @see _command_clean
         */
        'clean' => ['d'],

        /**
         * @see _command_git
         */
        'git' => ['y'],

        /**
         * @see _command_clone
         */
        'clone' => ['f', 'n'],

        /**
         * @see _command_show
         */
        'show' => ['a', 'v'],

        /**
         * @see _command_version
         * s: 简单模式
         */
        'version' => ['s'],
        'v' => 'version',
        'ver' => 'v',

        /**
         * @see _command_initialize
         */
        'initialize' => [],
        'init' => 'initialize',

        /**
         * @see _command_create
         */
        'create' => [],

        /**
         * @see _command_mapadd
         */
        'mapadd' => ['--map', 'f'],
        /**
         * @see _command_map
         */
        'map' => ['s', 'a'],

        /**
         * @see _command_mapdel
         */
        'mapdel' => [],

        /**
         * @see _command_persistent
         */
        'persistent' => ['--admin-shell', '--cwd'],
        'pss' => 'persistent',

        /**
         * @see _command_elevate_privileges
         */
        'elevate-privileges' => ['--cwd'],
        'ep' => 'elevate-privileges',

        /**
         * @see _command_cwd
         */
        'cwd' => [],
        'exit' => [],

        'remove' => ['d'],
        'rm' => 'remove',
        'disable' => [],
        'enable' => [],
    ];

    protected $_isPersistentMode = false;

    protected function _command_help($args)
    {
        $command = $args[0];
        $docs = $this->_getHelpDocument();

        if(!empty($command)) {
            $command = $this->_getTargetCommand($command);
        }

        if(empty($command)) {
            $commandList = array_filter(array_keys($this->_commondList));

            foreach($commandList as $i => $_command) {
                $_command = "{$this->crLSkyblue()}$_command  {$this->crGray()}". $docs[$this->_getTargetCommand($_command)]['simple'] . "{$this->crNull()}" ;
                $commandList [$i] = $_command;
            }

            $commands = '      '.implode("\n       ", $commandList);
            $command = '-';
        } else if(!isset($this->_commondList[$command])) {
            return $this->input("Command '%s' not found", $command);
        }

        $_COLORS = [];

        foreach($this->OUTPUT_COLOR as $key => $val) {
            $_COLORS ['cr' . $key] = $val;
        }

        extract($_COLORS);
        unset($_COLORS);

        $detail = $docs[$command]['detail'];
        $detail = str_replace('"', '\"', $detail);
        $detail = eval(sprintf('return "%s";', $detail));
        return $this->outputLine($detail . "\n");
    }

    protected function _command_cwd()
    {
        $this->outputLine(getcwd());
    }

    protected function _command_version()
    {
        if ($this->existsOption('s')) {
            echo MODMGR_VERSION;
        } else {
            echo 'Module Manager Version: ' . MODMGR_VERSION;
        }
    }

    protected function _command_persistent()
    {
        if($this->getOption('--cwd')) {
            chdir($this->getOption('--cwd'));
        }

        if ($this->existsOption('--admin-shell')) {
            if(PHP_OS == "WINNT") {
                $shell = strtolower(trim($this->getOption('--admin-shell')));

                if(empty($shell)) {
                    if(floatval(php_uname('r')) >= 7) {
                        $shell = "powershell";
                    } else {
                        $shell = "cmd";
                    }
                }

                $adminShells = [
                    'cmd' => 'cmd /k',
                    'powershell' => 'powershell -Command',
                    // 'bash' => 'bash -c',
                ];

                $shellApp = $adminShells[$shell];

                if($shell == 'cmd') {
                    $this->setOption('--nocolor');
                }

                $shellCmd = sprintf('%s', empty($shellApp) ? '' : $shellApp);

                $sudo = fs\path\join(dirname($this->_scriptPath), 'sudo.vbs');

                $cmd = sprintf('wscript "%s" %s php "%s" persistent %s --cwd "%s"',
                    $sudo, $shellCmd, $this->_scriptPath, $this->_packGlobalOptionsToStr(), getcwd());

            } else {
                $cmd = sprintf('sudo php "%s" persistent %s --cwd "%s"',
                    $this->_scriptPath, $this->_packGlobalOptionsToStr(), getcwd());
            }

            system($cmd);
            return;
        }

        $this->_isPersistentMode = true;
        $this->setOption('--persistent-mode');

        while(true) {
            $command = trim($this->input(" {$this->crLPurple()}MODMGR{$this->crNull()} {$this->crWhite()}%s> {$this->crNull()}", getcwd()));

            if('persistent' == strtolower($command) or 'pss' == strtolower($command)) {
                $command = '';
            }

            if(empty($command)) {
                continue;
            }

            if(strtolower($command) == "exit") {
                $this->output("{$this->crLGreen()}Bye!{$this->crNull()}");
                break;
            }

            $argv = \ary\concat([$this->_scriptPath], explode(' ', $command), explode(' ', $this->_packGlobalOptionsToStr()));
            new App($argv);
        }
    }

    protected function _command_show($args)
    {
        $data = [
            'module-path' => $this->_modulePath,
            'project-path' => $this->_projectPath,
            'script-path' => $this->_scriptPath
        ];

        if(empty($args)) {
            $this->setOption('a');
        }

        if (empty($args)) {
            if ($this->existsOption('a')) {
                if ($this->existsOption('v')) {
                    return $this->output(str\stringformat($data, true));
                } else {
                    return $this->output(str\stringformat(array_keys($data)));
                }
            }
        } else {
            return $this->output($data[$args[0]]);
        }
    }

    protected function _command_initialize()
    {
        if (is_dir(MODMGR_DIR_NAME)) {
            return $this->output("This directory does not need to be initialized again");
        }

        try {
            fs\mkdir(MODMGR_DIR_NAME);
            return $this->successLine("Initialized successfuly");
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    protected function _command_deploy($args)
    {
        if (empty($args)) {
            return $this->errorLine("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        if(!$this->multiModulesConfirm(count($modules), 'deployed')) {
            return false;
        }

        if (empty($modules)) {
            $this->errorLine("Parameter '%s' does not match any module.", $wildcard);
            return $this->infoLine("You can use 'list' command to get all available modules.");
        }

        foreach ($modules as $module) {
            $this->outputLine("Deploying module '{$this->crLSkyblue()}%s{$this->crNull()}'", $module);
            $this->_deployModule($module);
        }
    }

    protected function _command_undeploy($args)
    {
        if (empty($args)) {
            return $this->errorLine("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        if(!$this->multiModulesConfirm(count($modules), 'undeployed')) {
            return false;
        }

        if (empty($modules)) {
            $this->errorLine("Parameter '%s' does not match any module.", $wildcard);
            return $this->infoLine("You can use 'list' command to get all available modules.");
        }

        foreach ($modules as $module) {
            $this->outputLine("Undeploying module '{$this->crLWhite()}%s{$this->crNull()}'", $module);
            $this->_undeployModule($module);
        }
    }

    protected function _command_list($args)
    {
        $wildcard = $args[0];

        if ($this->existsOption('a')) {
            $modules = $this->_getAllModules($wildcard);
        } else {
            $modules = $this->_getAvailableModules($wildcard);
        }

        if(!empty($modules)) {
            foreach ($modules as $m) {
                if($this->isModuleAvailable($m)) {
                    $this->outputLine("%s", $m);
                } else {
                    $this->outputLine("{$this->crLYellow()}%s{$this->crNull()}%s",
                        $m, (!$this->existsOption('s') and $this->isModuleDisabled($m)) ? " {$this->crGray()}(disabled){$this->crNull()}": "");
                }
            }
        }

        if (!$this->existsOption('s')) {
            $this->infoLine("Total of %d module(s)", count($modules));
        }
    }

    protected function _command_create($args)
    {
        $moduleName = $args[0];

        if(empty($moduleName)) {
            return $this->errorLine('Missing a module name');
        }

        if($this->existsModule($moduleName)) {
            return $this->errorLine("The module '%s' already exists", $moduleName);
        }

        try {
            fs\mkdir(fs\path\join($this->_modulePath, $moduleName));
            io\writefile(fs\path\join($this->_modulePath, $moduleName, MODMGR_MAPPING_NAME), '');
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    protected function _command_mapadd($args)
    {
        $moduleName = $args[0];
        $path = $args[1];

        if(empty($moduleName)) {
            return $this->errorLine("Missing a module name");
        }

        if(!$this->existsModule($moduleName)) {
            return $this->errorLine("Module '%s' is not exists", $moduleName);
        }

        if(empty($path)) {
            return $this->errorLine("Missing source path");
        }

        if(!fs\exists($path)) {
            return $this->errorLine("Path '%s' is not exists", $path);
        }

        $subpath = fs\path\subpath($this->_projectPath, \fs\path\absolute($path));
        $mappingOption = $this->getOptionArray('--map');

        if(!$subpath && empty($mappingOption)) {
            return $this->errorLine("The file you specifiy is not in the project path, you need to use '--map' option to specity the source and target path.");
        }

        $source = $mappingOption[0];
        $source = empty($source) ? $subpath : $source;
        $target = $mappingOption[1];
        $target = empty($target) ? $source : $target;

        $source = \fs\path\standard($source);
        $target = \fs\path\standard($target);
        $moduleFilePath = fs\path\join($this->_modulePath, $moduleName, $source);
        $moduleFileDir = fs\path\parent($moduleFilePath);
        $mappings = (array)$this->_getModuleMapping($moduleName);

        foreach ($mappings as $mapping) {
            if(\fs\path\standard($mapping) == $target && !$this->existsOption('f')) {
                return $this->errorLine("This path '%s' is already in mapping list of module '%s'", $target, $moduleName);
            }
        }

        $mappings [$source]= $target;

        try {
            if(!fs\isdir($moduleFileDir)) {
                fs\mkdir($moduleFileDir, true);
            }

            fs\copy($path, $moduleFilePath);

            if(fs\exists($moduleFilePath)) {
                $mappingstr = $this->_translateMappingArrayToString($mappings);
                io\writefile(fs\path\join($this->_modulePath, $moduleName, MODMGR_MAPPING_NAME), $mappingstr, true);
                $this->successLine("%s => %s", $source, $target);
            }
        } catch(Exception $e) {
            return $this->errorLine($e->getMessage());
        }
    }

    protected function _command_map($args, $single=false)
    {
        $module = $args[0];

        if(empty($module)) {
            $module = "*";
        }

        if(!$single) {
            $modules = $this->_getAvailableModules($module);
            foreach ($modules as $module) {
                $this->_command_map([$module], true);
            }

            return;
        }

        $mappings = $this->_getModuleMapping($module);
        $sourcePrefix = '';
        $targetPrefix = '';
        $maxPrefix = 0;

        if($this->existsOption('a')) {
            $sourcePrefix = fs\path\join($this->_modulePath, $module);
            $targetPrefix = $this->_projectPath;
            $maxPrefix = strlen($sourcePrefix)+1;
        }

        $dc = 0;
        $all = 0;
        $str = '';
        $exceptioins = [];

        if(!empty($mappings)) {

            $i = 0;
            $max = $maxPrefix + $this->_getMappingMaxSourceLength($mappings);
            $ocwd = getcwd();

            foreach($mappings as $source => $target) {
                $i ++;
                $all ++;

                if($str != '') {
                    $str .= io\endline();
                }

                $status = "    ";
                try {
                    $targetFullPath = fs\path\join($this->_projectPath, $target);

                    if(fs\islink($targetFullPath)) {
                        chdir(dirname($targetFullPath));
                        $linkreal = realpath($targetFullPath);

                        if($linkreal) {
                            if(fs\path\standard(realpath(fs\path\join($this->_modulePath, $module, $source))) == fs\path\standard($linkreal)) {
                                $status = "{$this->crLSkyblue()}(D){$this->crNull()} ";
                                $dc ++;
                            }
                        }

                        chdir($ocwd);
                    }
                } catch(Exception $e) {
                    $exceptioins []= $e;
                }

                $str .= sprintf("{$this->crGray()}%03d: {$this->crNull()}$status{$this->crSkyblue()}%{$max}s{$this->crNull()} => {$this->crSkyblue()}%s{$this->crNull()}",
                    $i, fs\path\join($sourcePrefix,$source), fs\path\join($targetPrefix,$target));
            }
        }

        $incompleteStr = $all != $dc ? " {$this->crGray()}(incomplete){$this->crNull()}" : "";
        $this->outputLine("{$this->crLWhite()}%s {$this->crGray()}[{$this->crLGreen()}%d{$this->crGray()}+{$this->crLRed()}%d{$this->crGray()}={$this->crLWhite()}%d{$this->crGray()}]{$this->crNull()}$incompleteStr",
            $module, $dc, $all-$dc, $all);

        $showDetail = false;
        if(!empty($str) && !$this->existsOption('s')) {
            $this->outputLine($str);
            $showDetail = true;
        }

        foreach($exceptioins as $e) {
            $this->_processException($e);
        }

        if($showDetail) {
            $this->outputLine("");
        }
    }

    protected function _command_mapdel($args) {
        $module = $args[0];

        if(empty($module)) {
            return $this->errorLine("Missing a module name");
        }

        if(!$this->existsModule($module)) {
            return $this->errorLine("Module '%s' is not exists", $module);
        }

        if(!$this->isModuleAvailable($module)) {
            return $this->errorLine("This module '%s' is not available", $module);
        }

        $index = $args[1];

        if(empty($index)) {
            return $this->errorLine("Missing a index value");
        }

        $index = intval($index);

        if($index <= 0) {
            return $this->errorLine('Index value must be a number that greate than 0');
        }

        $mappings = $this->_getModuleMapping($module);
        $i = 1;

        foreach($mappings as $key => $mapping) {
            if($i == $index) {
                unset($mappings[$key]);
                break;
            }

            $i ++;
        }

        io\writefile(fs\path\join($this->_modulePath, $module, MODMGR_MAPPING_NAME),
            $this->_translateMappingArrayToString($mappings), true);
        return true;
    }

    protected function _command_git($args)
    {
        $moduleWildcard = $args[0];
        unset($args[0]);
        $args = array_values($args);

        if(empty($moduleWildcard)) {
            return $this->errorLine("Missing a module name");
        }

        $modules = $this->_getAllModules($moduleWildcard);
        $ocwd = getcwd();

        if(!$this->multiModulesConfirm(count($modules), "executed git command")) {
            return false;
        }

        foreach ($modules as $module) {
            $cmd = sprintf('git %s', implode(' ', $args));
            $this->outputLine("{$this->crLWhite()}Module: {$this->crSkyblue()}%s{$this->crNull()} > {$this->crGray()}%s{$this->crNull()}", $module, $cmd);
            $moduleParent = fs\path\join($this->_modulePath, $module);
            chdir($moduleParent);
            system($cmd);
            chdir($ocwd);
        }
    }

    protected function _command_elevate_privileges($args) {
        if(PHP_OS != "WINNT") {
            return;
        }

        $shell = $args[0];

        if(empty($shell)) {
            if(floatval(php_uname('r')) >= 8) {
                $shell = "powershell";
            } else {
                $shell = "cmd";
            }
        }

        $adminShells = [
            'cmd' => 'cmd --none-- /k cd /d --quote-- ',
            'powershell' => 'powershell.exe -noexit -command Set-Location -literalPath ',
            'gitbash' => 'git-bash --cd=',
        ];

        $shellCmd = $adminShells[strtolower($shell)];
        $shellCmd = $shellCmd ? $shellCmd : $shell;

        if($this->getOption('--cwd')) {
            chdir($this->getOption('--cwd'));
        }

        $sudo = fs\path\join(dirname($this->_scriptPath), 'sudo.vbs');
        $cmd = sprintf('wscript "%s" %s"%s"', $sudo, $shellCmd, getcwd());
        system($cmd);
    }

    protected function _command_clone($args)
    {
        $repo = $args[0];

        if(empty($repo)) {
            return $this->errorLine("Missing a repository uri.");
        }

        $module = preg_replace("#\.git$#i", '', \fs\path\basename($repo));

        if($this->existsOption('f')) {
            $modulePath = fs\path\join($this->_modulePath, $module);

            if(fs\exists($modulePath)) {
                try {
                    fs\rm($modulePath, true);
                } catch(Exception $e) {
                    return $this->_processException($e);
                }
            }
        }

        $cmd = sprintf('git clone %s', $repo);
        $ocwd = getcwd();
        chdir($this->_modulePath);
        system($cmd);
        chdir($ocwd);

        if(!$this->existsOption('n')) {
            $this->_deployModule($module);
        }

        return true;
    }

    protected function _command_clean($args)
    {
        $path = $args[0];

        if(empty($path)) {
            $path = getcwd();
        }

        $path = fs\path\absolute($path);
        $self = $this;

        if(!is_dir($path)) {
            return $this->errorLine("Path '%s' is not exists or not a folder", $path);
        }

        $cleanFunc = function ($path) use (&$cleanFunc, $self) {

            try {
                $dir = dir($path);
            } catch(Exception $e) {
                $self->_processException($e);
                return ;
            }

            while($file = $dir->read()) {
                if($file == "." or $file == ".." or $file == MODMGR_DIR_NAME) {
                    continue;
                }

                $filePath = \fs\path\standard("$path/$file");

                if (\fs\islink($filePath)) {
                    try {
                        $linkInfoValue = \realpath($filePath);

                        if (!$linkInfoValue) {
                            \fs\rm($filePath);
                            $self->successLine("Removed invalid link: '%s'", $filePath);
                        }

                    } catch (\Exception $e) {
                        $self->_processException($e);
                    }
                } else if (is_dir($filePath)) {
                    $cleanFunc($filePath);

                    if($this->existsOption('d')) {
                        if(fs\isempty($filePath)) {
                            try {
                                rmdir($filePath);
                                $self->successLine("Removed empty directory: '%s'", $filePath);
                            } catch (Exception $e) {
                                $self->_processException($e);
                            }
                        }
                    }
                }
            }

            $dir->close();
        };

        $cleanFunc($path);
        return true;
    }

    protected function _command_remove($args)
    {
        if (empty($args)) {
            return $this->errorLine("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];

        if(empty($wildcard)) {
            return $this->error("Missing a module name");
        }

        $modules = $this->_getAllModules($wildcard);

        if(!$this->multiModulesConfirm(count($modules), 'removed')) {
            return false;
        }

        if (empty($modules)) {
            $this->errorLine("Parameter '%s' does not match any module.", $wildcard);
            return $this->infoLine("You can use 'list' command to get all available modules.");
        }

        foreach ($modules as $module) {
            $this->outputLine("Removing module '{$this->crLWhite()}%s{$this->crNull()}'", $module);

            if($this->existsOption('d')) {
                $this->_undeployModule($module);
            }

            $moduleFolder = fs\path\join($this->_modulePath, $module);

            try {
                fs\rm($moduleFolder, true);
                $this->successLine("Module '%s' has beed removed", $module);
            } catch (Exception $e) {
                $this->_processException($e);
            }
        }

        return true;
    }

    protected function _command_disable($args)
    {
        if (empty($args)) {
            return $this->errorLine("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];

        if(empty($wildcard)) {
            return $this->error("Missing a module name");
        }

        $modules = $this->_getAvailableModules($wildcard);

        if(!$this->multiModulesConfirm(count($modules), 'disabled')) {
            return false;
        }

        if (empty($modules)) {
            return $this->infoLine("Parameter '%s' does not match any enabled module.", $wildcard);
        }

        foreach ($modules as $module) {
            $this->_disableModule($module);
        }

        return true;
    }

    protected function _command_enable($args)
    {
        if (empty($args)) {
            return $this->errorLine("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];

        if(empty($wildcard)) {
            return $this->error("Missing a module name");
        }

        $modules = $this->_getAllModules($wildcard);

        foreach($modules as $i => $m) {
            if(!$this->isModuleDisabled($m)) {
                unset($modules[$i]);
            }
        }

        $modules = array_values($modules);

        if(!$this->multiModulesConfirm(count($modules), 'enabled')) {
            return false;
        }

        if (empty($modules)) {
            return $this->infoLine("Parameter '%s' does not match any disabled module.", $wildcard);
        }

        foreach ($modules as $module) {
            $this->_enableModule($module);
        }

        return true;
    }
}

/**
 * Class BaseOptionSupport
 */
abstract class BaseOptionSupport
{
    protected $_globalOptionsSupports = ['--nocolor', '--nooutput', '--persistent-mode', '--help'];
    protected $_options=[];

    /**
     * --long-option 用法 $this->existsOption('--long-option')
     * -xad 短参数用法 $this->existsOption('a')
     * @param $optionKey
     * @return bool
     */
    public function existsOption($optionKey)
    {
        return isset($this->_options[$optionKey]);
    }

    public function getOption($optionKey, $index=0)
    {
        return $this->_options[$optionKey][$index];
    }

    public function getOptionArray($optionKey) {
        return $this->_options[$optionKey];
    }

    public function setOption($key, $value=[true])
    {
        $this->_options[$key] = $value;
        return $this;
    }

    public function removeOption($key) {
        unset($this->_options[$key]);
        return $this;
    }
}

/**
 * Class BaseOutputInput
 * @method crNull
 * @method crRed
 * @method crBlack
 * @method crGreen
 * @method crYellow
 * @method crBlue
 * @method crPurple
 * @method crSkyblue
 * @method crWhite
 * @method crLRed
 * @method crGray
 * @method crLGreen
 * @method crLYellow
 * @method crLBlue
 * @method crLPurple
 * @method crLSkyblue
 * @method crLWhite
 */
abstract class BaseOutputInput extends BaseOptionSupport
{
    protected $_isFirstOutput = true;
    protected $_enableOutput = true;
    public $OUTPUT_COLOR = [
        "NULL" => "\033[0;0m",

        'BLACK' => "\033[0;30m",
        'RED' => "\033[0;31m",
        'GREEN' => "\033[0;32m",
        'YELLOW' => "\033[0;33m",
        'BLUE' => "\033[0;34m",
        'PURPLE' => "\033[0;35m",
        'SKYBLUE' => "\033[0;36m",
        'WHITE' => "\033[0;37m",

        'GRAY' => "\033[1;30m",
        'LRED' => "\033[1;31m",
        'LGREEN' => "\033[1;32m",
        'LYELLOW' => "\033[1;33m",
        'LBLUE' => "\033[1;34m",
        'LPURPLE' => "\033[1;35m",
        'LSKYBLUE' => "\033[1;36m",
        'LWHITE' => "\033[1;37m",
    ];

    public function input($msg)
    {
        if(count(func_get_args()) > 0) {
            call_user_func_array([$this, 'output'], func_get_args());
        }

        return fgets(STDIN);
    }

    public function inputYN() {
        while(true) {
            $result = trim(strtoupper(call_user_func_array([$this, 'input'], func_get_args())));

            if($result == "Y") {
                return true;
            }

            if($result == "N") {
                return false;
            }
        }

        return false;
    }

    public function output($str='')
    {
        if($this->existsOption('--nooutput')) {
            return null;
        }

        $args = func_get_args();
        $content = $args[0];

        if($this->existsOption('--nocolor')) {
            $content = preg_replace("#\033\[([01];)?([0-9]{1,2};)?[0-9]{1,2}m#", '', $content);
        } else {
            $args[0] = (string)$content;
        }

        $args[0] = $content;
        echo call_user_func_array('sprintf', $args);
        return null;
    }

    public function outputLine($str='')
    {
        call_user_func_array([$this, 'output'], func_get_args());
        echo io\endline();
        return null;
    }

    public function errorLine()
    {
        call_user_func_array([$this, 'error'], func_get_args());
        $this->outputLine();
        return false;
    }

    public function warningLine()
    {
        call_user_func_array([$this, 'warning'], func_get_args());
        $this->outputLine();
        return false;
    }

    public function infoLine()
    {
        call_user_func_array([$this, 'info'], func_get_args());
        $this->outputLine();
        return false;
    }

    public function successLine()
    {
        call_user_func_array([$this, 'success'], func_get_args());
        $this->outputLine();
        return null;
    }

    public function error()
    {
        $args = func_get_args();
        $args[0] = "{$this->crWhite()}[{$this->crLRed()}error{$this->crWhite()}]{$this->crNull()} " . $args[0];
        call_user_func_array([$this, 'output'], $args);
        return false;
    }

    public function warning()
    {
        $args = func_get_args();
        $args[0] = "{$this->crWhite()}[{$this->crLYellow()}warning{$this->crWhite()}]{$this->crNull()} " . $args[0];
        call_user_func_array([$this, 'output'], $args);
        return false;
    }

    public function info()
    {
        $args = func_get_args();
        $args[0] = "{$this->crGray()}{$args[0]}{$this->crNull()}";
        call_user_func_array([$this, 'output'], $args);
        return false;
    }

    public function success()
    {
        $args = func_get_args();
        $args[0] = "{$this->crWhite()}[{$this->crGreen()}ok{$this->crWhite()}]{$this->crNull()} {$args[0]}";
        call_user_func_array([$this, 'output'], $args);
        return null;
    }

    public function disableOutput() {
        $this->setOption('--nooutput');
    }

    public function enableOutput() {
        $this->removeOption('--nooutput');
    }

    public function __call($functionName, $t)
    {
        if(substr($functionName, 0,2) == 'cr') {
            $key = strtoupper(substr($functionName, 2));
            return $this->OUTPUT_COLOR[$key];
        }

        return null;
    }
}

/**
 * Class BaseApp
 */
abstract class BaseApp extends BaseOutputInput
{
    protected $_commondList = [];
    protected $_noNeedToInit = ['help', 'version', 'initialize', 'persistent', 'cwd',
        'elevate-privileges', 'clean', 'list', 'show'];

    protected $_command;
    protected $_targetCommand;
    protected $_commandEscape;
    protected $_commandArguments = [];

    protected $_scriptPath;
    protected $_modulePath;
    protected $_projectPath;

    public function __construct($argv)
    {
        $argv = $this->_init($argv);

        if(!$this->_parseAppArguments($argv)) {
            return false;
        }

        if(empty($this->_command)) {
            $this->_setCommand('help');
        }

        if(!$this->_checkArguments()) {
            return false;
        }

        if($this->existsOption('--help')) {
            if($this->_targetCommand != 'help') {
                $this->_commandArguments = [$this->_targetCommand];
                $this->_setCommand('help');
            }
        }

        if(!$this->_checkInit()) {
            return false;
        }

        $this->_dispatch();
        return true;
    }

    protected function _init($argv)
    {
        $this->_initErrorHandle();
        $this->_scriptPath = fs\path\standard($argv[0]);

        unset($argv[0]);

        // $this->_setCommand($argv[0]);
        // unset($argv[1]);

        $this->_findModulePath();

        return $argv;
    }

    protected function _setCommand($command)
    {
        $this->_command = $command;
        $this->_targetCommand = $this->_getTargetCommand($command);

        if(empty($this->_command)) {
            $this->_command = $this->_targetCommand;
        }

        $this->_commandEscape = $this->_escapeCommand($this->_targetCommand);
    }

    protected function _escapeCommand($command)
    {
        return str_replace('-', '_', $this->_targetCommand);
    }

    protected function _packGlobalOptionsToStr()
    {
        $array = [];
        foreach($this->_globalOptionsSupports as $key) {
            if(isset($this->_options[$key])) {
                $array []= $key;

                $val = $this->_options[$key];

                if(!is_bool($this->getOption($key)) && !empty($val)) {
                    $array []= is_array($this->_options[$key]) ? implode(' ', $this->_options[$key]) : "$val";
                }
            }
        }

        return implode(" ", $array);
    }

    protected function _checkInit()
    {
        if(empty($this->_modulePath)) {
            if(!in_array($this->_targetCommand, $this->_noNeedToInit)) {
                $this->errorLine("The current directory has not been initialized yet.");
                $this->outputLine("Directory '%s'", getcwd());
                $this->infoLine("You can use 'init' command to initialize");
                return false;
            }
        }

        return true;
    }

    protected function _findModulePath()
    {
        $current = getcwd();

        while(!is_dir($current . '/' . MODMGR_DIR_NAME)) {
            $parent = dirname($current);

            if($current == $parent) {
                return false;
            }

            $current = $parent;
        }

        $this->_projectPath = fs\path\standard($current);
        $this->_modulePath = fs\path\standard($current . '/' . MODMGR_DIR_NAME);
        return true;
    }

    protected function _getAvailableModules($wildcard)
    {
        $result = [];

        foreach($this->_getAllModules($wildcard) as $module) {
            if($this->isModuleAvailable($module)) {
                $result []= $module;
            }
        }

        return $result;
    }

    protected function _initErrorHandle()
    {
        set_error_handler(function($errno, $errmsg) {
            if($errno == E_ERROR or $errno == E_USER_ERROR) {
                throw new Exception($errmsg, 2);
            }

            if($errno == E_WARNING or $errno == E_USER_WARNING) {
                throw new Exception($errmsg, 1);
            }
        });
    }

    protected function _disableModule($module)
    {
        $modulePath = fs\path\join($this->_modulePath, $module);
        $disabledFile = fs\path\join($modulePath, MODMGR_DISABLED);

        if(fs\exists($disabledFile)) {
            return $this->infoLine("The module '%s' had already been disabled", $module);
        }

        try {
            file_put_contents($disabledFile, 'MODMGR ' . MODMGR_VERSION);
            $this->successLine("Module '%s' has been disabled", $module);
        } catch (Exception $e) {
            return $this->_processException($e);
        }

        return true;
    }

    protected function _enableModule($module) {
        $modulePath = fs\path\join($this->_modulePath, $module);
        $disabledFile = fs\path\join($modulePath, MODMGR_DISABLED);

        if(!fs\exists($disabledFile)) {
            return $this->infoLine("The module '%s' had already been enabled", $module);
        }

        try {
            fs\rm($disabledFile);
            $this->successLine("Module '%s' has been enabled", $module);
        } catch (Exception $e) {
            return $this->_processException($e);
        }

        return true;
    }

    protected function _getAllModules($wildcard) {
        $result = [];

        foreach(fs\subdirs(fs\path\join($this->_modulePath)) as $module) {
            if(empty($wildcard) || str\matchwildcard($module, $wildcard)) {
                $result []= $module;
            }
        }

        return $result;
    }

    public function existsModule($module)
    {
        return fs\isdir(fs\path\join($this->_modulePath, $module));
    }

    public function isModuleAvailable($module) {
        if($this->isModuleDisabled($module)) {
            return false;
        }

        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            return false;
        }

        return true;
    }

    public function isModuleDisabled($module) {
        return fs\exists(fs\path\join($this->_modulePath, $module, MODMGR_DISABLED));
    }

    protected function _translateMappingArrayToString($mappings)
    {
        $str = '';
        $max = $this->_getMappingMaxSourceLength($mappings);

        foreach ($mappings as $source => $target) {
            if($str != '') {
                $str .= "\n";
            }

            if($source == $target) {
                $str .= sprintf("%{$max}s", $source);
            } else {
                $str .= sprintf("%{$max}s %s", $source, $target);
            }
        }

        return $str;
    }

    protected function _getMappingMaxSourceLength($mappings)
    {
        $max = 0;

        foreach($mappings as $source => $target) {
            if($max < strlen($source)) {
                $max = strlen($source);
            }
        }

        return $max;
    }

    protected function _undeployModule($module)
    {
        if(!fs\isdir(fs\path\join($this->_modulePath, $module))) {
            return $this->errorLine("Module '%s' is not exists.", $module);
        }

        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            return false;
        }

        $itemCount = 0;

        foreach ($mappings as $modulePath => $targetPath) {
            $targetFullPath = fs\path\join($this->_projectPath, $modulePath);

            try {
                if ($this->existsOption('c')) {
                    if (fs\exists($targetFullPath)) {
                        fs\rm($targetFullPath, true);
                        $this->outputLine("Removed: '%s'", $targetFullPath);
                    }
                } else {
                    if (fs\exists($targetFullPath)) {
                        if(!fs\islink($targetFullPath) && !$this->existsOption('f')) {
                            $this->warningLine("There is an exist file or folder '%s'", $targetFullPath);
                            $this->infoLine("You can use '-f' option to forece remove exists file or folder.");
                        } else {
                            fs\rm($targetFullPath, true);
                            $this->successLine("Removed '%s'", $targetFullPath);
                        }

                        $itemCount++;
                    }
                }
            } catch (Exception $e) {
                $itemCount++;
                $this->_processException($e, "{$this->crGray()} During undeployed '$targetFullPath'{$this->crNull()}");
            }
        }

        if(0 == $itemCount) {
            $this->outputLine("{$this->crGray()}(No item needs to be undeployed){$this->crNull()}");
        }
    }

    protected function _processException(Exception $e, $subfixMsg='')
    {
        if($e->getCode() == 2) {
            $this->errorLine($e->getMessage() . $subfixMsg);
        } else if($e->getCode() == 1){
            $this->warningLine($e->getMessage() . $subfixMsg);
        }
    }

    protected function _deployModule($module)
    {
        if(!fs\isdir(fs\path\join($this->_modulePath, $module))) {
            return $this->errorLine("Module '%s' is not exists.", $module);
        }

        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            return $this->errorLine("Module '%s' is not available", $module);
        }

        $newItemCount = 0;

        foreach ($mappings as $modulePath => $targetPath) {
            $moduleFullPath = fs\path\join($this->_modulePath, $module, $modulePath);
            $targetFullPath = fs\path\join($this->_projectPath, $modulePath);

            try {
                if($this->existsOption('c')) {
                    if(fs\exists($targetFullPath)) {
                        if($this->existsOption('f')) {
                            fs\rm($targetFullPath);
                            fs\copy($moduleFullPath, $targetFullPath);
                            $this->outputLine("Copy '%s' to '%s'", $moduleFullPath, $targetFullPath);
                        } else {
                            $this->errorLine("Can't copy file or directory to '%s', the path is already exists", $targetFullPath);
                        }
                    } else {
                        $newItemCount ++;
                        fs\copy($moduleFullPath, $targetFullPath);
                        $this->outputLine("Deployed: %s", $moduleFullPath);
                    }
                } else {
                    $linkval = $moduleFullPath;
                    if(!$this->existsOption('a')) {
                        $linkval = fs\path\relative(fs\path\parent($targetFullPath), $moduleFullPath);
                    }

                    if(fs\islink($targetFullPath) && !$this->existsOption('f')) {
                        $realpath = $linkval;

                        if(!$this->existsOption('c')) {
                            $realpath = realpath(fs\path\join(dirname($targetFullPath), $linkval));
                        }

                        $realpath = fs\path\standard($realpath);
                        $readlink = fs\path\standard(fs\readlink($targetFullPath));

                        if($readlink == $realpath) {
                            continue;
                        }
                    }

                    $newItemCount ++;

                    if(fs\exists($targetFullPath)) {
                        if($this->existsOption('f')) {
                            $result = fs\rm($targetFullPath,true);

                            if(true !== $result) {
                                $this->errorLine("Can't remove link '%s'. %s", $targetFullPath, $result);
                                continue;
                            }
                        } else {
                            $this->errorLine("Can't create link '%s', the path is already exists", $targetFullPath);
                            continue;
                        }
                    }

                    $targetParent = dirname($targetFullPath);

                    if(!fs\isdir($targetParent)) {
                        fs\mkdir($targetParent, true);
                    }

                    $oldcwd = getcwd();
                    $relativeTargetPath = fs\path\subpath($this->_projectPath, $targetFullPath);
                    chdir(dirname($targetFullPath));

                    if(!fs\exists($linkval)) {
                        $this->errorLine("Link to a not exists file or folder: '%s'", $linkval);
                        continue;
                    }

                    $result = fs\symlink($targetFullPath, $linkval);

                    if($result===true) {
                        $this->successLine("%s => %s", $linkval, $relativeTargetPath);
                    } else {
                        $this->errorLine($result);
                    }

                    chdir($oldcwd);
                }
            } catch (Exception $e) {
                $newItemCount ++;
                $this->_processException($e, "{$this->crGray()} During deployed '$targetFullPath'{$this->crNull()}");
            }
        }

        if(0 == $newItemCount) {
            $this->outputLine("{$this->crGray()}(No item needs to be deployed){$this->crNull()}");
        }
    }

    protected function _getModuleMapping($module)
    {
        $moduleMappingFile = fs\path\join($this->_modulePath, $module, MODMGR_MAPPING_NAME);

        if(!fs\exists($moduleMappingFile)) {
            return null;
        }

        $content = trim(io\readfile($moduleMappingFile));

        if(empty($content)) {
            return null;
        }

        $mappingTems = array_filter(array_map("trim", explode("\n", $content)));
        $mappings = [];

        foreach($mappingTems as $mapping) {
            $mapping = str_replace(["\t", "\r"], ' ', $mapping);
            $mapping = explode(' ', trim($mapping), 2);
            $mapping = array_map('trim', $mapping);

            if(empty($mapping[1])) {
                $mapping[1] = $mapping[0];
            }

            $mappings [$mapping[0]] = $mapping[1];
        }

        return $mappings;
    }

    protected function _parseAppArguments($argv)
    {
        $argv = array_values($argv);
        $argc = count($argv);
        $argsHandle = &$this->_commandArguments;
        $valueEscapeKey = '';

        for($i=0; $i<$argc; $i++) {
            $arg = trim($argv[$i]);

            if($arg == '--') {
                for($j=$i+1; $j<$argc; $j++) {
                    $this->_commandArguments []= $argv[$j];
                }

                return true;
            }

            if(substr($arg, 0, 2) == '--') {
                $this->_options[$arg] = [];
                $valueEscapeKey = str_replace('-', '_', ltrim($arg, '-'));
                $argsHandle = &$this->_options[$arg];
            } else if($arg[0] == '-') {
                foreach (str_split(substr($arg, 1), 1) as $key) {
                    if($this->existsOption($key)) {
                        return $this->errorLine("Duplicate option: '%s'", $key);
                    }

                    $this->_options[$key] = true;
                }
            } else {
                if(empty($this->_command)) {
                    $this->_setCommand($arg);
                    continue;
                }

                $value = $this->_processingValue(trim($arg));
                $valueMethod = "_processingValue_{$this->_commandEscape}";

                if(method_exists($this, $valueMethod)) {
                    $value = $this->$valueMethod($value);
                }

                if(!empty($valueEscapeKey)) {
                    $funcName = "Command_{$this->_commandEscape}::{$valueEscapeKey}";

                    if(function_exists($funcName)) {
                        $value = $funcName($value);
                    }
                }

                $argsHandle []= $value;
            }
        }

        return true;
    }

    protected function _checkArguments()
    {
        if($this->_isCommandNotFound()) {
            return $this->errorLine("Command '%s' not found", $this->_command);
        }

        $supportOptions = $this->_getCommandSupportOptions();

        foreach ($this->_options as $key => $option) {
            if(!in_array($key, $supportOptions) && !in_array($key, $this->_globalOptionsSupports)) {
                return $this->errorLine("Option '%s' is not supported in command '%s'", $key, $this->_command);
            }
        }

        return true;
    }

    public function multiModulesConfirm($count, $opmsg)
    {
        if($count > 1 && !$this->existsOption('y')) {
            $result = $this->inputYN("{$this->crWhite()}There are $count modules will be $opmsg, are you sure? (y/n):{$this->crNull()} ");
            // $this->_isFirstOutput = true;
            return $result;
        }

        return true;
    }

    protected function _getTargetCommand($command=null)
    {
        $command = strval($command);

        while(is_string($this->_commondList[$command])) {
            $newCommand = $this->_commondList[$command];

            if($command == $newCommand) {
                return 'help';
            }

            $command = $newCommand;
        }

        return $command;
    }

    protected function _getCommandSupportOptions() {
        return $this->_commondList[$this->_targetCommand];
    }

    protected function _isCommandNotFound() {
        if(!isset($this->_commondList[$this->_targetCommand])) {
            return true;
        }

        if(!method_exists($this, $this->_getDispatchMethodName())) {
            return true;
        }

        return false;
    }

    protected function _processingValue($val)
    {
        return $val;
    }

    protected function _dispatch()
    {
        $this->{$this->_getDispatchMethodName()}($this->_commandArguments);
    }

    protected function _getDispatchMethodName()
    {
        return "_command_{$this->_commandEscape}";
    }

    protected function _getHelpDocument()
    {
        $phpContent = file_get_contents(__FILE__);
        preg_match("#/\*\*[\s\S]*@modmgr-help\s([\s\S]*?)\*/#", $phpContent, $matchs);
        $segments = explode("\n", $matchs[1]);
        $document = [];
        $index = '-';
        $indexFlag = "@modmgr-help-";
        $indexFlagLen = strlen($indexFlag);

        foreach($segments as $segment) {
            $segment = rtrim(preg_replace("#^\s\*+\s{0,1}#", '', $segment));

            if(substr($segment, 0, $indexFlagLen) == $indexFlag) {
                $document[$index]['detail'] = ' ' . trim(implode(\io\endline(), (array)$document[$index]['detail']));
                $index = substr($segment, $indexFlagLen);
                continue;
            }

            if(preg_match("#^@d #", $segment)) {
                $segment = substr($segment, 3);
                $document[$index]['simple'] = $segment;
            }

            $document[$index]['detail'] []= ' '.$segment;
        }

        $document[$index]['detail'] = ' ' . trim(implode(\io\endline(), $document[$index]['detail']));
        return $document;
    }
}

/**
 * Start application
 */
new App($_SERVER['argv']);