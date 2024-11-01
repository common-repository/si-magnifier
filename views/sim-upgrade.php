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

// Is call from within WP
defined('ABSPATH') or die("Invalid ...\n"); 

?>
<div class="wrap sim_premiumwrap">
    
    <h1><?php esc_html_e("SI Magnifier upgrade", 'mwe_si_magnifier'); ?></h1>
    
    <section id="sim_upgrade">
        <img src="/wp-content/plugins/si-magnifier/images/magmouse-400.png" alt="SI-magnifier" style="position:absolute;margin-left:50%;">
        <p class="sim_bigger">
            <?php esc_html_e("There is a premium version, just", 'mwe_si_magnifier'); ?><span id="sim_price">£4.19</span>(GBP). 
            <?php esc_html_e("It does everything this version does as well as supporting ...", 'mwe_si_magnifier'); ?>        </p>

        <ul class="mwe_listdash">
            <li><?php esc_html_e("WooCommerce single product gallery images", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("WP Multi-site", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("additional len's styling options", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("additional cursor types", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("no limit imposed on the number of lenses", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("use of CSS class names to render lenses", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("filter posts and pages in admin listings based on existance of one or more lenses in a post or page", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("use full size option", 'mwe_si_magnifier'); ?> <strong class="sim_highlight">*</strong></li>
            <li><?php esc_html_e("auto rendering", 'mwe_si_magnifier'); ?> <strong class="sim_highlight">**</strong></li>
            <li><?php esc_html_e("lazyloading, transitions and flex-boxes", 'mwe_si_magnifier'); ?></li>
            <li><?php esc_html_e("usage control", 'mwe_si_magnifier'); ?> <strong class="sim_highlight">***</strong></li>
            <li><?php esc_html_e("custom preview image", 'mwe_si_magnifier'); ?></li>
        </ul>
        <table id="sim_desc">
            <tr><td class="sim_highlight">*</td><td><?php esc_html_e("When this option is enabled, the original , uploaded image is used as the magnified image's drawing source. This results in greater clarity of detail in the magnified portion of the image especially when smaller custom image sizes are in use.", 'mwe_si_magnifier'); ?></td></tr> 
            <tr><td class="sim_highlight">**</td><td><?php esc_html_e("Sometimes when you visit a page the mouse ends up already positioned over an image.  In such cases, if a lens has been configured, it will be automatically rendered as soon as the mouse is moved.", "mwe_si_magnifier"); ?></td></tr>
            <tr><td class="sim_highlight">***</td><td><?php esc_html_e("Assign SI Magnfifier editors and users based on capability levels.", "mwe_si_magnifier"); ?></td></tr>
        </table>
        <p class="sim_bigger">
            <?php esc_html_e("You can see screenshots and demos and purchase via my site.", 'mwe_si_magnifier'); ?>
            <a class="sim_link" href='https://markweastwood.co.uk/si-magnifier' target='_blank'>
                <?php  esc_html_e('SI Magnifier Premium', 'mwe_si_magnifier'); ?>
            </a>
        </p>
        
    </section>
    <br />
    <h2><?php esc_html_e("Premium not for you?", 'mwe_si_magnifier'); ?></h2>
    <section id="sim_donate">

        <p><?php esc_html_e("If you like SI Magnifier free please consider making a small donation by visting my site.", 'mwe_si_magnifier'); ?>&nbsp;
        <a class="sim_link" href="https://markweastwood.co.uk/donate-to-si-magnifier" target="_blank" style="font-size:110%;"><?php esc_html_e("Make a donation to", "mwe_si_magnifier"); ?> SI Magnifier</a>&nbsp;
        </em><?php esc_html_e("Any amount would be greatly appreciated. Thanks.", 'mwe_si_magnifier'); ?></em><br />
        <br />
        </p>
    </section>
    
</div>
