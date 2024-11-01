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

//global $simLensMgr;

// Is call from within WP
defined('ABSPATH') or die("Invalid ...\n"); 

$numpat = "^[0-9]{1,3}$";
$numlen = "3";

$values = $this->_lens->getLensConfig();
$squared =  $this->_lens->post_title === 'Circle' || $this->_lens->post_title === 'Square'  ? 'yes' : 'no';
?>

<div id="sim_infowrap">
    <img src="<?php echo SIMURL; ?>images/magmouse-100.png">
    <a id="mwe_help"><span id="sim_info_icon"></span></a>
</div>

<div class="wrap">

<div id="sim_configure" class="no_enter_key sim_reducedwidth">
    
    <table class="form-table"><tbody>
        <tr>
            <th scope="row">
                <label>Preview Image</label>
                <button id="sim_previewtoggle" href="#" data-current="small" class="button" 
                    data-src-large="<?php echo SIMURL; ?>images/sample-700x342.png" 
                    data-src-small="<?php echo SIMURL; ?>images/sample-small-700x213.png">
                    <?php esc_html_e("Toggle preview image", "mwe_si_magnifier"); ?>
                </button>
            </th>
            <td>
                <img id="sim_previewimage" data-sim-id="<?php echo $this->_lens->post_id; ?>" 
                    src="<?php echo SIMURL; ?>images/sample-small-700x213.png" 
                    alt="Image hover magnifier preview">
            </td>
        </tr>
         
        <tr id="sim_wdh">
            <th scope="row"><label><?php esc_html_e("Lens width:", 'mwe_si_magnifier'); ?></label></th>
            <td>
                <input type="text" class="sim_number sim_input" name="wdh" id="wdh" 
                    pattern="<?php echo $numpat; ?>" maxlength="<?php echo $numlen; ?>" 
                    value="<?php echo esc_attr( $values['wdh'] ); ?>">
                <span class="sim_unit">&percnt;</span>
                <br />
                <p class="description">
                    <?php esc_html_e("A percentage of the image width.", 'mwe_si_magnifier'); 
                    if ( array_key_exists('wdh', $this->_lens->maxPercents) ) {
                        echo " Max = " . $this->_lens->maxPercents['wdh']; } ?>
                </p> 
                <?php $this->_lens->printError('wdh'); ?>
            </td>
        </tr> 

    <?php $showhgt = $squared === 'no' ? '' : ' sim_noshow'; ?>
    
        <tr id="sim_hgt" class="<?php echo $showhgt; ?>">
            <th scope="row"><label><?php esc_html_e("Lens height:", 'mwe_si_magnifier'); ?></label></th>
            <td>
                <input type="text" class="sim_number sim_input" name="hgt" id="hgt" 
                    pattern="<?php echo $numpat; ?>" maxlength="<?php echo $numlen; ?>" 
                    value="<?php echo esc_attr( $values['hgt'] ); ?>">
                <span class="sim_unit">&percnt;</span>
                <br />
                <p class="description">
                    <?php esc_html_e("A percentage of the image height.", 'mwe_si_magnifier'); 
                    if ( array_key_exists('hgt', $this->_lens->maxPercents ) ) {
                        echo " Max = " . $this->_lens->maxPercents['hgt']; } ?>
                </p> 
                <?php $this->_lens->printError('hgt'); ?>
                <input id="sim_squ" type="hidden" name="squ" value="<?php echo $squared; ?>" class="sim_input">
            </td>
        </tr> 

    <?php $showcrd = $this->_lens->post_title === 'Rounded' ? '' : ' sim_noshow'; ?>
    
        <tr id="sim_crd" class="<?php echo $showcrd; ?>">
            <th scope="row"><label><?php esc_html_e("Lens corner radius:", 'mwe_si_magnifier'); ?></label></th>
            <td>
                <input type="text" class="sim_number sim_input" name="crd" id="crd" 
                    pattern="<?php echo $numpat; ?>" maxlength="<?php echo $numlen; ?>" 
                    value="<?php echo esc_attr($values['crd']); ?>">
                <span class="sim_unit">&percnt;</span>
                <br />
                <p class="description">
                    <?php esc_html_e("A non-negative number. A 50% corner radius on a " .
                             "square gives you a circle.", 'mwe_si_magnifier'); 
                    if ( array_key_exists('crd', $this->_lens->maxPercents) ) {
                        echo " Max = " . $this->_lens->maxPercents['crd'];
                    } ?>
                </p> 
                <?php $this->_lens->printError('crd'); ?>
            </td>
        </tr>
        
        <?php  $yes = "yes" === $values['shz'] ? ' checked="checked"' : '';
            $no  = "no"  === $values['shz'] ? ' checked="checked"' : ''; ?>
        
        <tr id="sim_shz">
            <th scope="row"><label><?php esc_html_e("Show zoom level:", 'mwe_si_magnifier'); ?></label></th>
            <td>
                <input<?php echo $yes; ?> type="radio" name="shz" value="yes" class="sim_input sim_checkin">
                <span class="sim_radspan">Yes</span>
                <input<?php echo $no; ?> type="radio" name="shz" value="no" class="sim_input sim_checkin">
                <span>No</span>
                <br />
                <p class="description">
                    <?php esc_html_e("Show the current zoom level or not.", 'mwe_si_magnifier'); ?>
                </p> 
            </td>
        </tr>
    
        <tr id="sim_cur">
            <th scope="row"><label><?php esc_html_e("Cursor type:", 'mwe_si_magnifier'); ?></label></th>
            <td>
                <?php $nocur = 'sim_no_cursor' === $values['cur'] ? ' checked="checked"' : ''; ?>
                    <input type="radio" name="cur" <?php echo $nocur; ?>
                        value="sim_no_cursor" class="sim_input  sim_checkin" >
                    <span id="sim_no_cursor" class="sim_radspan"><?php esc_html_e("None","mwe_si_magnifier"); ?></span>    

                <?php $cur = 'sim_def_cursor' === $values['cur'] ? ' checked="checked"' : ''; ?>
                    <input type="radio" name="cur" <?php echo $cur; ?>
                        value="sim_def_cursor" class="sim_input  sim_checkin" >
                    <span id="sim_def_cursor"></span>    
                <p class="description">
                    <?php esc_html_e("The mouse cursor to use on image mouse-over.", 'mwe_si_magnifier'); ?>
                </p> 
            </td>
        </tr>
    </tbody></table>      

    <p class="submit">
    <input type="submit" id="sim_savelens" value="Save <?php echo esc_html( $this->_lens->post_title ); ?>"
           name="sim_savelens" class="button button-primary" />
    </p>
    
