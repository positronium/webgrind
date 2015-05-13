<?php

/**
 * Configuration for webgrind
 * @author Jacob Oettinger
 * @author Joakim Nygård
 */
class Webgrind_Config extends Webgrind_MasterConfig {
	/**
	* Automatically check if a newer version of webgrind is available for download
	*/
	static $checkVersion = true;
	static $hideWebgrindProfiles = true;

	/**
	* Writable dir for information storage.
	* If empty, will use system tmp folder or xdebug tmp
	*/
	static $storageDir = '';
	static $profilerDir = '/tmp';

	/**
	* Suffix for preprocessed files
	*/
	static $preprocessedSuffix = '.webgrind';

	static $defaultTimezone = 'Europe/Copenhagen';
	static $dateFormat = 'Y-m-d H:i:s';
	static $defaultCostformat = 'percent'; // 'percent', 'usec' or 'msec'
	static $defaultFunctionPercentage = 90;
	static $defaultHideInternalFunctions = false;

	/**
	* Path to python executable
	*/
	static $pythonExecutable = '/usr/bin/python';

	/**
	* Path to graphviz dot executable
	*/
	static $dotExecutable = '/usr/local/bin/dot';


	/**
	* Path to python executable on Windows
	*/
	static $pythonExecutableWin = 'C:\\Python27\\python.exe';

	/**
	* Path to graphviz dot executable on Windows
	*/
	static $dotExecutableWin = 'C:\\Program Files (x86)\\Graphviz2.38\\bin\\dot.exe';

	/**
	* sprintf compatible format for generating links to source files.
	* %1$s will be replaced by the full path name of the file
	* %2$d will be replaced by the linenumber
	*/
	static $fileUrlFormat = 'index.php?op=fileviewer&file=%1$s#line%2$d'; // Built in fileviewer
	//static $fileUrlFormat = 'txmt://open/?url=file://%1$s&line=%2$d'; // Textmate
	//static $fileUrlFormat = 'file://%1$s'; // ?

	/**
	* format of the trace drop down list
	* default is: invokeurl (tracefile_name) [tracefile_size]
	* the following options will be replaced:
	*   %i - invoked url
	*   %f - trace file name
	*   %s - size of trace file
	*   %m - modified time of file name (in dateFormat specified above)
	*/
	static $traceFileListFormat = '%i (%f) [%s]';


	#########################
	# BELOW NOT FOR EDITING #
	#########################

	/**
	* Regex that matches the trace files generated by xdebug
	*/
	static function xdebugOutputFormat() {
		$outputName = ini_get('xdebug.profiler_output_name');
		if($outputName=='') // Ini value not defined
			$outputName = '/^cachegrind\.out\..+$/';
		else
			$outputName = '/^'.preg_replace('/(%[^%])+/', '.+', $outputName).'$/';
		return $outputName;
	}

	/**
	* Directory to search for trace files
	*/
	static function xdebugOutputDir() {
		$dir = ini_get('xdebug.profiler_output_dir');
		if($dir=='') // Ini value not defined
			return realpath(Webgrind_Config::$profilerDir).'/';
		return realpath($dir).'/';
	}

	/**
	* Writable dir for information storage
	*/
	static function storageDir() {
		if (!empty(Webgrind_Config::$storageDir))
			return realpath(Webgrind_Config::$storageDir).'/';

		if (!function_exists('sys_get_temp_dir') || !is_writable(sys_get_temp_dir())) {
			# use xdebug setting
			return Webgrind_Config::xdebugOutputDir();
		}
		return realpath(sys_get_temp_dir()).'/';
	}

	static function init() {
		if (stripos(PHP_OS, 'winnt') !== false) {
			if (!is_readable(self::$pythonExecutable))
				self::$pythonExecutable = self::$pythonExecutableWin;
			if (!is_readable(self::$dotExecutable))
				self::$dotExecutable    = self::$dotExecutableWin;
		}
	}
}

Webgrind_Config::init();
