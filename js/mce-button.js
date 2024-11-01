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
( function() {
    var theImg, selected, lenses=[], idx, form, selection,
    nosel = "Please select an image.", conf="CONFIRMATION: Remove lens?", lbl="Select a Lens",
    invalidlensa = "This image contains an invalid lens.", $ = tinymce.dom.DomQuery, 
    buttonstyle, invalid, map;

    if ( typeof simstrings != 'object' 
            || ! simstrings.hasOwnProperty( 'map' ) ) { 
        console.log("SIM - No CDATA simstrings");
        return true;
    }

    $.each( simstrings.map, function( p, v ) { lenses.push( v ); } );
    if ( simstrings.hasOwnProperty('nosel') ) { nosel = simstrings.nosel; }
    if ( simstrings.hasOwnProperty('conf') ) { conf = simstrings.conf; }
    if ( simstrings.hasOwnProperty('lbl') ) { lbl = simstrings.lbl; }
    if ( simstrings.hasOwnProperty('invalidlensa') ) { invalidlensa = simstrings.invalidlensa; }

        
    tinymce.PluginManager.add(
        'sim_magnifier_button', function(editor, url)
        { 
            editor.addButton(
                'sim_magnifier_button', {
                    tooltip: 'SI Magnifier',
                    icon: 'dashicon dashicons-search',
                    image: tinymce.documentBaseURL + '../wp-content/plugins/si-magnifier/images/maglink20.png',
                    cmd: 'hover_magnifier'
                } );

            // Add Command when Button Clicked
            editor.addCommand(
                'hover_magnifier', function()
                {
                    // Has an image been selected?
                    theImg = editor.selection.getNode();
                    if ( theImg.nodeName != 'IMG' ) {
                        alert( nosel );
                        return true;
                    }
                    
                    // reset vars that are/maybe cached
                    selected = 'None';
                    form = null;
                    invalid = false;
                    
                    // save this images current lens if one is present
                    if ( theImg.getAttribute( 'data-sim-id' ) != null ) {
                        invalid = true;
                        var id = theImg.getAttribute( 'data-sim-id' );
                        $.each( simstrings.map, function( p, v ) {
                            if ( p == id ) { 
                                selected = v; 
                                invalid = false;
                            }
                        });
                    }
                   
                    // Open window with a form containing lens select drop down 
                    editor.windowManager.open( {
                        title: 'SI Magnifier',
                        id: 'sim_dialog',
                        body: getFields(),
                        minWidth: 300,
                        style: "padding:10px;",
                        onsubmit: function(e) {
                            selection = document.getElementById('lensSelect').value;
                            if ( invalid && selection == 'None' ) {
                                theImg.removeAttribute( 'data-sim-id' );
                                return true;
                            }
                            if ( selected != 'None' && selection == 'None') {
                                if ( confirm(conf + ' - ' + selected) ) {
                                        theImg.removeAttribute( 'data-sim-id' );
                                        return true;
                                }
                            } 
                            if ( selection != 'None' ) {
                                $.each( simstrings.map, function( p, v ) {
                                    if ( v == selection ) { 
                                        theImg.setAttribute( 'data-sim-id', p );
                                    }
                                });
                                return true;
                            } 
                        }
                    } );                
                    // set modal background to something a bit lighter
                    document.getElementById('mce-modal-block').style.opacity = ".45";
                    
                    // Set the select box option to the current value and move
                    // the ok button
                    document.getElementById('lensSelect').value = selected;
                    buttonstyle = $(".mce-window .mce-btn.mce-primary").attr('style');
                    $(".mce-window .mce-btn.mce-primary").attr('style', buttonstyle + 'left:10px;');
                    
                    // Add window form and it's contents
                    function getFields() {
                        // create empty mce form
                        form = tinymce.ui.Factory.create( {type: 'form', items: []} );
                        // invalid lens id
                        if ( invalid ) {
                            form.add( {type:'label', text: invalidlensa} );                                    
                            form.add( {type: 'spacer'});
                        }
                        form.add( {type:'label', text: lbl} );
                        form.add( {type:'selectbox', id:'lensSelect', name:'lensSelect', 
                            classes:"sim_selectbox", options: ['None'].concat(lenses)} );
                        form.add( {type:'spacer'} );
                        return form; 
                    }

                } );               
        } );
})();

// Taken from core plugins
var editor = tinymce.activeEditor;
