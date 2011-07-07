<?php


 /*
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
 * JFusion Authentication Class for MRBS
 * For detailed descriptions on these functions please check the model.abstractauth.php
 * 
 * @category   JFusion
 * @package    JFusionPlugins
 * @subpackage MRBS
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2011 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org
 */
class JFusionAuth_mrbs extends JFusionAuth 
{
    /**
     * returns the name of this JFusion plugin
     * @return string name of current JFusion plugin
     */
    function getJname() 
    {
        return 'mrbs';
    }
    function generateEncryptedPassword($userinfo) {
	/* This function is configurable depending on chosen encryption (default recommended is md5)
        $the_crypt = md5($userinfo->password_clear);
        return $the_crypt;*/
    }
}