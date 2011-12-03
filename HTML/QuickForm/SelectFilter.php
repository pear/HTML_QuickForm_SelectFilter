<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Nicolas Hoizey <nicolas@hoizey.com>                         |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/text.php';

/**
 * Class to dynamically create a filter for an HTML SELECT
 *
 * @author       Nicolas Hoizey <nicolas@hoizey.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class HTML_QuickForm_SelectFilter extends HTML_QuickForm_text {
    
    // {{{ properties

    /**
     * Contains the select targets
     *
     * @var       array
     * @since     1.0
     * @access    private
     */
    var $_targets = array();

    // }}}
    // {{{ constructor
        
    /**
     * Class constructor
     * 
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field label
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string 
     *                                      or an associative array
     * @param     string    $selectName     (optional)Target select field name
     * @param     boolean   $mustBeginWith  (optional)Indicates wether the select
     *                                      must begin with the filtered value or
     *                                      can just contain it
     * @since     1.0
     * @access    public
     * @return    void
     */
    function HTML_QuickForm_SelectFilter($elementName = null, $elementLabel = null, $attributes = null, $selectName = null, $mustBeginWith = false)
    {
        $this->HTML_QuickForm_text($elementName, $elementLabel, $attributes);
        $this->_type = 'selectfilter';
        if (!is_null($selectName)) {
        	$this->addSelect($selectName, $mustBeginWith);
        }
    } //end constructor
    
    // }}}
    // {{{ addSelect()

    /**
     * Adds a select as a target of the filter 
     * 
     * @param     string    $selectName     Target select field name
     * @param     boolean   $mustBeginWith  (optional)Indicates wether the select
     *                                      must begin with the filtered value or
     *                                      can just contain it
     * @since     1.0
     * @access    public
     * @return    void
     */
    function addSelect($selectName, $mustBeginWith = false)
    {
		$this->_targets[$selectName] = $mustBeginWith;
    } //end func addSelect

    // }}}
    // {{{ apiVersion()

    /**
     * Returns the current API version 
     * 
     * @since     1.0
     * @access    public
     * @return    double
     */
    function apiVersion()
    {
        return 1.0;
    } //end func apiVersion

    // }}}
    // {{{ toHtml()

    /**
     * Returns the filter in HTML
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml()
    {
		$onKeyUp = '';
        foreach($this->_targets as $target => $mustBeginWith) {
       		$onKeyUp .= 'javascript:QF_SelectFilter(this.value, this.form[\''.$target.'\'], '.($mustBeginWith ? 'true' : 'false').');';
        }
        $this->updateAttributes(array('onkeyup' => $onKeyUp));

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $tabs = $this->_getTabs();
            $js = '';
            if (count($this->_targets) > 0 && !defined('HTML_QUICKFORM_SELECTFILTER_EXISTS')) {
                $js = '
<script type="text/javascript">
//<![CDATA[
// begin javascript for filtered select
var values = new Array();

function QF_SelectFilter(str, list, muststart) {
	if (!values[list.name]) {
	    // first use of this list, we store initial elements
        nb = list.options.length;
        values[list.name] = new Array(nb);
        for (i = 0; i < nb; i++) {
            values[list.name][i] = list.options[i];
        }
    }

    // empty list
    for(i = list.options.length - 1; i >= 0; i--) {
        list.options[i] = null;
    }

    // put needed elements
    index = 0;
    for (i = 0; i < nb; i++) {
        if (str == ""
                || (muststart && values[list.name][i].text.toUpperCase().indexOf(str.toUpperCase()) == 0)
                || (!muststart && values[list.name][i].text.toUpperCase().indexOf(str.toUpperCase()) != -1)) {
            list.options[index] = new Option(values[list.name][i].text, values[list.name][i].value);
            index++;
        }
    }
    if (index == 1) {
        list.options[0].selected = true;
    }
}
// end javascript for filtered select
//]]>
</script>
';
                define('HTML_QUICKFORM_SELECTFILTER_EXISTS', true);
            }
            return $js . parent::toHtml();
        }
    } //end func toHtml

    // }}}
} //end class HTML_QuickForm_SelectFilter

if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerElementType('SelectFilter', 'HTML/QuickForm/SelectFilter.php', 'HTML_QuickForm_SelectFilter');
}
?>