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
        /* @see _command_help */
        'help' => [],
        '' => 'help',

        /* @see _command_list */
        'list' => ['a', 's'],

        /* @see _command_deploy */
        'deploy' => ['f', 'a', 'c'],
        'deploy-all' => [],
        'd' => 'deploy',

        'undeploy' => [],
        'ud' => 'undeploy',

        'clean' => [],

        /* @see _command_show */
        'show' => ['l', 'v'],

        /* @see _command_version */
        'version' => ['s'],
        'v' => 'version',
        'ver' => 'v',

        /* @see _command_initialize */
        'initialize' => [],
        'init' => 'initialize',

        /* @see _command_create */
        'create' => [],

        /* @see _command_addmap */
        'addmap' => ['--map', 'f'],
        /* @see _command_showmap */
        'showmap' => ['s', 'a'],
        /* @see _command_delmap */
        'delmap' => [],
    ];

    /**
     * Display help document
     */
    protected function _command_help()
    {
        echo 'helpppper';
    }

    protected function _command_version()
    {
        if ($this->existsOption('s')) {
            echo MODMGR_VERSION;
        } else {
            echo 'Module Manager Version: ' . MODMGR_VERSION;
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
            if ($this->existsOption('l')) {
                if ($this->existsOption('v')) {
                    $this->outputAndExit(str\stringformat($data, true));
                } else {
                    $this->outputAndExit(str\stringformat(array_keys($data)));
                }
            }

            $this->errorDie("Missing the value key.\nUse 'help' command to get help.");
        } else {
            $this->outputAndExit($data[$args[0]]);
        }
    }

    protected function _command_initialize()
    {
        if (is_dir(MODMGR_DIR_NAME)) {
            $this->outputAndExit("This directory does not need to be initialized again");
        }

        try {
            fs\mkdir(MODMGR_DIR_NAME);
            $this->outputAndExit("Initialized successfuly");
        } catch (Exception $e) {
            $this->errorDie($e->getMessage());
        }
    }

    protected function _command_deploy($args)
    {
        if (empty($args)) {
            $this->errorDie("Missing a module name or wildcard.");
        }

        $wildcard = $args[0];
        $modules = $this->_getAvailableModules($wildcard);

        if (empty($modules)) {
            $this->errorDie("Parameter '%s' does not match any module.\nYou can use 'list' command to get all available modules", $wildcard);
        }

        foreach ($modules as $module) {
            $this->output("Deploying module '%s'", $module);
            $this->_deployModule($module);
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
            $this->output(str\stringformat($modules));
        }

        if (!$this->existsOption('s')) {
            $this->output("Total of %d module(s)", count($modules));
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
            $this->errorDie($e->getMessage());
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

    protected function _command_showmap($args)
    {
        $module = $args[0];
        $this->_moduleCheckAlert($module);

        if($this->getOption('s')) {
            $this->outputAndExit(
                implode("\n", array_filter(array_map("trim",
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

            foreach($mappings as $source => $target) {
                $i ++;

                if($str != '') {
                    $str .= "\n";
                }

                $str .= sprintf("%03d: %{$max}s => %s", $i, fs\path\join($sourcePrefix,$source), fs\path\join($targetPrefix,$target));
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
}


/**
 * 基础
 * Class CommandBase
 */
class CommandBase
{
    protected $_commondList = [];
    protected $_noNeedToInit = ['help', 'version', 'initialize'];

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

    protected function _checkInit()
    {
        if(empty($this->_modulePath)) {
            if(!in_array($this->_targetCommand, $this->_noNeedToInit)) {
                $this->errorDie("The current directory has not been initialized yet.".
                "\nYou can use 'init' command to initialize".
                "\nDirectory '%s'", getcwd());
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
                            $this->output("Deployed: %s", $moduleFullPath);
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

                    if(fs\islink($targetFullPath) && fs\readlink($targetFullPath) == $linkval) {
                        continue;
                    }

                    if(fs\exists($targetFullPath)) {
                        if($this->existsOption('f')) {
                            fs\rm($targetFullPath,true);
                        } else {
                            $this->error("Can't create link '%s', the path is already exists", $targetFullPath);
                            continue;
                        }
                    }
                    
                    $targetParent = dirname($targetFullPath);
                    
                    if(!fs\isdir($targetParent)) {
                        fs\mkdir($targetParent, true);
                    }

                    // $oldcwd = getcwd();
                    // chdir(dirname($targetFullPath));
                    $result = fs\symlink($targetFullPath, $linkval);
                    
                    if($result===true) {
                        $this->output("Deployed: %s => %s", $targetFullPath, $linkval);
                    } else {
                        $this->error($result);
                    }
                    // chdir($oldcwd);
                    
                }
            } catch (Exception $e) {
                $this->error($e->getMessage() . " '$targetFullPath'");
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

        $mappingTems = explode("\n", $content);
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
            $arg = $argv[$i];

            if($arg == '--') {
                for($j=$i+1; $j<$argc; $j++) {
                    $this->_commandArguments []= $arg[$j];
                }

                return;
            }

            if(substr($arg, 0, 2) == '--') {

                if($this->existsOption($arg)) {
                    $this->errorDie("Duplicate option: '%s'", $arg);
                }

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
            if(!in_array($key, $supportOptions)) {
                $this->errorDie("Does not supported option '%s' in command '%s'", $key, $this->_command);
            }
        }
    }

    public function outputAndExit()
    {
        call_user_func_array([$this, 'output'], func_get_args());
        exit;
    }

    public function output($str)
    {
        $args = func_get_args();

        if(!$this->_isFirstOutput) {
            io\screenoutput("\n");
        } else {
            $this->_isFirstOutput = false;
        }

        io\screenoutput(call_user_func_array('sprintf', $args));
    }

    public function errorDie($msg)
    {
        call_user_func_array([$this, 'error'], func_get_args());
        die;
    }

    public function error()
    {
        $args = func_get_args();
        $args[0] = '[error] ' . $args[0];
        call_user_func_array([$this, 'output'], $args);
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
}

set_error_handler(function($errno, $errmsg) {
    if($errno & ERROR_REPORTING) {
        throw new Exception($errmsg);
    }
});


//
// Start application
//
new App();