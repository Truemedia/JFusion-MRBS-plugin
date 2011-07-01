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
	
	function setupFromPath($storePath) {
	    $config = JFusionAdmin_mrbs::loadSetup($storePath);
        if (!empty($config)) {
            //save the parameters into array
            $params = array();
            $params['database_host'] = $config['db_host'];
            $params['database_name'] = $config['db_database'];
            $params['database_user'] = $config['db_login'];
            $params['database_password'] = $config['db_password'];
            $params['database_prefix'] = $config['db_tbl_prefix'];
            $params['database_type'] = $config['dbsys'];
            $params['source_path'] = $storePath;
            $params['cookie_key'] = $config['_COOKIE_KEY_'];
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
