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
	/*
        $params = JFusionFactory::getParams($this->getJname());
        $the_crypt = md5($params->get('cookie_key') . $userinfo->password_clear);
        return $the_crypt;*/
    }
}