<?php
/**
 * modmgr php版本
 * -------------------------
 * @modmgr-help
 * Usage: modmgr [command] [commandArguments] [options] [-- [commandArguments]]
 *
 * command:
 *     MODMGR supports commands bellow:
 *
 * {$commands}
 *
 *     You can use `modmgr help [command]` or `modmgr [command] --help` to get the command help you need
 *
 * commandArguments:
 *     Use `modmgr help [command]` or `modmgr [command] --help` to get the more details
 *
 * options:
 *     Use `modmgr help [command]` or `modmgr [command] --help` to get the more details
 *
 * @modmgr-help-help
 * Usage: modmgr help [command]
 *
 * @d Show detailed help documentation for a command
 *
 * Such as `modmgr help deploy`
 *
 * @modmgr-help-list
 * Usage: modmgr list [wildcard] [-as]
 *        modmgr l [wildcard] [-as]
 *
 * @d List the modules that matching wildcard
 *
 * wildcard:
 *     [wildcard] use '*' to match any character, use '?' to match one character
 *     If [wildcard] is not specified, the default is '*'
 *
 *     e.g. `modmgr list "mod_ave*"` matchs all modules that begin with 'mod_ave'
 *
 * options:
 *     -a: List all modules.
 *         If this option is not specified, only available modules will be listed
 *     -s: Simple mode.
 *
 * @modmgr-help-deploy
 * Usage: modmgr deploy [wildcard] [-facy]
 *        modmgr d [wildcard] [-facy]
 *
 * @d Deploy modules which matches the wildcard
 *
 * wildcard:
 *     [wildcard] use '*' to match any character, use '?' to match one character
 *     If [wildcard] is not specified, the default is '*'
 *
 * options:
 *     -f: Force deploy. Remove the files, folders, and symbolic links that existed before deployment
 *     -a: Using absolute path to create symbolic links. This option is invalid if '-c' is specified
 *     -c: Copy files or folders directly instead of creating symbolic links
 *     -y: Does not display confirm message when operating on multiple modules
 *
 * @modmgr-help-undeploy
 * Usage: modmgr undeploy [wildcard] [-fcy]
 *        modmgr ud [wildcard] [-fcy]
 *
 * @d Undeploy modules which matches the wildcard
 *
 * wildcard:
 *     [wildcard] use '*' to match any character, use '?' to match one character.
 *     If [wildcard] is not specified, the default is '*'
 *
 * options:
 *     -f: Force deploy. Remove the files, folders, and symbolic links that existed before deployment
 *     -c: Remove files, folders or symbocli links that mapping in module
 *         If this option is not specified, only symbocli links will be removed
 *     -y: Does not display confirm message when operating on multiple modules
 *
 * @modmgr-help-clean
 * Usage: modmgr clean [path] [-d]
 *
 * @d Clean broken symbocli links and empty directory tree
 *
 * path:
 *
 */
