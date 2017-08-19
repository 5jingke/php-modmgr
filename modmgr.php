<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-8-4
 * Time: 下午 05:26
 */
require_once 'lib.php';

define('MODMGR_VERSION', '0.1.0');

class App
{
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
    ];

    protected $_currentCommand;
    protected $_options;
    protected $_commandArguments = [];
    protected $_scriptPath;

    protected $_isFirstOutput = true;

    public function __construct()
    {
        $this->_parseAppArguments();
        $this->_checkArguments();
    }

    protected function _parseAppArguments()
    {
        $argv = $_SERVER['argv'];
        $this->_scriptPath = $argv[0];
        unset($argv[0]);
        $this->_currentCommand = $argv[1];
        unset($argv[1]);
        $argv = array_values($argv);
        $argc = count($argv);

        for($i=0; $i<$argc; $i++) {
            $arg = $argv[$i];

            if(substr($arg, 0, 2) == '--') {
                $optionDetail = explode('=', $arg);
                $this->_options[$optionDetail[0]] = $optionDetail[1];
            } else if($arg[0] == '-') {
                $this->_options[$arg[0]] = $argv[$i+1];
                $i++;
            } else {
                $this->_commandArguments []= $arg[$i];
            }
        }
    }

    protected function _checkArguments()
    {
        if(!isset($this->_commondList[$this->_currentCommand])) {
            $this->output('Command not found: %s', $this->_currentCommand);
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
}

new App();