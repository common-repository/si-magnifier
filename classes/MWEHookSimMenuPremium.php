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
 * Upgrade free -> premium sub menu
 * 
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWEHookSimMenuPremium extends MWEBaseHook
{
    /**
     * This hooks actions
     * 
     * @var array
     */
    public $actions = array(
        array('admin_menu', 'addSubMenu'),
    );
    
    
    //--------------------------------------------------------------------------
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * Add premium upgrade sub menu item
     */
    public function addSubMenu()
    {
        // add the plugin upgrade page
        add_submenu_page(
            'edit.php?post_type=' . SIMLENSCPT,     // parent slug
            'Upgrade to Premium',                   // title text displayed in page 
            'Upgrade',                              // menu item title
            'upload_plugins',                       // capability
            'premiumupgrade',                       // this menu items page slug
            array( &$this, 'submenuPage' ));        // view method 
    }
    
    
    /**
     * Display upgrade to premium submenu page
     */
    public function submenuPage()
    {
        // Render the view
        include sprintf("%s/../views/sim-upgrade.php", dirname(__FILE__));
    }
    
}

