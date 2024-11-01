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
 * WP hooks for adding plugin Lens CPT
 * 
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWEHookSimMenu extends MWEBaseHook
{
    /**
     * Array of action hooks
     * 
     * @var array
     */
    public $actions = array(
        array('admin_footer', 'addJSData'),
    );
    
    
    /**
     * Array of filter hooks 
     * 
     * @var array
     */
    public $filters = array(
        array('post_row_actions', 'removeQuickEdit', 10, 2)
    );    
    
    
    /**
     * This menu's sub menu objects
     * 
     * @var array
     */
    private $_submenus = array();
    

    /**
     * add_meta_box command args for this cpt 
     * 
     * @var array
     */
    public $metaboxes = array(
        array('id' => 'sim_lensedit','title' => 'Configuration','meth' => 'showLensEdit','pri' => 'high'),
    );
    
    
    /**
     * Lens object representing whichever lens is being edited
     * 
     * @var MWESimLens
     */
    private $_lens;
    
    
    /*------------------------------------------------------------------------*/        

    
    /**
     * Constructor
     */
    public function __construct()
    {
        // CPT specific save post
        $this->actions[] = array('save_post_' . SIMLENSCPT, 'save');
        $this->actions[] = array('add_meta_boxes_' . SIMLENSCPT, 'addMetaBoxes');
        parent::__construct();
 
        $this->_submenus[] = new MWEHookSimMenuSettings();
        $this->_submenus[] = new MWEHookSimMenuPremium();
    }
    
    
    /**
     * Add CPT meta boxes
     */
    public function addMetaBoxes()
    { 
        global $post;
        foreach ( $this->metaboxes as $abox ) {
            add_meta_box( 
                $abox['id'], 
                $abox['id'] === 'sim_lensedit' ? $post->post_title : $abox['title'], 
                array( &$this, $abox['meth']), 
                SIMLENSCPT,
                array_key_exists('ctx', $abox) ? $abox['ctx'] : 'advanced',
                array_key_exists('pri', $abox) ? $abox['pri'] : 'default'
            );
        }
    } 
    
    
    /**
     * Display lens edit page
     * 
     * @param obj $post - supplied by WP
     */
    public function showLensEdit( $post )
    {
        $this->_lens = new MWESimLens();

        // Render the view
        include sprintf("%s/../views/sim-lensedit.php", dirname(__FILE__));
        $this->_lens->clearErrors();
    }
    
    
    /**
     * Display upgrade meta boxe
     */
    public function showUpgrade() 
    { 
        // Render the view
        include sprintf("%s/../views/sim-upgrade.php", dirname(__FILE__));
    }    
    
    
    /**
     * Pass CPT lens data to js on admin edit post page ONLY
     */
    public function addJSData()
    {
        global $post;
        if ( ! $post instanceof \WP_Post ) { return; }
        
        $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRIPPED );
        if ( ! is_admin() 
                || ! current_user_can('manage_options') 
                || $post->post_type !== SIMLENSCPT 
                || $action !== 'edit') { 
            return; 
        }
        
        $data = array(
            'lensdata'  => array( $this->_lens->post_id => $this->_lens->getLensDeltas() ),
            'datakey'  => $this->_lens->post_id,
        );

        wp_localize_script('sim_js', 'simdata', $data);
    }
    
    
    /**
     * Remove the Quick Edit link from admin listing page for
     * plugin CPTs
     * 
     * @param array $actions    - WP supplied array of action links
     * @return array            - modified action links if applicable
     */
    public function removeQuickEdit( $actions )
    {   
        $type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRIPPED );
        if ( is_admin() 
                && current_user_can( 'manage_options')
                && $type === SIMLENSCPT ) {     
            // Remove the Quick Edit link
            if ( is_array($actions)                         && 
                    isset($actions['inline hide-if-no-js'])
                ) {
                unset( $actions['inline hide-if-no-js'] );
            }
        }   
        
        return $actions;        
    }    
    
    
    /**
     * Save lens post data 
     * 
     * @param string $post_id       - WP supplied numeric post ID
     * @return mixed                - post id or void
     */
    public function save( $post_id )
    {
        // do nothing if a revision or auto save
        if ( wp_is_post_revision( $post_id ) ) { return; }
        if ( wp_is_post_autosave( $post_id ) ) { return; }
        
        $form_inputs = filter_input_array( INPUT_POST );
        if ( empty( $form_inputs ) ) { return; }

        $this->_lens = new MWESimLens();
        $this->_lens->setAttrs( $form_inputs, true );
        $this->_lens->save();
    }

}