define('MODMGR_VERSION', '0.1.0');
define('ERROR_REPORTING', E_ALL ^ E_NOTICE ^ E_STRICT);
define('MODMGR_DIR_NAME', '.modman');
define('MODMGR_MAPPING_NAME', 'modman');

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
         * a: 显示全部模块
         * s: 简单模式
         */
        'list' => ['a', 's'],
        'l' => 'list',

        /**
         * @see _command_deploy
         * 部署模块
         * f: 强制部署代码。删除已存在的文件或链接
         * a: 部署代码链接时，使用绝对路径创建链接。（如果指定了 -c 选项，则此选项会被忽略）
         * c: 复制文件或目录进行部署，而不是创建符号链接
         * y: 忽略多模块时的确认信息
         */
        'deploy' => ['f', 'a', 'c', 'y'],
        'd' => 'deploy',

        /**
         * @see _command_undeploy
         * f: 强制卸载模块，删除链接或文件
         * y: 忽略多模块时的确认信息
         */
        'undeploy' => ['f', 'y', 'c'],
        'ud' => 'undeploy',

        /**
         * @see _command_clean
         * 清除无效的链接
         * d: 同时清除空目录树
         */
        'clean' => ['d'],

        /**
         * @see _command_git
         * y: 忽略多模块时的确认信息
         */
        'git' => ['y'],

        /**
         * @see _command_clone
         * f: 强制克隆，删除已存在的目录
         * n: 只克隆代码， 不进行部署
         */
        'clone' => ['f', 'n'],

        /**
         * @see _command_show
         * a: 显示所有程序变量名
         * v: 显示值
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
         * 创建模块
         */
        'create' => [],

        /**
         * @see _command_mapadd
         * 添加模块映射
         * --map: 指定映射
         * f: 覆盖已存在的map
         */
        'mapadd' => ['--map', 'f'],
        /**
         * @see _command_map
         * 显示模块的映射
         * s: 简单模式
         * a: 显示绝对路径
         */
        'map' => ['s', 'a'],

        /**
         * @see _command_mapdel
         */
        'mapdel' => [],

        /**
         * @see _command_persistent
         * 常驻模式
         * --admin-shell: 提升权限并在指定的shell中运行. 默认 cmd, 支持powershell
         * --cwd: 指定工作目录
         */
        'persistent' => ['--admin-shell', '--cwd'],
        'pss' => 'persistent',

        /**
         * @see _command_elevate_privileges
         * 提升权限
         */
        'elevate-privileges' => ['--cwd'],
        'ep' => 'elevate-privileges',

        /**
         * @see _command_cwd
         * 显示工作路径
         */
        'cwd' => [],
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
        return $this->outputLine($detail);
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
                // $this->_isFirstOutput = true;
                continue;
            }

            if(strtolower($command) == "exit") {
                // $this->_isFirstOutput = true;
                $this->output("{$this->crLGreen()}Bye!{$this->crNull()}");
                break;
            }

            // $cmd = sprintf('php "%s" %s %s', $this->_scriptPath, $command, $this->_packGlobalOptionsToStr());
            // system($cmd);

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

        if (empty($args)) {
            if ($this->existsOption('a')) {
                if ($this->existsOption('v')) {
                    return $this->output(str\stringformat($data, true));
                } else {
                    return $this->output(str\stringformat(array_keys($data)));
                }
            }

            $this->error("Missing the value key.");
            return $this->info("Use 'help' command to get help.");
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
            return $this->success("Initialized successfuly");
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    protected function _command_deploy($args)
    {
        if (empty($args)) {
            return $this->error("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        if(!$this->multiModulesConfirm(count($modules), 'deployed')) {
            return false;
        }

        if (empty($modules)) {
            $this->error("Parameter '%s' does not match any module.", $wildcard);
            return $this->info("You can use 'list' command to get all available modules.");
        }

        foreach ($modules as $module) {
            $this->outputLine("Deploying module '{$this->crLSkyblue()}%s{$this->crNull()}'", $module);
            $this->_deployModule($module);
        }
    }

    protected function _command_undeploy($args)
    {
        if (empty($args)) {
            return $this->error("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        if(!$this->multiModulesConfirm(count($modules), 'undeployed')) {
            return false;
        }

        if (empty($modules)) {
            $this->error("Parameter '%s' does not match any module.", $wildcard);
            return $this->info("You can use 'list' command to get all available modules.");
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
            if($this->existsOption('t')) {
                foreach ($modules as $m) {
                    if($this->isModuleAvailable($m)) {
                        $this->outputLine("%s", $m);
                    } else {
                        $this->outputLine("{$this->crLYellow()}%s{$this->crNull()}", $m);
                    }
                }
            } else {
                $this->outputLine("{$this->crLWhite()}%s{$this->crNull()}", str\stringformat($modules));
            }
        }

        if (!$this->existsOption('s')) {
            $this->info("Total of %d module(s)", count($modules));
        }
    }

    protected function _command_create($args)
    {
        $moduleName = $args[0];

        if(empty($moduleName)) {
            return $this->error('Missing a module name');
        }

        if($this->existsModule($moduleName)) {
            return $this->error("The module '%s' already exists", $moduleName);
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
            return $this->error("Missing a module name");
        }

        if(!$this->existsModule($moduleName)) {
            return $this->error("Module '%s' is not exists", $moduleName);
        }

        if(empty($path)) {
            return $this->error("Missing source path");
        }

        if(!fs\exists($path)) {
            return $this->error("Path '%s' is not exists", $path);
        }

        $subpath = fs\path\subpath($this->_projectPath, \fs\path\absolute($path));
        $mappingOption = $this->getOptionArray('--map');

        if(!$subpath && empty($mappingOption)) {
            return $this->error("The file you specifiy is not in the project path, you need to use '--map' option to specity the source and target path.");
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
                return $this->error("This path '%s' is already in mapping list of module '%s'", $target, $moduleName);
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
                $this->success("%s => %s", $source, $target);
            }
        } catch(Exception $e) {
            return $this->error($e->getMessage());
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
                            if(fs\path\join($this->_modulePath, $module, $source) == fs\path\standard($linkreal)) {
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

        $this->outputLine("{$this->crLWhite()}%s {$this->crGray()}[{$this->crLGreen()}%d{$this->crGray()}+{$this->crLRed()}%d{$this->crGray()}={$this->crLWhite()}%d{$this->crGray()}]{$this->crNull()}",
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
            return $this->error("Missing a module name");
        }

        if(!$this->existsModule($module)) {
            return $this->error("Module '%s' is not exists", $module);
        }

        if(!$this->isModuleAvailable($module)) {
            return $this->error("This module '%s' is not available", $module);
        }

        $index = $args[1];

        if(empty($index)) {
            return $this->error("Missing a index value");
        }

        $index = intval($index);

        if($index <= 0) {
            return $this->error('Index value must be a number that greate than 0');
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
    }

    protected function _command_git($args)
    {
        $moduleWildcard = $args[0];
        unset($args[0]);
        $args = array_values($args);

        if(empty($moduleWildcard)) {
            return $this->error("Missing a module name");
        }

        $modules = $this->_getAllModules($moduleWildcard);
        $ocwd = getcwd();

        if(!$this->multiModulesConfirm(count($modules), "executed git command")) {
            return false;
        }

        foreach ($modules as $module) {
            $cmd = sprintf('git %s', implode(' ', $args));
            $this->outputLine("{$this->crLWhite()}Module: {$this->crSkyblue()}%s{$this->crNull()} > {$this->crGray()}%s{$this->crNull()}\n", $module, $cmd);
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
            return $this->error("Missing a repository uri.");
        }

        $module = preg_replace("#\.git$#i", '', \fs\path\basename($repo));

        if($this->existsOption('f')) {
            $modulePath = fs\path\join($this->_modulePath, $module);

            if(fs\exists($modulePath)) {
                fs\rm($modulePath, true);
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
    }

    protected function _command_clean($args)
    {
        $path = $args[0];

        if(empty($path)) {
            $path = getcwd();
        }

        $self = $this;

        $cleanFunc = function ($path) use (&$cleanFunc, $self) {
            $dir = dir($path);
            $ocwd = getcwd();

            while($file = $dir->read()) {
                if($file == "." or $file == ".." or $file == MODMGR_DIR_NAME) {
                    continue;
                }

                $filePath = \fs\path\standard("$path/$file");

                if (\fs\islink($filePath)) {
                    try {
                        $linkInfoValue = \linkinfo($filePath);
                        chdir(dirname($filePath));

                        if ($linkInfoValue == 0 or $linkInfoValue == -1) {
                            \fs\rm($filePath);
                            $self->success("Removed invalid link: '%s'", $filePath);
                        }

                        chdir($ocwd);
                    } catch (\Exception $e) {
                        chdir($ocwd);
                        $self->_processException($e);
                    }
                } else if (is_dir($filePath)) {
                    $cleanFunc($filePath);

                    if($this->existsOption('d')) {
                        if(fs\isempty($filePath)) {
                            try {
                                rmdir($filePath);
                                $self->success("Removed empty directory: '%s'", $filePath);
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
    }
}

/**
 * Class BaseOptionSupport
 */
abstract class BaseOptionSupport
{
    protected $_globalOptionsSupports = ['--nocolor', '--nooutput', '--persistent-mode'];
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

    public function output($str)
    {
        if($this->existsOption('--nooutput')) {
            return null;
        }

        $args = func_get_args();
        $content = $args[0];

        if($this->existsOption('--nocolor')) {
            $content = preg_replace("#\033\[([01];)?([0-9]{1,2};)?[0-9]{1,2}m#", '', $content);
            $args[0] = $content;
        }
        echo call_user_func_array('sprintf', $args);
        return null;
    }

    public function outputLine($str='')
    {
        call_user_func_array([$this, 'output'], func_get_args());
        echo io\endline();
        return null;
    }

    public function error()
    {
        $args = func_get_args();
        $args[0] = "{$this->crWhite()}[{$this->crLRed()}error{$this->crWhite()}]{$this->crNull()} " . $args[0];
        call_user_func_array([$this, 'outputLine'], $args);
        return false;
    }

    public function warning()
    {
        $args = func_get_args();
        $args[0] = "{$this->crWhite()}[{$this->crLYellow()}warning{$this->crWhite()}]{$this->crNull()} " . $args[0];
        call_user_func_array([$this, 'outputLine'], $args);
        return false;
    }

    public function info()
    {
        $args = func_get_args();
        $args[0] = "{$this->crGray()}{$args[0]}{$this->crNull()}";
        call_user_func_array([$this, 'outputLine'], $args);
        return false;
    }

    public function success()
    {
        $args = func_get_args();
        $args[0] = "{$this->crWhite()}[{$this->crGreen()}ok{$this->crWhite()}]{$this->crNull()} {$args[0]}";
        call_user_func_array([$this, 'outputLine'], $args);
        return null;
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

        if(!$this->_checkArguments()) {
            return false;
        }

        if(!$this->_checkInit()) {
            return false;
        }

        $this->_dispatch();
    }

    protected function _init($argv)
    {
        $this->_initErrorHandle();
        $this->_scriptPath = fs\path\standard($argv[0]);

        unset($argv[0]);

        $this->_command = $argv[1];
        $this->_targetCommand = $this->_getTargetCommand();

        if(empty($this->_command)) {
            $this->_command = $this->_targetCommand;
        }

        $this->_commandEscape = str_replace('-', '_', $this->_targetCommand);
        unset($argv[1]);

        $this->_findModulePath();

        return $argv;
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
                $this->error("The current directory has not been initialized yet.");
                $this->outputLine("Directory '%s'", getcwd());
                $this->info("You can use 'init' command to initialize");
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
        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            return false;
        }

        return true;
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
            return $this->error("Module '%s' is not exists.", $module);
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
                            $this->warning("There is an exist file or folder '%s'", $targetFullPath);
                            $this->info("You can use '-f' option to forece remove exists file or folder.");
                        } else {
                            fs\rm($targetFullPath, true);
                            $this->success("Removed '%s'", $targetFullPath);
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
            $this->error($e->getMessage() . $subfixMsg);
        } else if($e->getCode() == 1){
            $this->warning($e->getMessage() . $subfixMsg);
        }
    }

    protected function _deployModule($module)
    {
        if(!fs\isdir(fs\path\join($this->_modulePath, $module))) {
            return $this->error("Module '%s' is not exists.", $module);
        }

        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            return $this->error("Module '%s' is not available", $module);
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
                            $this->error("Can't copy file or directory to '%s', the path is already exists", $targetFullPath);
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
                                $this->error("Can't remove link '%s'. %s", $targetFullPath, $result);
                                continue;
                            }
                        } else {
                            $this->error("Can't create link '%s', the path is already exists", $targetFullPath);
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
                        $this->error("Link to a not exists file or folder: '%s'", $linkval);
                        continue;
                    }

                    $result = fs\symlink($targetFullPath, $linkval);

                    if($result===true) {
                        $this->success("%s => %s", $linkval, $relativeTargetPath);
                    } else {
                        $this->error($result);
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
                        return $this->error("Duplicate option: '%s'", $key);
                    }

                    $this->_options[$key] = true;
                }
            } else {
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
            return $this->error("Command '%s' not found", $this->_command);
        }

        $supportOptions = $this->_getCommandSupportOptions();

        foreach ($this->_options as $key => $option) {
            if(!in_array($key, $supportOptions) && !in_array($key, $this->_globalOptionsSupports)) {
                return $this->error("Option '%s' is not supported in command '%s'", $key, $this->_command);
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
        if(!$command) {
            $command = strval($this->_command);
        }

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