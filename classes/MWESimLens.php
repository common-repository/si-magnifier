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
 * A magnifier lens
 * 
 * @author Mark Eastwood <markweastwood.co.uk>
 */
class MWESimLens
{
    /**
     * Lens post id
     * 
     * @var numeric string
     */
    public $post_id;
    
    
    /**
     * Lens name
     * 
     * @var string
     */
    public $post_title;
    
    
    /**
     * Lens configuration options
     * 
     * @var array
     */
    private $_defaults = array(
        'izf' => 115,                   // initial zoom factor
        'wzi' => 5,                     // wheel zoom increment
        'hgt' => 50,                    // height
        'wdh' => 40,                    // width
        'crd' => 0,                     // corner radius
        'squ' => 'no',                  // squared 
        'shz' => 'no',                  // show zoom
        'cur' => 'sim_def_cursor',      // cursor
    );    
  
    
    /**
     * Non-default settings - these represent user configured/saved values
     * 
     * @var array
     */
    private $_lensDeltas = array();
    
    
    /**
     * List of supported cursor css classes
     * 
     * @var array
     */
    public $cursorValues = array(
        'sim_no_cursor', 'sim_def_cursor',
    );

    
    /**
     * Max percentage values per given attribute
     * 
     * @var array
     */
    public $maxPercents = array(
        'hgt' => 150,
        'wdh' => 150,
        'crd' => 50,
    );
    
    
    /**
     * Form validation errors
     * 
     * @var array
     */
    private $_errs = array();
    
    
    /**
     * Name of transient to store any errors
     * 
     * @var string
     */
    private $_errTransient = 'sim_lenserrs';
    
    
    /**
     * Stored deltas - if in the event of a field input validation error the
     * previous stored value will be used
     * 
     * @var array
     */
    private $_storedDeltas = array();
    
    
    /*------------------------------------------------------------------------*/
    
    
    public function __construct()
    {
        $this->_loadLens();
        $this->_getErrors();
    }

    
    /**
     * Load a lens 
     * 
     * @global \WP_Post $post
     * @return void
     */
    private function _loadLens()
    {
        global $post;
        if ( ! $post instanceof \WP_Post ) { return; }

        $this->post_id = $post->ID;
        $this->post_title = $post->post_title;
        $post->filter = false;
        $this->setAttrs( $post->{SIMLENSMETA} );
    }
    
    
    /**
     * Set instance lens attributes.  
     * 
     * @param array $values         - magnifier lens values
     * @param boolean $form_data    - form data flag
     */
    public function setAttrs( $values, $form_data=false )
    {
        if ( ! is_array( $values ) ) { return; }
        
        // store previous form data
        if ( $form_data ) { $this->_storedDeltas = $this->_lensDeltas; }
        
        foreach ( $values as $k => $v ) {
            $attr = strtolower( $k );
            switch ( $attr ) {
                case 'izf':
                case 'wzi':
                case 'hgt':
                case 'wdh':
                case 'crd': $this->setNumber( $attr, $v );
                            break;
                case 'squ':
                case 'shz': $this->setYesNo( $attr, $v );
                            break;
                case 'cur': $this->setCursorClass($v);    
                default :
            }
        }
 
    }
    
    
    /**
     * Set the cursor css class
     * 
     * @param string $v             - cursor type radio button value
     * @return boolean              - true on success, false if not
     */
    public function setCursorClass( $v )
    {
        $cur = is_string( $v ) ? strtolower( trim( $v ) ): '';
        if ( in_array( $cur, $this->cursorValues ) ) {
            
            if ( $this->_defaults['cur'] === $cur ) {
                // same as default
                $this->_removeDelta( 'cur' );
            } else { 
                $this->_lensDeltas['cur'] = $cur;
            }
            return true;
        } 
        
        $this->_errs['cur'] = __("Invalid cursor value", "mwe_si_magnifier");
        $this->_setPreviousValue( $cur );
        
        return false;
    }
    
    
    /**
     * Set the previous field value
     * 
     * @param string $field_id      - form input tag id
     */
    private function _setPreviousValue( $field_id )
    {
        if ( array_key_exists( $field_id, $this->_storedDeltas ) ){
            $this->_lensDeltas[ $field_id ] = $this->_storedDeltas[ $field_id ];
        } else {
            $this->_removeDelta( $field_id );
        }
    }
    
    
    /**
     * Set the lens Title
     * 
     * @return boolean              - true on success, false if not
     */
    public function setTitle( $title )
    {
        $v = ucfirst( trim( strtolower( $title) ) );
        if ( in_array( $v, array('Circle', 'Square', 'Rounded', 'Rectangle') ) ) {
            $this->post_title = $v;
            return true;
        }
        
        return false;
    }
    