</div> <!-- sim_configure -->
<br />

<div id="dialog-message" title="SI Magnifier">
    <div>
        <p><?php esc_html_e("When the mouse starts off over the preview image you need to "
            . "move it off then back on to activate the lens.", "mwe_si_magnifier"); ?></p>
        <p><?php esc_html_e("When updating a dimension you need to click outside the field for the "
                . "change to take effect..", "mwe_si_magnifier"); ?></p>
        <p><?php esc_html_e("Both of these additional actions are unnecessary in the premium version.", "mwe_si_magnifier"); ?></p>
        
        <br />
        
        <span><strong><?php esc_html_e("To apply a lens to an image:", 'mwe_si_magnifier'); ?></strong></span>
        <ul><li>(1) <?php esc_html_e("Edit the post or page that contains the image", 'mwe_si_magnifier'); ?> </li>
            <li>(2) <?php esc_html_e("Click the Visual tab in the editor", 'mwe_si_magnifier'); ?></li>
            <li>(3) <?php esc_html_e("Select the image and click", 'mwe_si_magnifier'); ?><span id="sim_icon"></span></li>
            <li>(4) <?php esc_html_e("Select the lens to apply from the drop-down and click OK", 'mwe_si_magnifier'); ?></li>
            <li>(5) <?php esc_html_e("Update, save the post or page", 'mwe_si_magnifier'); ?></li></ul>
    </div>
</div>

</div>
