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
 * SIM tinyMCE button
 *
 * @author mark
 */
class MWEHookSimMCE extends MWEBaseHook
{
    /**
     * Action hooks
     * 
     * @var array
     */
    public $actions = array(
        array('wp_kses_allowed_html', 'allowSimAttr', 11),
        array('admin_footer', 'localiseMCE'),
    );
    
    
    /**
     * Filter hooks
     * 
     * @var array
     */
    public $filters = array(
        array('mce_external_plugins', 'addMceButton'),
        array('mce_buttons', 'registerMceButton'),
    );
    
    
    /**
     * Flag that determines if a user can add SIM lenses to images or not
     * 
     * @var boolean
     */
    private $_userCan;
    
    
    /**
     * Role to capability mappings
     * Capabilities hard coded in MWESimSettings 'minrole' values definition
     *  
     * @var array
     */
    private $_roleMap = array(
        'administrator' => 'manage_options',
        'editor'        => 'edit_others_posts',
        'author'        => 'edit_published_posts',
        'subscriber'    => 'edit_posts',
    );
    
    
    /*------------------------------------------------------------------------*/
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    /**
     * Append mce button script path to WP plugins array
     * 
     * @param array $plugin_array   - passed by WP. List of plugins.
     * @return array $plugin_array  - mce-button script appended to array
     */
    public function addMceButton( $plugin_array )
    {
        $cando = isset( $this->_userCan ) ? $this->_userCan : $this->_canUser();
        if ( $cando === true ) {
            $plugin_array['sim_magnifier_button'] 
                    = plugins_url('../js/mce-button-min.js', __FILE__ ); 
        }
        
        return $plugin_array;
    }

    
    /**
     * Register/add button to mce buttons array
     * 
     * @param array $buttons        - array of registered mce buttons
     * @return array $buttons       - list of editor buttons 
     */
    public function registerMceButton( $buttons )
    {                   
        $cando = isset( $this->_userCan ) ? $this->_userCan : $this->_canUser();
        if ( $cando === true ) {
            array_push( $buttons, "sim_magnifier_button");
        }
        
        return $buttons;
    }
    
    
    /**
     * Localise translation strings for tinyMCE dialog
     * 
     * @global object $wpdb         - instance of WP \wpdb
     */
    public function localiseMCE()
    {
        $cando = isset( $this->_userCan ) ? $this->_userCan : $this->_canUser();
        if ( $cando === true ) {
            global $wpdb;
            $sql = $wpdb->prepare(
                "select ID, post_title from {$wpdb->posts} where post_type = '%s'  
                and post_status = 'publish' and post_title in ('Circle', 'Rectangle', 'Rounded', 'Square');"
                ,SIMLENSCPT );
            $res = $wpdb->get_results($sql); 

            $map = array();
            foreach ( $res as $rowObj ) { $map[ $rowObj->ID ] = $rowObj->post_title; }

            $strs = array(
                'map' => $map,
                'nosel' => esc_html__("Please select an image.", "mwe_si_magnifier"),
                'conf' => esc_html__("CONFIRMATION: Remove lens?", "mwe_si_magnifier"),
                'lbl' => esc_html__("Select a Lens", "mwe_si_magnifier"),
                'invalidlensa' => esc_html__("This image contains an invalid lens.", "mwe_si_magnifier"),
            );

            wp_localize_script( 'common', 'simstrings', $strs);
        }
    }
    
    
    /**
     * Allow non-admins to add SIM img tag attribute in post content
     * by adding to kses allowable list
     *  
     * @param array $allowed_html   - wp supplied list of tags and their attributes
     * @return array                - modified allowable tag, attribute list
     */
    public function allowSimAttr( $allowed_html )
    {
        $cando = isset( $this->_userCan ) ? $this->_userCan : $this->_canUser();
        if ( $cando === true ) {
            // Not for this plugin to override any custom settingss
            // Only add if 'img' tags are allowed
            if ( array_key_exists('img', $allowed_html ) ) {
                $allowed_html['img']['data-sim-id'] = true;
            }
        }
        
        return $allowed_html;
    }            

    
    /**
     * Determine if user is allowed to add SIM lenses to images
     * 
     * @return boolean              - true if user can, false if not
     */
    protected function _canUser()
    {
        // called too early
        if ( ! function_exists('current_user_can') ) { return false; }
        
        if ( current_user_can('manage_options') ) { 
            $this->_userCan = true;
            return true;
        }
        
        $settings = new MWESimSetting();
        $role = $settings->getSettingvalue('minrole');
        if ( array_key_exists( $role, $this->_roleMap ) 
                && current_user_can( $this->_roleMap[ $role ] ) ) {
            $this->_userCan = true;
            return true;
        }
        $this->_userCan = false;
        
        return false;
     }    
}
