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
 * Register and enqueue plugin styles and scripts
 * 
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWEHookSimScripts extends MWEBaseHook
{
    /**
     * Use minified flag.  
     * Minified file base name (not inc. extension) terminated by '-min'
     * 
     * @var boolean
     */
    private $_minified = true;
    
    
    /**
     * Action hooks
     * 
     * @var array
     */
    public $actions = array(
        array('wp_enqueue_scripts', 'addScripts'),
        array('admin_enqueue_scripts', 'addAdminScripts'),
        array('wp_footer', 'localiseJS'),
    );
    
    
    /**
     * Script args
     * 
     * @var array
     */
    public $scripts = array(
        'fe' => array(
            array('sim_js' , 'js/hoverlens.js', '', '', true),
            ),
        'admin' => array(
            array('sim_js' , 'js/hoverlens.js', '', '', true),
            array('sim_adm_js', 'js/admin.js','', '', true),
        ),
    );

    
    /**
     * Style args
     * 
     * @var array
     */
    public $styles = array(
        'fe' => array(
            array('sim_lens_styles','css/styles.css'),
        ),
        'admin' => array(
            array('sim_jqui_styles', 'css/jquery-ui.css'),
            array('sim_adm_styles','css/admin.css'),
            array('sim_styles','css/styles.css'),
        ),
    );
    
    
    /**
     * Scripts to enqueue
     * 
     * @var array
     */
    public $enqueueScripts = array(
        'fe' => array(
            'jquery', 
            'sim_js',
        ),
        'admin' => array(
            'jquery-ui-dialog',
            'sim_adm_js',
            'sim_js',
        ),
     ); 

    
    /**
     * Styles to enqueue
     * 
     * @var array
     */
    public $enqueueStyles = array(
        'fe' => array('sim_lens_styles'),
        'admin' => array(
            'sim_jqui_styles',
            'sim_adm_styles',
            'sim_styles',
        ),
    );
    
    
    /**
     * Deactivate si_magnifier effect for mobile devices flag
     * 
     * @var boolean 
     */
    private $_deactivate = false;
    

    /**
     * Settings object
     * 
     * @var \MWESimSettings
     */
    private $_settings;
    
    
    //--------------------------------------------------------------------------
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_settings = new MWESimSetting();
        $this->_deactivate = $this->isMobile();
        parent::__construct();
    }
    
    
    /**
     * Is mobile device - taken straight from WP core wp_is_mobile()
     * 
     * @return boolean              - true if a mobile device, false if not
     */
    public function isMobile()
    {
        $agent = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING );
        if ( empty( $agent ) ) { return false; }
        
	if ( strpos( $agent, 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
		|| strpos( $agent, 'Android') !== false
		|| strpos( $agent, 'Silk/') !== false
		|| strpos( $agent, 'Kindle') !== false
		|| strpos( $agent, 'BlackBerry') !== false
		|| strpos( $agent, 'Opera Mini') !== false
		|| strpos( $agent, 'Opera Mobi') !== false ) {
            return true;
	}
        
        return false;
     }
     
     
    /**
     * Add front end scripts and styles
     */
    public function addScripts()
    {
        // skip loading of front-end scripts if deactive
        if ( $this->_deactivate !== true ) {
            $this->add( $this->scripts['fe'], '\wp_register_script' );
            $this->add( $this->enqueueScripts['fe'], '\wp_enqueue_script'  );
        }
        
        $this->add( $this->styles['fe'], '\wp_register_style' );
        $this->add( $this->enqueueStyles['fe'], '\wp_enqueue_style' );
    }

    
    /**
     * Add back-end scripts and styles
     * 
     * @global obj $post            - \WP_Post instance
     */
    public function addAdminScripts( )
    {
        global $post;
        
        $type = ! $post instanceof \WP_Post 
                ? filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRIPPED )
                : $post->post_type;
        
        if ( ! is_admin()
                || ! current_user_can('manage_options') 
                || $type !== SIMLENSCPT ) { 
            return; 
        }
        
        $this->add( $this->scripts['admin'], '\wp_register_script' );
        $this->add( $this->enqueueScripts['admin'], '\wp_enqueue_script' );
        $this->add( $this->styles['admin'], '\wp_register_style' );
        $this->add( $this->enqueueStyles['admin'], '\wp_enqueue_style' );
    }    
    
    
    /**
     * Add script or style file to WP
     * 
     * @param mixed $script_list    - lists of scripts if any
     * @param string $function      - wp function to add scripts with
     */
    public function add( $script_list, $function )
    {
        if ( empty( $script_list ) || 
                ! is_array( $script_list ) ) { return; }
        
        foreach ( $script_list as $script ) {
            // check there is a source
            if ( ! is_array( $script ) ) { $script = array( $script ); }
            
            // prepend plugin path to source
            if ( count( $script ) > 1 ) {
                $script[1] = $this->_minified
                        ? str_replace( array('.css', '.js'), array('-min.css','-min.js' ), $script[1] )
                        : $script[1];
                $script[1] = SIMURL . $script[1];

            }
            $fx = new \ReflectionFunction( $function );
            $fx->invokeArgs( $script );
        }
    }    
    
    
    /**
     * Get all lens deltas and localise for FE JS script
     * 
     * @return void 
     */
    public function localiseJS()
    {
        if ( $this->_deactivate ) { return; }
        
        global $wpdb;
        if ( ! $wpdb instanceof \wpdb ) { return; }
        
        $sql = $wpdb->prepare(
                "select id, meta_value from {$wpdb->posts}, {$wpdb->postmeta} 
                where post_status = 'publish' 
                and id = {$wpdb->postmeta}.post_id 
                and meta_key = '%s';", 
            SIMLENSMETA );
        $res = $wpdb->get_results( $sql );
        
        $i = 0;
        $lenses = array();
        foreach ( $res as $row ) {
            if ( $i === 4 ) { break; }
            $lenses[ $row->id ] = maybe_unserialize( $row->meta_value );
            $i++;
        }
        $data = array('lensdata' => $lenses );
        
        wp_localize_script('sim_js', 'simdata', $data);
    }
    
}
