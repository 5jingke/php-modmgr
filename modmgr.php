<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-8-4
 * Time: 下午 05:26
 */

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
require_once 'lib.php';

define('MODMGR_VERSION', '0.1.0');

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
        'help' => [],
        '' => 'help',

        'list' => [],

        'deploy' => [],
        'deploy-all' => [],
        'd' => 'deploy',

        'undeploy' => [],
        'ud' => 'undeploy',

        'clean' => [],
        'show' => [],
        
        'version' => ['s'],
        'v' => 'version',
        'ver' => 'v'
    ];

    /**
     * Display help document
     */
    protected function _command_help()
    {
        echo 'helpppper';
    }

    /**
     * Display the version of Module Manager
     */
    protected function _command_version()
    {
        if($this->existsOption('s')) {
            echo MODMGR_VERSION;
        } else {
            echo 'Module Manager Version: ' . MODMGR_VERSION;
        }
    }
}


/**
 * 基础
 * Class CommandBase
 */
class CommandBase
{
    protected $_commondList = [];

    protected $_command;
    protected $_targetCommand;
    protected $_commandEscape;
    protected $_commandOptions=[];
    protected $_commandArguments = [];
    protected $_scriptPath;

    protected $_isFirstOutput = true;

    public function __construct()
    {
        $this->_parseAppArguments();
        $this->_checkArguments();
        $this->_dispatch();
    }

    protected function _parseAppArguments()
    {
        $argv = $_SERVER['argv'];
        $this->_scriptPath = $argv[0];
        unset($argv[0]);
        $this->_command = $argv[1];
        $this->_targetCommand = $this->_getTargetCommand();
        
        if(empty($this->_command)) {
            $this->_command = $this->_targetCommand;
        }
         
        $this->_commandEscape = str_replace('-', '_', $this->_targetCommand);
        unset($argv[1]);
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
                    $this->error("Duplicate option: '%s'", $arg);
                }
                
                $this->_commandOptions[$arg] = [];
                $valueEscapeKey = str_replace('-', '_', ltrim($arg, '-'));
                $argsHandle = &$this->_commandOptions[$arg];
            } else if($arg[0] == '-') {
                foreach (str_split(substr($arg, 1), 1) as $key) {
                    if($this->existsOption($key)) {
                        $this->error("Duplicate option: '%s'", $key);
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
        if($this->_commandNotFound()) {
            $this->error("Command '%s' not found", $this->_command);
        }
        
        $supportOptions = $this->_getCommandSupportOptions();
        
        foreach ($this->_commandOptions as $key => $option) {
            if(!in_array($key, $supportOptions)) {
                $this->error("Does not supported option '%s' in command '%s'", $key, $this->_command);
            }
        }
    }

    public function output($str)
    {
        $args = func_get_args();

        if(!$this->_isFirstOutput) {
            echo "\n";
        } else {
            $this->_isFirstOutput = false;
        }

        echo call_user_func_array('sprintf', $args);;
    }
    
    public function error($msg)
    {
        $args = func_get_args();
        $args[0] = '[Error] ' . $args[0];
        call_user_func_array([$this, 'output'], $args);
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
    
    public function getOption($optionKey)
    {
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
    
    protected function _commandNotFound() {
        if(!isset($this->_commondList[$this->_targemand])) {
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




//
// Start application
//
new App();