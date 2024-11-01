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
 * Wrapper for WP Settings API to add a settings page Submenu
 * 
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWEHookSimMenuSettings extends MWEBaseHook
{
    /**
     * This hooks actions
     * 
     * @var array
     */
    public $actions = array(
        array('admin_menu', 'addSubMenu'),
        array('admin_init', 'registerSetting'),
    );

    
    /**
     * This menu item's page slug
     * 
     * @var string
     */
    public $page;

    
    /**
     * Settings 
     * 
     * @var MWESimSetting
     */
    public $settings;
    
    
    //--------------------------------------------------------------------------
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settings = new MWESimSetting();
        $this->settings->setSettingValues();
        $this->page = $this->settings->option('page');
        parent::__construct();
    }
    
    
    /**
     * Add settings sub menu item
     */
    public function addSubMenu()
    {
        // add the plugin upgrade page
        add_submenu_page(
            'edit.php?post_type=' . SIMLENSCPT,      // parent slug
            'SI Magnifier Settings',              // title text displayed in page 
            'Settings',                           // menu item title
            'manage_options',                     // capability
            $this->page,                          // this menu items page slug
            array( &$this, 'submenuPage' ));      // view method 
    }
    
    
    /**
     * Add the sub menu page contents
     * 
     * @return void
     */
    public function submenuPage()
    {
         // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) { return; }

        // add error/update messages - check if the user have submitted the settings
        if ( filter_input(INPUT_GET, 'settings-updated') ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'mwesim_msgs', 'mwesim_msgs', esc_html__( 'Settings Saved', 'mwe_si_magnifier' ), 'updated' );
        }

        // show error/update messages
        settings_errors( 'mwesim_msgs' );
        ?>

        <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <div class="mwe_adminwrapper">
            <form action="options.php" method="post">
                <?php
                 // output security fields for the registered setting 
                 settings_fields( $this->settings->option('group') );

                 // output setting sections and their fields
                 do_settings_sections( $this->page );
                 submit_button( esc_html__('Save Settings', 'mwe_si_magnifier') );
                ?>
            </form>
        </div>
        </div>

        <?php
    }
    
    
    /**
     * Register settings using Settings API
     */
    public function registerSetting()
    {
        // make view file available
        $view = $this->settings->option('view');
        if ( ! empty( $view ) ) {
            include_once sprintf("%s/../views/%s", dirname(__FILE__), $view );
        }
        
        // add setting sections
        foreach ( $this->settings->sections() as $section ) {
            add_settings_section(
                    $section['id'], 
                    $section['title'], 
                    $section['callback'],
                    $this->page
            );
        }
        
        // add setting fields
        foreach ( $this->settings->fields() as $field ) {
            $field['optionname'] = $this->settings->option('name');
            $field['settingval'] = $this->settings->getSettingValue( $field['id']);
            add_settings_field(
                    $field['id'],
                    $field['title'],
                    $field['callback'],
                    $this->page,
                    $field['section'],
                    $field
            );
        }

        register_setting( 
                $this->settings->option('group'), 
                $this->settings->option('name'), 
                array( &$this, 'sanitiseSetting') 
        );
    }
    

    /**
     * Sanitise form inputs
     * 
     * @param mixed $input          - WP API supplied settings form inputs
     * @return mixed $formvals      - sanitised form inputs
     */
    public function sanitiseSetting( $input )
    {
        $formvals = array();

        // not an array ... it should be
        if ( ! is_array( $input ) ) { 
            // return pre form submit values
            return $this->settings->getValues();
        }
                
        foreach ( $input as $k => $v ) {
            switch ( $k ) {
                case 'minrole'          :
                    $val = filter_var( $v, FILTER_SANITIZE_STRING );
                    $formvals[ $k ] = array_key_exists( $v, $this->settings->getSettingArg( $k, 'values') )
                            ? $val : $this->settings->getSettingArg( $k, 'default');
                    break;
                case 'keepdata'         :
                case 'removetags'       :
                    $val = filter_var( $v, FILTER_SANITIZE_STRING );
                    $formvals[ $k ] = $val === 'yes' ? 'yes' : 'no';
                    break;
                default:
            }
        }
        
        return $formvals;
    }

}

