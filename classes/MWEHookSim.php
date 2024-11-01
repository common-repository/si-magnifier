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
 * Miscellaneous SIM functionality
 *
 * @author Mark Eastwood <markweastwood.co.uk>
 */
class MWEHookSim extends MWEBaseHook
{
    /**
     * Array of action hooks
     * 
     * @var array
     */
    public $actions = array(
        array('init', 'registerLens'),
        array('plugins_loaded', 'loadTextDomain'),
    );
    
    
    /**
     * Array of filter hooks 
     * 
     * @var array
     */
    public $filters = array(
        array('plugin_action_links_si-magnifier/si-magnifier.php', 'pluginSettingsLink'),
    );

   
    
    /**
     * Admin only menu
     * 
     * @var MWEHookSimMenu
     */
    public $adminMenu;
    
    
    /*------------------------------------------------------------------------*/
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * Load text domain
     */
    public function loadTextDomain()
    {
        $path = basename( dirname( __FILE__ ) ) . '/../languages';
        load_plugin_textdomain( 'mwe_si_magnifier', FALSE, $path );
    }
    
    
    /**
     * Add the settings link to the plugins page
     * 
     * @param array $links          - WP supplied array of plugin page links
     * @return array '$links'       - merged array with settings link included
     */
    public function pluginSettingsLink( $links ) 
    {
        $settings = new MWESimSetting();
        $settings_link = array(
            '<a href="edit.php?post_type=' . SIMLENSCPT . '&page=' . $settings->option('page') . '">Settings</a>'
        );
       
        return array_merge( $links, $settings_link );
    }  
    
    
    /**
     * Register a lens Custom Post Type
     * 
     * @return void
     */
    public function registerLens()
    {
        defined('SIMLENSCPT') or die(
                esc_html_x("Something wrong with env - SIMLENSCPT not defined"
                        , 'do not translate SIMLENSCPT'
                        , 'mwe-si_magnifier')
                ); 
        $usr = \wp_get_current_user();
        if ( ! $usr instanceof  \WP_User ) { return; }
        
        $showui = in_array('administrator', $usr->roles ) ? true : false;
        
        $slug       = get_theme_mod( SIMLENSCPT . '_permalink');
        $the_slug   = ( empty( $slug ) ) ? 'magnifier_lens' : $slug;

        $labels = array(
            'name'                  => 'SIM ' . esc_html_x( 'Lenses','magnifier lens', 'mwe_si_magnifier' ),
            'singular_name'         => 'SIM ' . esc_html_x( 'Lens','magnifier lens','mwe_si_magnifier' ),
            'add_new'               => esc_html__( 'Add a lens', 'mwe_si_magnifier' ),
            'add_new_item'          => esc_html__( 'Add lens', 'mwe_si_magnifier' ),
            'edit_item'             => esc_html__( 'Edit lens', 'mwe_si_magnifier' ),
            'new_item'              => esc_html__( 'New lens', 'mwe_si_magnifier' ),
            'all_items'             => esc_html__( 'All lenses', 'mwe_si_magnifier' ),
            'view_item'             => esc_html__( 'View lens', 'mwe_si_magnifier' ),
            'search_items'          => esc_html__( 'Search lenses', 'mwe_si_magnifier' ),
            'not_found'             => esc_html__( 'Nothing found', 'mwe_si_magnifier' ),
            'not_found_in_trash'    => esc_html__( 'No lenses in trash', 'mwe_si_magnifier' ),
            'menu_name'             => 'SI Magnifier'
        );

        $args = array(
            'labels'        => $labels,
            'hierarchical'  => false,
            'public'        => false,
            'has_archive'   => true,
            'rewrite'       => array('slug' => $the_slug),
            'supports'      => false,
            'show_ui'       => $showui,
            'menu_icon'     => plugins_url('../images/adminicon.png', __FILE__),
            'capabilities' => array('create_posts' => false, 'delete_posts' => true),
            'map_meta_cap' => true,
        );
        register_post_type( SIMLENSCPT, $args );  

        // Flush Rewrite Rules on Activation
        flush_rewrite_rules();
    }    

}
