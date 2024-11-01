<?php
/*
Copyright (C) 2016 Mark W. Eastwood 

This file is part of SIMagnifier.

SIMagnifier is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SIMagnifier is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SIMagnifier.  If not, see <http://www.gnu.org/licenses/>.
*/
namespace mwesim;

// is call from WP
defined('ABSPATH') or die("Invalid ...\n"); 


/**
 * A settings data interface for WP Settings API.  
 * 
 * A class instance that implements this interface should define all the 
 * elements required to register one or more settings via the WP Settings API.
 * 
 * All settings defined within a class instance are stored using a single option 
 * name containing an array of setting name => value pairs
 * 
 * @author Mark Eastwood <markweastwood.co.uk>
 */
interface MWEiSetting
{
    /**
     * Method that returns a defined option key value
     * [
     *  'name'  => string (REQUIRED) - option_name used to store this setting
     * 
     *  'group' => string (REQUIRED) - setting_group used for setting registration
     * 
     *  'page'  => string (REQUIRED) - sub menu page to display settings in 
     * 
     *  'view'  => string (Optional) - view file. A file containing a set of 
     *                                 functions to display setting elements 
     * ]
     * 
     * @var array
     */
    public function option( $key );
   
    
    /**
     * Method that returns an array of one or more arrays of defined setting 
     * sections for an option
     *
     * [ 
     *      'id'        => string (optional) - section id 
     *                     Default: value of MWESimSetting->section['id']
     * 
     *      'title'     => string (optional)    - section title
     *                     Default: MWESimSetting->section['title']
     * 
     *      'desc'      => string/array of strings (optional) - section descriptive text
     *                     Default: MWESimSetting->section['desc']
     * 
     *      'callback'  => callable (optional)  - function to display section
     *                     Default: MWESimSetting->section['callback']
     * ]
     * 
     */
    public function sections();
    
    
    
    /*
     * Method that returns an array of one or more arrays of defined setting
     * fields for an option
     * 
     * [
     *      'id'        =>  string (REQUIRED) - filed id used as html tag id 
     * 
     *      'title'     =>  string (optional)   - section title
     *                      Default: MWESimSetting->field['title']
     * 
     *      'callback'  =>  callable (optional) - function to display field
     *                      Default: MWESimSetting->field['callback'] 
     * 
     *      'section'   =>  string (optional)   - id of section this field will appear under 
     *                      Default: value of MWESimSetting->section['id']
     * 
     *      'desc'      =>  string/array of strings (optional) - section's descriptive text
     *                      Default: MWESimSetting->field['desc']
     * 
     *      'vars'      =>  array - additional variables passed to callback.  Variable name => value pairs
     *                      accessible in callback via $args['vars'][<variable name>]
     * 
     *      'type'      =>  string (optional)   - used with default callback. A valid html type
     *                      Default: value of MWESimSetting->field['type']
     * 
     *      'checked'   =>  mixed (optional)    - when type = 'checkbox' value when checked
     *                      Default: value of MWESimSetting->field['checked']
     * 
     *      'unchecked' =>  mixed  (optional)   - when type = 'checkbox' value when unchecked
     *                      Default: value of MWESimSetting->field['checked']
     * 
     *      'default'   =>  mixed (optional)    - default/install value of this setting
     *                      Default: value of MWESimSetting->field['default']
     * 
     *      'values'    =>  array (optional)    - valid values for this setting
     *                      Default: value of MWESimSetting->field['values']
     * ]
     * 
     */    
    public function fields();
    
    
    /**
     * Install actions 
     */
    public function install();
    
    
}
