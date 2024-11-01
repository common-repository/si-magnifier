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

//------------------------------------------------------------------------------
// Helper function(s) 
//------------------------------------------------------------------------------

if ( ! function_exists('sim_print_desc') ) {
    /**
     * Print this fields desc paragraph(s)
     * 
     * @param mixed $desc - field description paragraph(s)
     * @return void
     */
    function sim_print_desc( $desc )
    {
        if ( empty( $desc ) ) { return; }
        if ( is_string( $desc ) ) {
            ?>
                <p class="description"><?php echo esc_html( $desc ); ?></p>
            <?php 
           return;
        }

        if ( is_array( $desc ) ) {
            foreach ( $desc as $para ) { 
                ?>
                <p class="description"><?php echo esc_html( $para ); ?></p>
                <?php 
            }
        }
    }
}


if ( ! function_exists('sim_print_rawhtml') ) {
    /**
     * Print additional html
     * 
     * @param mixed $rawhtml - additional html
     * @return void
     */
    function sim_print_rawhtml( $rawhtml )
    {
        if ( empty( $rawhtml ) ) { return; }
        
        if ( is_string( $rawhtml ) ) { 
            echo $rawhtml;
           return;
        }

        if ( is_array( $rawhtml ) ) {
            foreach ( $rawhtml as $html ) { 
                echo $html;
            }
        }
    }
}



//------------------------------------------------------------------------------
// Callbacks
//------------------------------------------------------------------------------

/*
 * SECTION
 */
if ( ! function_exists('sim_sectionHTML') ) {
    /**
     * Display section title
     * $args contains three key/value pairs - 'id', 'title' and 'callback'
     * 
     * @param array $args - WP available args
     */
    function sim_sectionHTML( $args )
    {
        $s = new \mwesim\MWESimSetting();
        $sd = $s->sections();
        
        if ( is_array( $sd ) ) {
            foreach ( $sd as $section ) {
                if ( $section['id'] === $args['id']  &&
                        array_key_exists('desc', $section) ) {
                    sim_print_desc( $section['desc'] );
                }
            }
        } 
    }
}

/*
 * Text input field
 */
if ( ! function_exists('sim_inputText') ) {
    /**
     * Display this settings default field 
     *   
     * @param array $args - Setting_Field instance properties
     */
    function sim_inputText( $args )
    { 
        $value = empty( $args['settingval'] ) 
                 ? $args['default'] : $args['settingval'];
        
        $selected = ' value="' . $value . '"';

        // display value and or checkbox state
        if ( $args['type'] === 'checkbox' ) {
            $selected .= $args['settingval'] === $args['checked']
                ? ' checked="checked"' : '';
        }
        
        ?>
        <input id="<?php echo esc_attr( $args['id'] ); ?>"  
               type="<?php echo esc_attr( $args['type'] ); ?>"  
               name="<?php echo esc_attr( $args['optionname'] ); ?>[<?php echo esc_attr( $args['id'] ); ?>]"
               <?php echo $selected; ?>
        >
        <?php if ( array_key_exists('unit', $args ) ) {
            ?><span class="sim_unit"><?php echo esc_html( $args['unit'] ); ?></span> <?php
        }

        sim_print_desc( $args['desc'] );
        if ( array_key_exists('rawhtml', $args ) ) { 
            sim_print_rawhtml( $args['rawhtml'] ); 
        }
    }
} 

/*
 * Radio input field
 */
if ( ! function_exists('sim_inputRadio') ) {
    /**
     * Display this settings default radio field 
     *   
     * @param array $args - Setting_Field instance properties
     */
    function sim_inputRadio( $args )
    { 
        $val = empty( $args['settingval'] ) 
                 ? $args['default'] : $args['settingval'];

        // add a class 
        $evclass = $args['id'] === 'keepdata' ? 'class="sim_keepdata" ' : '';
        
        // display radio fields
        $name = $args['optionname'] . '[' . $args['id'] . ']';
        foreach ( $args['values']  as $value => $label  ) {
            $selected = $val === $value ? ' checked="checked"' : '';
            ?>
            <input type="radio" <?php echo $evclass; ?>
                   name="<?php echo esc_attr( $name ); ?>" 
                   value="<?php echo esc_attr( $value ); ?>" 
                   <?php echo $selected; ?>
                   ><span class="sim_radspan"><?php echo esc_html( $label ); ?></span>
            <?php
        }
        sim_print_desc( $args['desc'] );
        if ( array_key_exists('rawhtml', $args ) ) { 
            sim_print_rawhtml( $args['rawhtml'] ); 
        }
    }
}


if ( ! function_exists('sim_roleselect') ) {
    
    function sim_roleselect( $args )
    {
        $val = empty( $args['settingval'] ) 
                 ? $args['default'] : $args['settingval'];

        ?>
            <select name="<?php echo esc_attr( $args['optionname'] ); ?>[<?php echo esc_attr( $args['id'] ); ?>]" autocomplete="off">
        <?php 
        foreach ( $args['values']  as $value => $label  ) {
            $selected = $val === $value ? ' selected="selected"' : '';
            ?>
            <option value="<?php echo esc_attr( $value ); ?>"<?php echo $selected; ?>><?php echo esc_html( $label ); ?></option>
        <?php } ?>    
        </select> 
        <?php

        sim_print_desc( $args['desc'] );        
        if ( array_key_exists('rawhtml', $args ) ) { 
            sim_print_rawhtml( $args['rawhtml'] ); 
        }
    }
}
