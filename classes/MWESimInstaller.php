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
 * Plugin Installer - install this plugin on plugin activation
 *
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWESimInstaller
{
    /**
     * SIM option 
     * 
     * [
     *  label => value,
     * ]
     * 
     * @var array
     */
    private $_plugin = array(
        'name'      => 'si-magnifier',
        'version'   => '1.0',
        'type'      => 'free',
    );
    
    
    /**
     * SIM lens default deltas
     * 
     * @var array
     */
    private $_defaultDeltas = array(
        'Circle'    => array ('crd' => '50','squ' => 'yes','shz' => 'yes', 'cur' => 'sim_no_cursor'),
        'Square'    => array ('squ' => 'yes'),
        'Rounded'   => array ('crd' => '50'),
        'Rectangle' => array ('shz' => 'yes', 'cur' => 'sim_no_cursor'),            
     );

    
    /*------------------------------------------------------------------------*/        
            
   
    /**
     * Run install actions
     */
    public function install() 
    {
        // get installed versioning info if it exists
        $current = get_option( 'mwe_simplugin', false );
         
        // if new install add plugin option and settings
        if ( false === $current || 
                ! is_array( $current )) {
            add_option( 'mwe_simplugin', $this->_plugin);
            $obj = new MWESimSetting();
            $obj->install();
        } else {
            update_option( 'mwe_simplugin', array_merge( $current, $this->_plugin) );
        }
        
        $this->_addLenses();
    }
    
    
    /**
     * Uninstall SIM
     * 
     * @global \obj $wpdb           - WP $wpdb instance
     * @return void
     */
    public function uninstall()
    {
        global $wpdb;
        if ( ! $wpdb instanceof \wpdb ) { retutn; }
        
        $settings = new MWESimSetting();
        $setting_vals = $settings->getSettingValue('_all_');
        
         // keep data so do nothing
        if ( array_key_exists('keepdata', $setting_vals) &&
                $setting_vals['keepdata'] === 'yes') { 
            return;
        }
        
        // delete all 
        $settings->uninstall();
        $wpdb->delete( $wpdb->posts, array('post_type' => SIMLENSCPT ) );
        $wpdb->delete( $wpdb->postmeta, array('meta_key' => SIMLENSMETA ) );
        delete_option( 'mwe_simplugin' );
     }
    
    
    /**
     * Add a lens post
     * 
     * @param string $title - the title of the lens post
     * @return mixed - string numeric post id or array on error
     */
    private function _addLensPost( $title)
    {
        // create and save the post 
        $post_data = array(
            'post_type'     => SIMLENSCPT,
            'post_status'   => 'publish',
            'post_title'    => trim( $title ),
            'post_name'     => sanitize_title( $title ),
            );
        $post_id = wp_insert_post( $post_data, true );
                    
        return $post_id;
    } 
    
    
    /**
     * Add lens posts
     * 
     * @global obj $wpdb - WP wpdb instance
     * @return void
     */
    private function _addLenses()
    {
        global $wpdb;

        // check existance of lenses
        $sql = $wpdb->prepare(
            "select post_title from {$wpdb->posts} 
             where post_type = '%s' and post_status in ( 'publish', 'trash' );" 
            , SIMLENSCPT );
        $res = $wpdb->get_results($sql, OBJECT_K);
     
        if ( $wpdb->num_rows >= 4 ) { return; }
        
        // Add any missing
        foreach ( $this->_defaultDeltas as $title => $meta ) {
            if ( array_key_exists( $title, $res ) ) { continue; }
            $post_id = $this->_addLensPost( $title );
            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, SIMLENSMETA, $meta );
            }
        }
    }    
    
}
