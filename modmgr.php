<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-8-4
 * Time: 下午 05:26
 */
define('ERROR_REPORTING', E_ALL ^ E_NOTICE ^ E_STRICT);
error_reporting(ERROR_REPORTING);
require_once 'lib.php';

define('MODMGR_VERSION', '0.1.0');
define('MODMGR_DIR_NAME', '.modman');
define('MODMGR_MAPPING_NAME', 'modman');
// var_dump(linkinfo('G:/wujingke/projects/php-modmgr/app/1.txt'));
// var_dump(linkinfo('G:\wujingke\projects\php-modmgr\app\1.txt'));
//
// die;
class App extends CommandBase
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
        'deploy-all' => [],
        'd' => 'deploy',

        /**
         * @see _command_undeploy
         * f: 强制卸载模块，删除链接或文件
         * y: 忽略多模块时的确认信息
         */
        'undeploy' => ['f', 'y'],
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
         * @see _command_addmap
         * 添加模块映射
         * --map: 指定映射
         * f: 覆盖已存在的map
         */
        'mapadd' => ['--map', 'f'],
        /**
         * @see _command_showmap
         * 显示模块的映射
         * s: 简单模式
         * a: 显示绝对路径
         * d: 显示部署状态: (D)已部署， (UND)未部署
         */
        'showmap' => ['s', 'a', 'd'],
        'map' => 'showmap',

        /**
         * @see _command_delmap
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

    /**
     * Display help document
     */
    protected function _command_help()
    {
        echo 'helpppper';
    }

    protected function _command_cwd()
    {
        $this->output(getcwd());
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
                    if(floatval(php_uname('r')) >= 8) {
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

        while(true) {
            $command = trim($this->input("{$this->crWhite()}modmgr %s> {$this->crNull()}", getcwd()));

            if('persistent' == strtolower($command) or 'pss' == strtolower($command)) {
                $command = '';
            }

            if(empty($command)) {
                $this->_isFirstOutput = true;
                continue;
            }

            if(strtolower($command) == "exit") {
                $this->_isFirstOutput = true;
                $this->output("{$this->crLGreen()}Bye!{$this->crNull()}");
                break;
            }

            $cmd = sprintf('php "%s" %s %s', $this->_scriptPath, $command, $this->_packGlobalOptionsToStr());
            system($cmd);
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
                    $this->outputDie(str\stringformat($data, true));
                } else {
                    $this->outputDie(str\stringformat(array_keys($data)));
                }
            }

            $this->error("Missing the value key.");
            $this->infoDie("Use 'help' command to get help.");
        } else {
            $this->outputDie($data[$args[0]]);
        }
    }

    protected function _command_initialize()
    {
        if (is_dir(MODMGR_DIR_NAME)) {
            $this->outputDie("This directory does not need to be initialized again");
        }

        try {
            fs\mkdir(MODMGR_DIR_NAME);
            $this->successDie("Initialized successfuly");
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    protected function _command_deploy($args)
    {
        if (empty($args)) {
            $this->errorDie("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        $this->multiModulesConfirm(count($modules), 'deployed');

        if (empty($modules)) {
            $this->error("Parameter '%s' does not match any module.", $wildcard);
            $this->infoDie("You can use 'list' command to get all available modules.");
        }

        foreach ($modules as $module) {
            $this->output("Deploying module '{$this->crLWhite()}%s{$this->crNull()}'", $module);
            $this->_deployModule($module);
        }
    }

    protected function _command_undeploy($args)
    {
        if (empty($args)) {
            $this->errorDie("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        $this->multiModulesConfirm(count($modules), 'undeployed');

        if (empty($modules)) {
            $this->error("Parameter '%s' does not match any module.", $wildcard);
            $this->infoDie("You can use 'list' command to get all available modules.");
        }

        foreach ($modules as $module) {
            $this->output("Undeploying module '{$this->crLWhite()}%s{$this->crNull()}'", $module);
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
            $this->output("{$this->crLWhite()}%s{$this->crNull()}", str\stringformat($modules));
        }

        if (!$this->existsOption('s')) {
            $this->info("Total of %d module(s)", count($modules));
        }
    }

    protected function _command_create($args)
    {
        $moduleName = $args[0];

        if(empty($moduleName)) {
            $this->errorDie('Missing a module name');
        }

        if($this->existsModule($moduleName)) {
            $this->errorDie("The module '%s' already exists", $moduleName);
        }

        try {
            fs\mkdir(fs\path\join($this->_modulePath, $moduleName));
            io\writefile(fs\path\join($this->_modulePath, $moduleName, MODMGR_MAPPING_NAME), '');
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    protected function _command_addmap($args)
    {
        $moduleName = $args[0];
        $path = $args[1];

        if(empty($moduleName)) {
            $this->errorDie("Missing a module name");
        }

        if(!$this->existsModule($moduleName)) {
            $this->errorDie("Module '%s' is not exists", $moduleName);
        }

        if(empty($path)) {
            $this->errorDie("Missing mapping path");
        }

        if(!fs\exists($path)) {
            $this->errorDie("Path '%s' is not exists", $path);
        }

        $subpath = fs\path\subpath($this->_projectPath, realpath($path));
        $mappingOption = $this->getOptionArray('--map');
        $source = $mappingOption[0];
        $target = $mappingOption[1];

        if(empty($target)) {
            $target = $source;
        }

        if(!$target) {
            if(!$subpath) {
                $this->errorDie("The file you specifiy is not in the project path, you need to use '--map' option to specity the source and target path.");
            }

            $target = $subpath;
        }

        if(empty($source)) {
            $source = $target;
        }

        $source = \fs\path\standard($source);
        $target = \fs\path\standard($target);
        $moduleFilePath = fs\path\join($this->_modulePath, $moduleName, $source);
        $moduleFileDir = fs\path\parent($moduleFilePath);
        $mappings = (array)$this->_getModuleMapping($moduleName);

        foreach ($mappings as $mapping) {
            if(\fs\path\standard($mapping) == $target && !$this->existsOption('f')) {
                $this->errorDie("This path '%s' is already in mapping list of module '%s'", $target, $moduleName);
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
            }
        } catch(Exception $e) {
            $this->errorDie($e->getMessage());
        }
    }

    protected function _command_showmap($args, $single=false)
    {
        $module = $args[0];
        $this->_moduleCheckAlert($module);

        if($this->existsOption('s')) {
            $this->outputDie(
                implode(io\endline(), array_filter(array_map("trim",
                    explode("\n", io\readfile(fs\path\join($this->_modulePath, $module, MODMGR_MAPPING_NAME))))))
            );
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

        if(!empty($mappings)) {
            $str = '';
            $i = 0;
            $max = $maxPrefix + $this->_getMappingMaxSourceLength($mappings);
            $ocwd = getcwd();

            foreach($mappings as $source => $target) {
                $i ++;

                if($str != '') {
                    $str .= io\endline();
                }

                $status = "    ";
                try {
                    $targetFullPath = fs\path\join($this->_projectPath, $target);

                    if(fs\islink($targetFullPath)) {
                        $linkinfo = linkinfo($targetFullPath);

                        chdir(dirname($targetFullPath));
                        if($linkinfo > 0) {
                            $linkval = readlink($targetFullPath);
                            $linkreal = realpath($linkval);

                            if(fs\path\join($this->_modulePath, $module, $source) == fs\path\standard($linkreal)) {
                                $status = "{$this->crLSkyblue()}(D){$this->crNull()} ";;
                            }
                        }

                        chdir($ocwd);
                    }
                } catch(Exception $e) {
                    $this->_processException($e);
                }

                $str .= sprintf("{$this->crGray()}%03d: {$this->crNull()}$status{$this->crSkyblue()}%{$max}s{$this->crNull()} => {$this->crSkyblue()}%s{$this->crNull()}",
                    $i, fs\path\join($sourcePrefix,$source), fs\path\join($targetPrefix,$target));
            }

            $this->output($str);
        }
    }

    protected function _command_delmap($args) {
        $module = $args[0];
        $this->_moduleCheckAlert($module);

        $index = $args[1];

        if(empty($index)) {
            $this->errorDie("Missing a index value");
        }

        $index = intval($index);

        if($index <= 0) {
            $this->errorDie('Index value must be a number that greate than 0');
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
            $this->errorDie("Missing a module name");
        }

        $modules = $this->_getAllModules($moduleWildcard);
        $this->multiModulesConfirm(count($modules), "executed git command");
        $ocwd = getcwd();

        foreach ($modules as $module) {
            $cmd = sprintf('git %s', implode(' ', $args));
            $this->output("{$this->crLWhite()}Module: {$this->crSkyblue()}%s{$this->crNull()} > {$this->crGray()}%s{$this->crNull()}\n", $module, $cmd);
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
            $this->errorDie("Missing a repository uri.");
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

    protected function _command_clean()
    {
        $current = getcwd();
        $self = $this;

        $cleanFunc = function ($path) use (&$cleanFunc, $self) {
            $dir = dir($path);
            $ocwd = getcwd();

            while($file = $dir->read()) {
                if($file != "." and $file != "..") {
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

        $cleanFunc($current);
    }
}


/**
 * 基础
 * Class CommandBase
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
class CommandBase
{
    protected $_commondList = [];
    protected $_globalOptionsSupports = ['--nocolor', '--nooutput'];
    protected $_noNeedToInit = ['help', 'version', 'initialize', 'persistent', 'cwd',
        'elevate-privileges', 'clean', 'list', 'show'];

    protected $_command;
    protected $_targetCommand;
    protected $_commandEscape;
    protected $_commandOptions=[];
    protected $_commandArguments = [];

    protected $_scriptPath;
    protected $_modulePath;
    protected $_projectPath;

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

    public function __construct()
    {
        $argv = $this->_init();
        $this->_parseAppArguments($argv);
        $this->_checkArguments();
        $this->_checkInit();
        $this->_dispatch();
    }

    protected function _init()
    {
        $this->_initErrorHandle();

        $argv = $_SERVER['argv'];
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

    protected function _moduleCheckAlert($module) {
        if(empty($module)) {
            $this->errorDie("Missing a module name");
        }

        if(!$this->existsModule($module)) {
            $this->errorDie("Module '%s' is not exists", $module);
        }

        if(!$this->isModuleAvailable($module)) {
            $this->errorDie("This module '%s' is not available", $module);
        }
    }

    protected function _packGlobalOptionsToStr()
    {
        $array = [];
        foreach($this->_globalOptionsSupports as $key) {
            if(isset($this->_commandOptions[$key])) {
                $array []= $key;

                $val = $this->_commandOptions[$key];

                if(!is_bool($val) && !empty($val)) {
                    $array []= "$val";
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
                $this->output("Directory '%s'", getcwd());
                $this->infoDie("You can use 'init' command to initialize");
            }
        }
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

    public function setOption($key, $value=[true])
    {
        $this->_commandOptions[$key] = $value;
        return $this;
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
            $this->errorDie("Module '%s' is not exists.", $module);
        }

        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            die;
        }

        foreach ($mappings as $modulePath => $targetPath) {
            $targetFullPath = fs\path\join($this->_projectPath, $modulePath);

            try {
                if ($this->existsOption('c')) {
                    if (fs\exists($targetFullPath)) {
                        fs\rm($targetFullPath, true);
                        $this->output("Remove: '%s'", $targetFullPath);
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
                    }
                }
            } catch (Exception $e) {
                $this->_processException($e, "{$this->crGray()} During undeployed '$targetFullPath'{$this->crNull()}");
            }
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
            $this->errorDie("Module '%s' is not exists.", $module);
        }

        $mappings = $this->_getModuleMapping($module);

        if(empty($mappings)) {
            $this->errorDie("Module '%s' is not available", $module);
        }

        foreach ($mappings as $modulePath => $targetPath) {
            $moduleFullPath = fs\path\join($this->_modulePath, $module, $modulePath);
            $targetFullPath = fs\path\join($this->_projectPath, $modulePath);

            try {
                if($this->existsOption('c')) {
                    if(fs\exists($targetFullPath)) {
                        if($this->existsOption('f')) {
                            fs\rm($targetFullPath);
                            fs\copy($moduleFullPath, $targetFullPath);
                            $this->output("Copy '%s' to '%s'", $moduleFullPath, $targetFullPath);
                        } else {
                            $this->error("Can't copy file or directory to '%s', the path is already exists", $targetFullPath);
                        }
                    } else {
                        fs\copy($moduleFullPath, $targetFullPath);
                        $this->output("Deployed: %s", $moduleFullPath);
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
                        $this->success("%s => %s", $relativeTargetPath, $linkval);
                    } else {
                        $this->error($result);
                    }

                    chdir($oldcwd);
                }
            } catch (Exception $e) {
                $this->_processException($e, "{$this->crGray()} During deployed '$targetFullPath'{$this->crNull()}");
            }
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

                return;
            }

            if(substr($arg, 0, 2) == '--') {
                $this->_commandOptions[$arg] = [];
                $valueEscapeKey = str_replace('-', '_', ltrim($arg, '-'));
                $argsHandle = &$this->_commandOptions[$arg];
            } else if($arg[0] == '-') {
                foreach (str_split(substr($arg, 1), 1) as $key) {
                    if($this->existsOption($key)) {
                        $this->errorDie("Duplicate option: '%s'", $key);
                    }

                    $this->_commandOptions[$key] = true;
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
    }

    protected function _checkArguments()
    {
        if($this->_isCommandNotFound()) {
            $this->errorDie("Command '%s' not found", $this->_command);
        }

        $supportOptions = $this->_getCommandSupportOptions();

        foreach ($this->_commandOptions as $key => $option) {
            if(!in_array($key, $supportOptions) && !in_array($key, $this->_globalOptionsSupports)) {
                $this->errorDie("Option '%s' is not supported in command '%s'", $key, $this->_command);
            }
        }
    }

    public function outputDie()
    {
        call_user_func_array([$this, 'output'], func_get_args());
        die;
    }

    public function input($msg)
    {
        if(func_num_args() > 0) {
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

    public function multiModulesConfirm($count, $opmsg)
    {
        if($count > 1 && !$this->existsOption('y')) {
            if(!$this->inputYN("There are $count modules will be $opmsg, are you sure? (y/n):")) {
                die;
            }
        }
    }

    public function output($str)
    {
        if($this->existsOption('--nooutput')) {
            return;
        }

        $args = func_get_args();
        $content = $args[0];

        if($this->existsOption('--nocolor')) {
            $content = preg_replace("#\033\[([01];)?([0-9]{1,2};)?[0-9]{1,2}m#", '', $content);
            $args[0] = $content;
        }

        if(!$this->_isFirstOutput) {
            echo io\endline();
        } else {
            $this->_isFirstOutput = false;
        }

        echo call_user_func_array('sprintf', $args);
    }

    public function errorDie($msg)
    {
        call_user_func_array([$this, 'error'], func_get_args());
        die;
    }

    public function error()
    {
        $args = func_get_args();
        $args[0] = "[{$this->crLRed()}error{$this->crNull()}] " . $args[0];
        call_user_func_array([$this, 'output'], $args);
    }

    public function warning()
    {
        $args = func_get_args();
        $args[0] = "[{$this->crLYellow()}warning{$this->crNull()}] " . $args[0];
        call_user_func_array([$this, 'output'], $args);
    }

    public function info()
    {
        $args = func_get_args();
        $args[0] = "{$this->crGray()}{$args[0]}{$this->crNull()}";
        call_user_func_array([$this, 'output'], $args);
    }

    public function infoDie()
    {
        call_user_func_array([$this, 'info'], func_get_args());
        die;
    }

    public function success()
    {
        $args = func_get_args();
        $args[0] = "[{$this->crGreen()}ok{$this->crNull()}] {$args[0]}";
        call_user_func_array([$this, 'output'], $args);
    }

    public function successDie()
    {
        call_user_func_array([$this, 'success'], func_get_args());
        die;
    }

    /**
     * --long-option 用法 $this->existsOption('--long-option')
     * -xad 短参数用法 $this->existsOption('a')
     * @param $optionKey
     * @return bool
     */
    public function existsOption($optionKey)
    {
        return isset($this->_commandOptions[$optionKey]);
    }

    public function getOption($optionKey, $index=0)
    {
        return $this->_commandOptions[$optionKey][$index];
    }

    public function getOptionArray($optionKey) {
        return $this->_commandOptions[$optionKey];
    }

    protected function _getTargetCommand()
    {
        $command = strval($this->_command);

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
        $this->{$this->_getDispatchMethodName()}($this->_commandArguments, $this->_commandOptions);
    }

    protected function _getDispatchMethodName()
    {
        return "_command_{$this->_commandEscape}";
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

//
// set_error_handler(function($errno, $errmsg) {
//     if($errno & E_ERROR) {
//         throw new Exception($errmsg);
//     }
// });


//
// Start application
//
new App();