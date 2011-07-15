<?php


/**
 * JFusion User Class for MRBS
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
 * JFusion User Class for MRBS
 * For detailed descriptions on these functions please check the model.abstractuser.php
 * 
 * @category   JFusion
 * @package    JFusionPlugins
 * @subpackage MRBS
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2011 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org
 */
class JFusionUser_mrbs extends JFusionUser {
    function &getUser($userinfo) {
	    //get the identifier
        $identifier = $userinfo;
        if (is_object($userinfo)) {
            $identifier = $userinfo->email;
        }
        // Get user info from database
		$db = JFusionFactory::getDatabase($this->getJname());
        $params = JFusionFactory::getParams($this->getJname());
		$tbp = $params->get('database_prefix');
        $query = "SELECT id as userid, email, password as password, name FROM " . $tbp . "users WHERE email ='" . $identifier . "'";
        $db->setQuery($query);
        $result = $db->loadObject();
        // read through params for cookie key (the salt used)
        return $result;
    }

    /**
     * returns the name of this JFusion plugin
     * @return string name of current JFusion plugin
     */    
    function getJname() 
    {
        return 'mrbs';
    }
    function deleteUser($userinfo) {
        /* Warning: this function mimics the original mrbs function which is a suggestive deletion, 
		all user information remains in the table for past reference purposes. To delete everything associated
		with an account and an account itself, you will have to manually delete them from the table yourself. */
		// get the identifier
        $identifier = $userinfo;
        if (is_object($userinfo)) {
            $identifier = $userinfo->id_customer;
        }
		$params = JFusionFactory::getParams($this->getJname());
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'UPDATE #__customer SET deleted ="1" WHERE id_customer =' . $db->Quote($identifier);
        $db->setQuery($query);
		$status["debug"][] = "Deleted user";
		return $status;
    }
    function destroySession($userinfo = "", $option = "") {
	    $status = array();
        $status['error'] = array();
        $status['debug'] = array();
	     // use mrbs cookie class and functions to delete cookie
		$params = JFusionFactory::getParams($this->getJname());
    		// session has started
    		session_regenerate_id();
			session_destroy();
			session_name("MRBS_SESSID");  // call before session_set_cookie_params() - see PHP manual
			session_set_cookie_params(time() - 3600, '/mrbs/web/');
			session_start();
			session_unset();
			if(!session_destroy())
			{
				$status["error"][] = "Error Could not delete session, doesn't exist";
			}
			else
			{
				$status["debug"][] = "Deleted session and session data";
			}
		return $status;
    }
    function createSession($userinfo, $options, $framework = true) {
	    $params = JFusionFactory::getParams($this->getJname());
	    $status = array();
        $status['error'] = array();
        $status['debug'] = array();
        // this uses a code extract from authentication.php that deals with logging in completely
		$db = JFusionFactory::getDatabase($this->getJname());
		$password = $userinfo->password_clear;
	    $email = $userinfo->email;
		$password = trim($password);
		$email = trim($email);
		if (empty($email))
		{
		    JText::_('EMAIL_UPDATE_ERROR');
		    echo('e-mail address is required');
		}
		elseif (!Validate::isEmail($email))
		{
		    JText::_('EMAIL_UPDATE_ERROR');
		    echo('invalid e-mail address');
		}
		elseif (empty($password))
		{
		    JText::_('EMAIL_UPDATE_ERROR');
		    echo('password is required');
		}
		elseif (Tools::strlen($password) > 32)
		{
		    JText::_('EMAIL_UPDATE_ERROR');
		    echo('password is too long');
		}
		elseif (!Validate::isPasswd($password))
		{
		    JText::_('EMAIL_UPDATE_ERROR');
		    echo('invalid password');
		}
		else
	    { 
/*****************************************************************************\
*                                                                             *
*   Code from File name       session_php.inc                                 *
*                                                                             *
*   Description     Use PHP built-in sessions handling                        *
*                                                                             *
*   Notes           To use this authentication scheme, set in                 *
*                   config.inc.php:                                           *
*                       $auth["session"]  = "php";                            *
*                                                                             *
\*****************************************************************************/
// THE BELOW CODE WORKS IN ANY OTHER ENVIRONMENT FOR SESSION LOGIN AND LOGOUT BUT JOOMLA LIMITS ITS USE
		/*session_unset();
		session_destroy();
		session_name("MRBS_SESSID");  // call before session_set_cookie_params() - see PHP manual
		session_set_cookie_params(time() - 3600, "/mrbs/web/");
		session_start();
		$_SESSION = array();
		$_SESSION["UserName"] = "grab firstname and lastname here";*/
/*****************************************************************************\
*                                                                            *
*   Code from File name      session_cookie.inc                              *
*                                                                            *
*   Description    Manage sessions via cookies stored in the client browser. *
*                                                                            *
*   Notes          To use this session mechanism, set in config.inc.php:     *
*                  $auth["session"]  = "cookie";                             *
*                                                                            *
\*****************************************************************************/
		$expiry_time = time() + 3600*24*30;
		$UserName = $userinfo->name;
		$token = "AUTH".$UserName."|".$expiry_time;
		$secretz = "";
		$blowfish = new Crypt_Blowfish($secretz);
		setcookie("SessionToken", $blowfish->encrypt($token), time() + 3600*24*30, '/mrbs/web/');
  }


function getUserName()
{
  if (isset($_SESSION) && isset($_SESSION["UserName"]) && ($_SESSION["UserName"] != ""))
  {
    return $_SESSION["UserName"];
  }
  else
  {
    global $HTTP_SESSION_VARS;
    if (isset($HTTP_SESSION_VARS["UserName"]) && ($HTTP_SESSION_VARS["UserName"] != ""))
    {
      return $HTTP_SESSION_VARS["UserName"];
    }
  }
}
	}
    function filterUsername($username) {
        return $username;
    }
    function updatePassword($userinfo, &$existinguser, &$status) {
        jimport('joomla.user.helper');
        $existinguser->password_salt = JUserHelper::genRandomPassword(8);
        $existinguser->password = md5($userinfo->password_clear . $existinguser->password_salt);
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'UPDATE #__customer SET password =' . $db->Quote($existinguser->password) . ', salt = ' . $db->Quote($existinguser->password_salt) . ' WHERE id_customer =' . (int)$existinguser->userid;
        $db->setQuery($query);
        if (!$db->query()) {
            $status['error'][] = JText::_('PASSWORD_UPDATE_ERROR') . $db->stderr();
        } else {
            $status['debug'][] = JText::_('PASSWORD_UPDATE') . ' ' . substr($existinguser->password, 0, 6) . '********';
        }
    }
    function createUser($userinfo, &$status) {
		$db = JFusionFactory::getDatabase($this->getJname());
	    $params = JFusionFactory::getParams($this->getJname());
		
		/* user variables submitted through form (emulated) */
	    $user_variables = array( 
	    'name' => $userinfo->name, // alphanumeric values between 6 and 32 charachters long  
	    'password' => $userinfo->password_clear, // alphanumeric values between 6 and 32 charachters long
	    'email' => $userinfo->email, // alphanumeric values aswell as @ and . symbols between 6 and 128 charachters long 
	    );
		
		/* array to go into table ps_customer */
	    $mrbs_user = array(
	    'id' => "NULL", // column 0 (id_customer)
	    'level' => 0, // column 1 (id_default_group)
	    'name' => $user_variables['name'], // column 2 (secure_key)
	    'password' => md5($user_variables['password']), // column 3 (password)
	    'email' => $user_variables['email'], // column 4 (email)
		);
		
		/* safe data check and validation of array $user_variables
	    no other unique variables are used so this check only includes these */
	
		// Validate level
	    if (!is_numeric($mrbs_user['level']) || $mrbs_user['level'] > 2 || $mrbs_user['level'] < 0){
	        $errors[] = 'level wrong';
	        unset($mrbs_user);
	    }
	
        // Validate full name
	    if (!preg_match("/^([a-zA-Z])+([\'\-]){0,1}([a-zA-Z])*(\s){0,1}([\'\-]){0,1}([a-zA-Z])*(\s){0,1}([\'\-]){0,1}([a-zA-Z])+$/", $user_variables['name'])){
	        $errors[] = 'full name wrong';
	        unset($mrbs_user);
	    }
	 
	 	// Validate password
	    if (!preg_match("/^([a-zA-Z0-9])+$/", $user_variables['password'])){
	        $errors[] = 'invalid password';
	        unset($mrbs_user);
	    }
	    
	    // Validate email
	    if (!preg_match("/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $user_variables['email'])){
	        $errors[] = 'e-mail not valid';
	        unset($mrbs_user);
	    }
		
		/* enter customer account into mrbs database */ // if all information is validated
	    if(isset($mrbs_user))
	    {
	        $tbp = $params->get('database_prefix');
	        foreach($mrbs_user as $key => $value){
	            if($key == "id" || $key == "name" || $key == "password" || $key == "email"){
	                if($key == "id"){
	                    $insert_sql_columns = "INSERT INTO " . $tbp . "users (";
                        $insert_sql_values = "VALUES ("; 
			        }
					
	                else{
	                    $insert_sql_columns .= ", " . $key;
                        $insert_sql_values .= ", '" . $value . "'"; 
					}
	            }
				
	            elseif($key == "level"){
	                $insert_sql_columns .= "" . $key;
                    $insert_sql_values .= "'" . $value . "'";
                }
	            else{
	                $insert_sql_columns .= ", " . $key;
                    $insert_sql_values .= ", '" . $value . "'";
                }
	        }   
			
	        $insert_sql_columns .= ")";
            $insert_sql_values .= ")";
	        $query = $insert_sql_columns . $insert_sql_values;
	        $db->setQuery($query);
			$result = $db->query();

		}
	    else{ 
	        foreach ($errors as $key){
	            JText::_('PASSWORD_UPDATE_ERROR');
	        }
	    }
    }
    function updateEmail($userinfo, &$existinguser, &$status) {
        //we need to update the email
		$params = JFusionFactory::getParams($this->getJname());
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'UPDATE #__customer SET email =' . $db->Quote($userinfo->email) . ' WHERE id_customer =' . (int)$existinguser->userid;
        $db->setQuery($query);
        if (!$db->query()) {
            $status['error'][] = JText::_('EMAIL_UPDATE_ERROR') . $db->stderr();
        } else {
            $status['debug'][] = JText::_('PASSWORD_UPDATE') . ': ' . $existinguser->email . ' -> ' . $userinfo->email;
        }
    }
    function activateUser($userinfo, &$existinguser, &$status) {
        /* change the “active” field of the customer in the ps_customer table to 1 */
		$params = JFusionFactory::getParams($this->getJname());
        $db = JFusionFactory::getDatabase($this->getJname());
		$tbp = $params->get('database_prefix');
        $query = "UPDATE " . $tbp . "customer SET active ='1' WHERE id_customer ='" . (int)$existinguser->userid . "'";
        $db->setQuery($query);
    }
    function inactivateUser($userinfo, &$existinguser, &$status) {
        /* change the “active” field of the customer in the ps_customer table to 0 */
		$params = JFusionFactory::getParams($this->getJname());
        $db = JFusionFactory::getDatabase($this->getJname());
		$tbp = $params->get('database_prefix');
        $query = "UPDATE " . $tbp . "customer SET active ='0' WHERE id_customer ='" . (int)$existinguser->userid . "'";
        $db->setQuery($query);
    }
}