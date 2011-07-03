<?php

/**
 * file containing administrator function for the jfusion plugin
 *
 * PHP version 5
 *
 * @category   JFusion
 * @package    JFusionPlugins
 * @subpackage MRBS
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2011 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org
 */


// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * JFusion Admin Class for MRBS
 * For detailed descriptions on these functions please check the model.abstractadmin.php
 *
 * @category   JFusion
 * @package    JFusionPlugins
 * @subpackage MRBS
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2011 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org
 */


class JFusionAdmin_mrbs extends JFusionAdmin
{
    /**
     * returns the name of this JFusion plugin
     * @return string name of current JFusion plugin
     */
    function getJname()
    {
        return 'mrbs';
    }
    function getTablename() {
        return 'users';
    }
    function loadSetup($storePath) {
        //check for trailing slash and generate file path
        if (substr($storePath, -1) == DS) {
            $myfile = $storePath . 'config.inc.php';
        } else {
            $myfile = $storePath . DS . 'config.inc.php';
        }
        if (($file_handle = @fopen($myfile, 'r')) === false) {
            $result = false;
            return $result;
        } else {
            //parse the file line by line to get only the config variables
			$config = array();
            $file_handle = fopen($myfile, 'r');
            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                if (strpos($line, '$db') === 0 && count($config) <= 8) {
                    /* extract the name and value, it was coded to avoid the use of eval() function */
                    // name
                    $vars_strt[0] = strpos($line, "d");
                    $vars_end[0] = strpos($line, " =");
                    $name = trim(substr($line, $vars_strt[0], $vars_end[0] - $vars_strt[0]), "'");     
                    // value
                    $vars_strt[1] = strpos($line, '= "');
                    $vars_strt[1]++;
                    $vars_end[1] = strpos($line, '";');
                    $value = str_replace('"', ' ', trim(substr($line, $vars_strt[1], $vars_end[1] - $vars_strt[1]), "'"));
                    if($name == "db_password")
                    {
                    	// value
                    	$vars_strt[1] = strpos($line, "= '");
                    	$vars_strt[1]++;
                    	$vars_end[1] = strpos($line, "';");
                    	$value = str_replace("'", " ", trim(substr($line, $vars_strt[1], $vars_end[1] - $vars_strt[1]), "'"));
                    }    
                    $config[$name] = $value;
                }
            }
	        fclose($file_handle);
	    }
        return $config;
	}
	
	function getAndSetConfiguration($storePath) {
        //check for trailing slash and generate file path
        if (substr($storePath, -1) == DS) {
            $myfile = $storePath . 'config.inc.php';
        } else {
            $myfile = $storePath . DS . 'config.inc.php';
        }
        if (($file_handle = @fopen($myfile, 'r')) === false) {
            $result = false;
            return $result;
        } else {
        	// create backup file of current configuration (if it doesn't exist)
			$backupfile = preg_replace('/(inc[\.])(php)$/i', '$1'."backup.".'$2', $myfile);
			if(!file_exists($backupfile)){
				if(!copy($myfile, $backupfile)){
					return false;	
				}
			}
		
            //parse the file line by line to get only the config variables
			$config = array();
            $file_handle = fopen($myfile, 'r');
            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                if (strpos($line, '$aut') === 0 && count($config) <= 8) {
                    /* extract the name and value, it was coded to avoid the use of eval() function */
                    // name
                    $vars_strt[0] = strpos($line, "th['") + strlen("th['");
                    $vars_end[0] = strpos($line, "']['");
                    $name = trim(substr($line, $vars_strt[0], $vars_end[0] - $vars_strt[0]), "'");     
                    // key
                    $vars_strt[1] = strpos($line, "']['") + strlen("']['");
                    $vars_end[1] = strpos($line, "'] =");
                    $key = str_replace("'", " ", trim(substr($line, $vars_strt[1], $vars_end[1] - $vars_strt[1]), "'"));
                    // value
                    $vars_strt[2] = strpos($line, "'] = '") + strlen("'] = '");
                    $vars_end[2] = strpos($line, "';");
                    $value = str_replace("'", " ", trim(substr($line, $vars_strt[2], $vars_end[2] - $vars_strt[2]), "'"));
                    echo($name.' = '.$key.' => '.$value.'<br />');
                    if($name !== "db_ext")
                    {
                    	 // name
                    	$vars_strt[0] = strpos($line, "th['") + strlen("th['");
                   		$vars_end[0] = strpos($line, "'] =");
                    	$name = trim(substr($line, $vars_strt[0], $vars_end[0] - $vars_strt[0]), "'");
                    	// value
                    	$vars_strt[1] = strpos($line, "= '");
                    	$vars_strt[1]++;
                    	$vars_end[1] = strpos($line, "';");
                    	$value = str_replace("'", " ", trim(substr($line, $vars_strt[1], $vars_end[1] - $vars_strt[1]), "'"));
                    	echo($name.' = '.$value.'<br />');
                    	// for 2 dimensional arrays
                    	$config[$name] = $value;
                    } 
                    else{   
                    	// for 3 dimensional arrays
                    	$config[$name][$key] = $value;
                    }
                }
            }
	        fclose($file_handle);
	    }
        return $config;
	}
	
	function setupFromPath($storePath) {
	    $config = JFusionAdmin_mrbs::loadSetup($storePath);
        if (!empty($config)) {
        	$auth = JFusionAdmin_mrbs::getAndSetConfiguration($storePath);
        	/*********************************************************************
 			* JFusion authentification settings (EDIT THESE SETTINGS WITH CAUTION)
 			*********************************************************************/
			// How to validate the user/password. One of "none", "config", "db", "db_ext" (this is DEFAULT that JFusion adds), "pop3", "imap", "ldap" "nis" "nw" "ext"
			if($auth["type"] == "db_ext"){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 

			// The server to connect to
			if($auth['db_ext']['db_system'] == $config['dbsys']){
				$true_logic[];
			}
			else{
				$false_logic[];
			}  /* Or 'mysqli', 'pgsql' */
			if($auth['db_ext']['db_host'] == $config['db_host']){
				$true_logic[];
			}
			else{
				$false_logic[];
			}

			// The MySQL username and password to connect with
			if($auth['db_ext']['db_username'] == $config['db_login']){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 
			if($auth['db_ext']['db_password'] == $config['db_password']){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 

			// The name of the database.
			if($auth['db_ext']['db_name'] == $config['db_database']){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 

			// The table that holds the authentication data
			if($auth['db_ext']['db_table'] == $config['db_tbl_prefix'].'users'){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 

			// The names of the two columns that hold the authentication data
			if($auth['db_ext']['column_name_username'] == 'name'){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 
			if($auth['db_ext']['column_name_password'] == 'password'){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 

			// This is the format of the password entries in the table. You can specify 'md5', 'sha1', 'crypt' or 'plaintext'
			if($auth['db_ext']['password_format'] == 'md5'){
				$true_logic[];
			}
			else{
				$false_logic[];
			} 
			
			/* PARAMS */
            //save the parameters into array
            $params = array();
            $params['database_host'] = $config['db_host'];
            $params['database_name'] = $config['db_database'];
            $params['database_user'] = $config['db_login'];
            $params['database_password'] = $config['db_password'];
            $params['database_prefix'] = $config['db_tbl_prefix'];
            $params['database_type'] = $config['dbsys'];
            $params['source_path'] = $storePath;
            $params['cookie_key'] = ''; // not typical but can be modified
			$params['usergroup'] = 0;
			//return the parameters so it can be saved permanently
            return $params;
        }
    }
    function getUserList() {
        //getting the connection to the db
        $db = JFusionFactory::getDatabase($this->getJname());
		$params = JFusionFactory::getParams($this->getJname());
		$tbp = $params->get('database_prefix');
        $query = "SELECT email as email, id as userid from " . $tbp . "users WHERE email NOT LIKE '' and email IS NOT null";
        $db->setQuery($query);
        //getting the results
        $userlist = $db->loadObjectList();
        return $userlist;
    }
    function getUserCount() {
        //getting the connection to the db
        $db = JFusionFactory::getDatabase($this->getJname());
		$params = JFusionFactory::getParams($this->getJname());
		$tbp = $params->get('database_prefix');
        $query = "SELECT count(*) from " . $tbp . "customer WHERE email NOT LIKE '' and email IS NOT null";
        $db->setQuery($query);
        //getting the results
        $no_users = $db->loadResult();
        return $no_users;
    }
    function getUsergroupList() {
        //get the connection to the db
        $db = JFusionFactory::getDatabase($this->getJname());
		$params = JFusionFactory::getParams($this->getJname());
		$tbp = $params->get('database_prefix');
        $query = "SELECT value FROM " . $tbp . "configuration WHERE name IN ('PS_LANG_DEFAULT');";
        $db->setQuery($query);
        //getting the default language to load groups
        $default_language = $db->loadResult();
        //mrbs uses two group categories which are employees and customers, each have there own groups to access either the front or back end
        /*
          Customers only for this plugin
        */
        $query = "SELECT id_group as id, name as name from " . $tbp . "group_lang WHERE id_lang IN ('" . $default_language . "');";
        $db->setQuery($query);
        //getting the results
		$result = $db->loadObjectList();
        return $result;
    }
    function getDefaultUsergroup() {
	    $db = JFusionFactory::getDatabase($this->getJname());
	    $params = JFusionFactory::getParams($this->getJname());
		$tbp = $params->get('database_prefix');
        //we want to output the usergroup name
        $query = "SELECT value FROM " . $tbp . "configuration WHERE name IN ('PS_LANG_DEFAULT');";
        $db->setQuery($query);
        //getting the default language to load groups
        $default_language = $db->loadResult();
        $query = "SELECT name as name from " . $tbp . "group_lang WHERE id_lang IN ('" . $default_language . "') AND id_group IN ('1')";
        $db->setQuery($query);
		return $db->loadResult();
    }
    function allowRegistration() {
        //you cannot disable registration
            $result = true;
            return $result;
    }
	function allowEmptyCookiePath(){
		return true;
	}
	function allowEmptyCookieDomain(){
		return true;
	}
}