    /**
     * Set the post id 
     * 
     * @param string $post_id - SIM lens post id
     * @return boolean - true on success, false if not
     */
    public function setId( $post_id )
    {   
        if ( is_numeric( $post_id ) ) { 
            $this->post_id = intval( $post_id );
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Retrieve any form validation errors
     * 
     * @return void
     */
    private function _getErrors()
    {
        // if post form submission check for any form errors
        $errs = get_transient( $this->_errTransient );
        if ( false === $errs ) { return; }
        $this->_errs = $errs;
    }
    
    
    /**
     * Clear form errors after they have been displayed
     */
    public function clearErrors()
    {
        if ( count( $this->_errs ) > 0 ) {
            $this->_errs = array();
            delete_transient( $this->_errTransient );
        }
    }
    
    
    /**
     * Get a lens configuration - name => value pairs
     * 
     * @return array                - a lens's configuration
     */
    public function getLensConfig()
    {
        return array_merge( $this->_defaults, $this->_lensDeltas );
    }


    /**
     * Return an array of data- attribute name value pairs
     * 
     * @return array                - a lens's configuration deltas
     */
    public function getLensDeltas()
    {
        return $this->_lensDeltas;
    }
        
    
    /**
     * Get a lens's default configuration
     * 
     * @return array                - a lens's default configuration
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }
    
    
    /**
     * Set a numeric lens attribute
     * 
     * @param string $name          - lens attribute name
     * @param string $value         - lens attribute value
     * @return boolean              - true on success, false if not
     */
    public function setNumber( $name, $value )
    {
        $v = intval( trim( $value) );
        
        $cleaned = filter_var( $v, FILTER_VALIDATE_INT);
        if ( false === $cleaned  || $cleaned < 0 ) { 
            $this->_errs[ $name ] = __("Value failed filter or less than 0", "mwe_si_magnifier");
            $this->_setPreviousValue( $name );
            return false;
        }
        
        if ( $this->_tooBig( $name, $cleaned ) ) { 
            $this->_errs[ $name ] = __("Value exceeded max permitted", "mwe_si_magnifier");
            $this->_setPreviousValue( $name );
            return false;
        }   
        
        if ( $this->_defaults[ $name ] === $cleaned ) {
            // same as default
            $this->_removeDelta( $name );
        } else {  
            $this->_lensDeltas[ $name ] = $cleaned;
        }   
        
        return true;
    }
    
    
    /**
     * Check if attribute value exceeds max permitted. 
     * 
     * @param string $name          - attribute name
     * @param mixed $value          - numeric string or int value
     * @return boolean              - true if value exceeds max permitted or false if not
     */
    private function _tooBig( $name, $value )
    {
        if ( array_key_exists( $name, $this->maxPercents) ) { 
            if ( (int)$value > $this->maxPercents[ $name ] ) {
                return true;
            } 
        }
        
        return false;
    }
    
    
    /**
     * Set a yes/no lens attribute
     * 
     * @param string $name          - lens attribute name
     * @param string $value         - lens attribute value
     */
    public function setYesNo( $name, $value )
    {
        $cleaned = filter_var( $value, FILTER_SANITIZE_STRING );
        $setto = $cleaned === 'yes' ? $cleaned : 'no';
        
        if ( $this->_defaults[ $name ] !== $setto ) { 
            $this->_lensDeltas[ $name ] = $setto;
        } else { 
            $this->_removeDelta( $name );
        }
    }
    
    
    /**
     * Remove an attribute from deltas list i.e. value is now 
     * equal to the default for the given attribute
     * 
     * @param string $name          - name of attribute to remove
     */
    private function _removeDelta( $name )
    {
        if ( array_key_exists( $name, $this->_lensDeltas ) ) {
            unset( $this->_lensDeltas[ $name ] );
        }
    }
    
    
    /**
     * Print an error message for a given form field
     * 
     * @param string $field_id      - form field id
     */
    public function printError( $field_id )
    {
        $id = filter_var( $field_id, FILTER_SANITIZE_STRIPPED );
        if ( array_key_exists( $id, $this->_errs ) ) {
            echo '<p class="sim_err">' . esc_html( $this->_errs[ $id ] ) . '</p>';
        }
    }
    
    
    /**
     * Save this lens
     * 
     * @return void
     */
    public function save()
    {
        if ( empty( $this->post_id ) ) { return; }
        
        update_post_meta( $this->post_id, SIMLENSMETA, $this->_lensDeltas );
        if ( count( $this->_errs ) > 0 ) {
            set_transient( $this->_errTransient, $this->_errs, 10 );
        }
    }
}
