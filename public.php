<?php


/**
 * JFusion Public Class for MRBS
 * 
 * PHP version 5
 * 
 * @category   JFusion
 * @package    JFusionPlugins
 * @subpackage MRBS
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2008 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org 
 */


// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * JFusion Public Class for MRBS
 * For detailed descriptions on these functions please check the model.abstractpublic.php
 * 
 * @category   JFusion
 * @package    JFusionPlugins
 * @subpackage MRBS
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2008 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org
 */


class JFusionPublic_mrbs extends JFusionPublic {


    /**
     * returns the name of this JFusion plugin
     * @return string name of current JFusion plugin
     */    
    function getJname() 
    {
        return 'mrbs';
    }
    
    function getRegistrationURL() {
        return 'authentication.php';
    }
    function getLostPasswordURL() {
        return 'password.php';
    }
	function getLostUsernameURL() {
        return '';
    }
}