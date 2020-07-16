<?php

namespace Mypos\IPC;

/**
 * Library classes loader
 */
class Loader{

	/**
	 * Find and include required class file
	 * @param string $class_name
	 * @return boolean
	 */
	static public function loader($class_name){

		if(preg_match('/^' . str_replace('\\', '\\\\', __NAMESPACE__) . '\\\/', $class_name)){
			$filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace(array(__NAMESPACE__ . '\\', '\\'), array('', DIRECTORY_SEPARATOR), $class_name) . '.php';
			if(is_file($filePath) && is_readable($filePath)){
				require_once $filePath;
				return true;
			}
		}
	}

}

spl_autoload_register('\Mypos\IPC\Loader::loader');
