(function(){var h,c,e=[],l,b,k,i="Please select an image.",f="CONFIRMATION: Remove lens?",j="Select a Lens",m="This image contains an invalid lens.",d=tinymce.dom.DomQuery,n,g,a;if(typeof simstrings!="object"||!simstrings.hasOwnProperty("map")){console.log("SIM - No CDATA simstrings");return true}d.each(simstrings.map,function(q,o){e.push(o)});if(simstrings.hasOwnProperty("nosel")){i=simstrings.nosel}if(simstrings.hasOwnProperty("conf")){f=simstrings.conf}if(simstrings.hasOwnProperty("lbl")){j=simstrings.lbl}if(simstrings.hasOwnProperty("invalidlensa")){m=simstrings.invalidlensa}tinymce.PluginManager.add("sim_magnifier_button",function(p,o){p.addButton("sim_magnifier_button",{tooltip:"SI Magnifier",icon:"dashicon dashicons-search",image:tinymce.documentBaseURL+"../wp-content/plugins/si-magnifier/images/maglink20.png",cmd:"hover_magnifier"});p.addCommand("hover_magnifier",function(){h=p.selection.getNode();if(h.nodeName!="IMG"){alert(i);return true}c="None";b=null;g=false;if(h.getAttribute("data-sim-id")!=null){g=true;var r=h.getAttribute("data-sim-id");d.each(simstrings.map,function(t,s){if(t==r){c=s;g=false}})}p.windowManager.open({title:"SI Magnifier",id:"sim_dialog",body:q(),minWidth:300,style:"padding:10px;",onsubmit:function(s){k=document.getElementById("lensSelect").value;if(g&&k=="None"){h.removeAttribute("data-sim-id");return true}if(c!="None"&&k=="None"){if(confirm(f+" - "+c)){h.removeAttribute("data-sim-id");return true}}if(k!="None"){d.each(simstrings.map,function(u,t){if(t==k){h.setAttribute("data-sim-id",u)}});return true}}});document.getElementById("mce-modal-block").style.opacity=".45";document.getElementById("lensSelect").value=c;n=d(".mce-window .mce-btn.mce-primary").attr("style");d(".mce-window .mce-btn.mce-primary").attr("style",n+"left:10px;");function q(){b=tinymce.ui.Factory.create({type:"form",items:[]});if(g){b.add({type:"label",text:m});b.add({type:"spacer"})}b.add({type:"label",text:j});b.add({type:"selectbox",id:"lensSelect",name:"lensSelect",classes:"sim_selectbox",options:["None"].concat(e)});b.add({type:"spacer"});return b}})})})();var editor=tinymce.activeEditor;