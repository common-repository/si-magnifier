<?php
/**
 * Plugin Name: SI Magnifier
 * Plugin URI:  https://markweastwood.co.uk/si-magnifier
 * Description: A free image mouse-over magnifier. No second, larger image required. Comes with four predefined lenses. Supports responsive images, mouse wheel zoom, multiple lenses and lens types in posts and pages.
 * Version:     1.2
 * Tested up to: 4.9
 * 
 * Author:      Mark W. Eastwood <markweastwood.co.uk>
 * Author URI:  https://markweastwood.co.uk/about
 * License:     GPLv3
 * 
 * Text Domain: mwe_si_magnifier
 * Domain Path: /languages
 * PHP Version 5 & 7
 */
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

// Constants
define("SIMPATH", plugin_dir_path( __FILE__ ) );
define("SIMURL", plugins_url('/', __FILE__));
define("SIMLENSCPT", "mwesimlens");
define("SIMLENSMETA", "mwe_simlensmeta");


if ( function_exists('\spl_autoload_register') ) {
    
    \spl_autoload_register( '\mwesim\mweAutoLoader');
    
} else { 

    $classlist = array(
        'MWEBaseHook.php',                  // WP hook wrapper
        'MWEHookSim.php',                   // load domain, CPT registration, settings link, save_post
        'MWEHookSimMCE.php',                // kses, tinyMce button
        'MWEHookSimMenu.php',               // admin SIM menu
        'MWEHookSimMenuPremium.php',        // admin SIM premium upgrade menu
        'MWEHookSimMenuSettings.php',       // admin SIM settings menu
        'MWEHookSimScripts.php',            // front and backend styles and scripts
        'MWESimInstaller.php',              // SIM activation and uninstall
        'MWESimLens.php',                   // A SIM lens
        'MWEiSetting.php',                  // SIM settings interface for WP Settings API
        'MWESimSetting.php',                // SIM settings definitions
    ); 
    foreach ( $classlist as $classfile ) { 
        include_once SIMPATH . 'classes/' . $classfile;
    }
    
}


$sim    = new MWEHookSim();
$cssjs  = new MWEHookSimScripts(); 

if ( is_admin() ) { 
    $simmenu  = new MWEHookSimMenu();
    $mce      = new MWEHookSimMCE();
}


// Activation and uninstall hooks
register_activation_hook( __FILE__ , '\mwesim\sim_install');
register_uninstall_hook( __FILE__ , '\mwesim\sim_uninstall');

/**
 * Installer - install the plugin on activation hook
 */
function sim_install()
{
    $obj = new MWESimInstaller( );
    $obj->install();
}

/**
 * Un-install the plugin on un-install hook
 */
function sim_uninstall()
{
    $obj = new MWESimInstaller();
    $obj->uninstall();
}

/**
 * Class auto loader
 * @param string $classname         - name of class to load
 */
function mweAutoloader( $classname )
{
    // strip off namespace
    $bits  = explode('\\', $classname );

    // only autoload plugin classes
    if ( $bits['0'] === 'mwesim' ) { 
        // compose file name from classname and include
        $clp = SIMPATH . 'classes/';
        $classfile = $clp . end( $bits ) . '.php';
        include_once $classfile;
    }        
}    
