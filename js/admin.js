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
( function ( $ ) {
    
    $(document).ready( function() {

        if ( document.getElementById('sim_configure') ) {
            
            $('#sim_previewtoggle').click( function(e) {
                e.preventDefault();
                var n, nsrc, ncur, current = $( this ).attr('data-current');
                n = new Image();
                nsrc = current === 'small' ? 'data-src-large' : 'data-src-small';
                ncur = current === 'small' ? 'large' : 'small';
                n.src = $( this ).attr( nsrc );
                $( this ).attr('data-current', ncur );
                $('#sim_previewimage').attr('src', n.src);
            });

            
            $( "#dialog-message" ).dialog({
                autoOpen:false,
                dialogClass: "mwe_noclose",
                modal: true,
                width: 420,
                buttons: {
                    Ok: function() { $( this ).dialog( "close" ); }
                }
            });    

            $('#mwe_help').click(function(e) {
                e.preventDefault();
                $( "#dialog-message" ).dialog( "open" );
            });

            var img = document.getElementById('sim_previewimage'),
            img_cursor = $('input[name="cur"]:checked')[0].value;
            img.setAttribute('class', img_cursor);

            if ( typeof simdata === 'undefined' || 
                    simdata === 'null' ||
                        simdata.length < 1 ) {
                alert("Sorry - No lens data.\n\nDeactivating/Reactivating plugin may fix this problem.");
                return true;
            }

            if ( ! simdata.hasOwnProperty('lensdata') ||
                    ! simdata.hasOwnProperty('datakey') ) {
               console.log('lens-cpt.js: Missing simdata property - lensdata or datakey');
               return true;
            }

            // whenever a confguration value is changed update it's 
            // corresponding data-* attribute for the preview image
            $('.sim_input').change(function(){ 
                updateData(img, this);
            });

            // stop enter key down from updating/saving the post
            // instead, if a form element call updatedata()
            $('.no_enter_key').keypress(
                function(e) {
                    var x = e.which || e.keyCode;
                    if (x == 13 ) {
                        e.preventDefault();
                        updateData( img, e.target );
                    }
                }
            );
        
            // reflect changes
            function updateData(img, element)
            {
                 // add constraints
                switch( element.name ) {
                    case 'hgt':
                    case 'wdh': if ( parseInt( element.value ) > 150 ) { element.value = "150"; }
                        break;
                    case 'crd':if ( parseInt( element.value ) > 50 ) { element.value = "50"; }
                        break;
                }

                // update
                simdata.lensdata[ simdata.datakey ][ element.name ] = element.value;
            }
            
        }
    });
    
})(jQuery);