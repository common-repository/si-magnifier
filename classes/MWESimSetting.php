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
 * A class that defines a single WP option that contains one or more setting
 * sections and fields 
 * 
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWESimSetting implements MWEiSetting
{
    /**
     * Option data used for registration and display
     * 
     * @var array
     */
    private $_option = array(
        'name' => 'mwe_simsettings',
        'group'=> 'mwe_simgroup',
        'page' => 'simsettings',
        'view' => 'sim-settings.php',
    );
    
    
    /**
     * Default setting section values
     * 
     * @var array 
     */
    public $defSection = array(
        'id' => 'mwesim_section',
        'title' => ' ',
        'desc' => '',
        'callback' => 'sim_sectionHTML'
    );
    
    
    /**
     * Default setting field values
     * 
     * @var array
     */
    public $defField = array(
        'id'        => '',
        'title'     =>  ' ',
        'callback'  =>  '',                 // sim_input<Type>
        'section'   =>  'mwesim_section',
        'desc'      =>  '',
        'vars'      =>  array(),
        'type'      =>  'text',
        'checked'   =>  'yes',
        'unchecked' =>  'no',
        'default'   =>  '',
        'values'    =>  array(), 
    );
    
    
    /**
     * Default radio (pair) values - html => attr value 
     * 
     * @var array
     */
    public $defradiovalues = array(
        'yes' => 'Yes',
        'no' => 'No',
    );

    
    /**
     * Stored setting values
     * 
     * @var mixed  boolean | array 
     */
    private $_settingValues = false;
    
    
    /*------------------------------------------------------------------------*/


    /**
     * Return the value of a setting option data key
     * 
     * @param string $key           - option key 
     * @return mixed                - value of option key or null
     */
    public function option( $key ) 
    {
        $k = trim( strtolower( $key) );
        if ( $k === 'all' ) {
            return $this->_option;
        }
        
        if ( array_key_exists( $k, $this->_option ) ) {
            return $this->_option[ $k ];
        }
        
        return null;
    }

    
    /**
     * Apply defaults to defined sections
     */
    public function sections()
    {
        $defined = $this->sectionData();
        
        if ( empty( $defined ) ) { 
            return array( $this->defSection ); 
        }
        
        $sections = array();
        foreach ( $defined as $section ) {
            $sections[] = array_merge( $this->defSection, $section );
        }
        $sections[] = $this->defSection;
        
        
        return $sections;
    }
    
    
    /**
     * Define one or more section definitions
     */
    public function sectionData()
    {
        return array();
    }
    
    
    /**
     * Apply defaults to defined fields
     */
    public function fields()
    {
        $defined = $this->fieldData();
         
        $fields = array();
        foreach ( $defined as $k => $v ) {
            if ( ! array_key_exists('callback', $v ) ) {
                $v['callback'] = array_key_exists('type', $v )
                        ? 'sim_input'. ucfirst($v['type'])
                        : 'sim_inputText';
            }
            if ( array_key_exists('type', $v) && 
                    $v['type'] === 'radio' &&
                    ! array_key_exists('values', $v) ) {
                $v['values'] = $this->defradiovalues;
            }
            $fields[ $k ] = array_merge( $this->defField, $v );
        }
        
        return $fields;
    }
    
    
    
    /**
     * Define one or more field definitions
     */
    public function fieldData()
    {
        $roletrans = array(
            'administrator' => esc_html_x('Administrator','WP user role','mwe_si_magnifier'),
            'editor'        => esc_html_x('Editor','WP user role','mwe_si_magnifier'),
            'author'        => esc_html_x('Author','WP user role','mwe_si_magnifier'),
            'contributor'   => esc_html_x('Contributor','WP user role','mwe_si_magnifier'),
        );

        $this->defradiovalues = array(
            'yes' => esc_html_x('Yes', 'html checkbox label','mwe_si_magnifier'),
            'no'  => esc_html_x('No', 'html checkbox label','mwe_si_magnifier'),
        );
        
        return array(
            
            array(
                'id' => 'minrole',
                'title'    => esc_html__("Minimum user role", 'mwe_si_magnifier'),
                'desc'     => esc_html__("Users with the selected role along with the mapped capability will be able access the SIM "
                            . "rich text editor button.", 'mwe_si_magnifier'),
                'rawhtml'  => '<div id="sim_capmap"><span>' . esc_html__("Role &rarr; Capability map",'mwe_si_magnifier') . '</span><div>'
                            . '<span>' . $roletrans['administrator'] .' &rarr; <code>manage_options</code></span>'
                            . '<span>' . $roletrans['editor'] . ' &rarr; <code>edit_others_posts</code></span>'
                            . '<span>' . $roletrans['author'] . ' &rarr; <code>edit_published_posts</code></span>'
                            . '<span>' . $roletrans['contributor'] . ' &rarr; <code>edit_posts</code></span></div></div>',
                'callback' => 'sim_roleselect',
                'values'   => array(
                    'administrator' => $roletrans['administrator'],
                    'editor'        => $roletrans['editor'],
                    'author'        => $roletrans['author'],
                    'contributor'   => $roletrans['contributor'],
                ),
                'default'  => 'administrator',
            ),  
            
            array(
                'id'      => 'keepdata',
                'title'   => esc_html__("Keep configuration data on uninstall", 'mwe_si_magnifier'),
                'desc'    => esc_html__("You may want to do this if upgrading to SIM premium.", 'mwe_si_magnifier'),
                'type'    => 'radio',
                'default' => 'no',
            ),   
            
        );
    }
    
    
    /**
     * Install settings
     * 
     * @return boolean              - true if option is added to the DB, false otherwise
     */
    public function install()
    {
        if ( false === $this->isValidName() ) {
            return false;
        }

        $settings = array();
        foreach ( $this->fields() as $field ) {
            if ( ! array_key_exists('id', $field) 
                    || empty( $field['id'] ) ) { 
                continue;
            }
           
            $settings[ $field['id'] ] = array_key_exists('default', $field)
                    ? $field['default'] : $this->defField['default'];
        }

        return add_option( $this->_option['name'], $settings );
    }
    
    
    
    /**
     * Delete all settings 
     */
    public function uninstall()
    {
        delete_option( $this->_option['name'] );
    }
    
    
    /**
     * Test option name to see if it conforms to naming standard
     * 
     * @return boolean              - true if name conforms to naming standard, false if not
     */
    public function isValidName()
    {
        /*
         * Namimg standard: max 64 lowercase characters with no spaces
         */
        if ( ! array_key_exists('name', $this->_option) ) { 
            return false; 
        }
        
        if ( empty( $this->_option['name']) ) {
            return false;
        }
        
        if ( preg_match('/[A-Z ]/', $this->_option['name']) === 1 ) {
            return false;
        }
        
        if ( strlen( $this->_option['name']) > 64 ) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Return a value for a given setting's  arg
     * 
     * @param string $id            - field, setting id
     * @param string $key           - name field key whose value is to be returned
     * @return mixed                - value of key or empty string
     */
    public function getSettingArg( $id, $key )
    {
        foreach ( $this->fields() as $field ) {
            if ( $field['id'] === $id &&
                    array_key_exists( $key, $field ) ) {
                
                return $field[ $key ];
            }
        }
        
        return '';
    }
    

    /**
     * Return all or a setting value.  If a setting exists but as not been set
     * it's default value is returned
     * 
     * @param string $key           - setting key/id or '_all_' for all settings
     * @return mixed                - setting value or null if setting does not exist
     */
    public function getSettingValue( $key ) 
    {
        $k = filter_var( $key, FILTER_SANITIZE_STRING );
        if ( ! empty( $k ) ) { 
            $settings = false === $this->_settingValues 
                    ? get_option( $this->_option['name'], array() ) : $this->_settingValues;

            if ( $k === '_all_' ) { return $settings; }

            if ( array_key_exists( $k, $settings) ) {
                return $settings[ $k ];
            } else {
                return $this->getSettingArg( $k, 'default' );
            }
        }
        
        return null;
    }        

    
    /**
     * Set instance property with current setting values
     */
    public function setSettingValues()
    {
        $this->_settingValues = get_option( $this->_option['name'], array() );
    }
    
    
    /**
     * Get current settings in 'form' format
     * 
     * @return array                - setting field id => value
     */
    public function getValues() 
    {
        if ( false === $this->_settingValues ) { 
            $this->setSettingValues();
        }
        
        return $this->_settingValues;
    }
}
