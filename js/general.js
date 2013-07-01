$(document).ready(function() {

	/* Google Analytics */ 
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-13090749-1']);
	_gaq.push(['_setDomainName', '.yovoca.com']);
	_gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
  
  $('#uploadBTN').click(function() {
      $.blockUI( { message: $('#domMessage') });
  });

  $(document).keyup(function(event){
    if(event.keyCode == 13){
      $("#sNext").click();
    }
  });

	/* LOAD SlideShow */
	if ($('.slideshow').length > 0){
		$('.slideshow').cycle({
			fx: 'fade',
			timeout: 6000,
			random: 1
		});
	}

	/* LOAD Accordion */
	if ($('#accordion').length > 0){
		$("#accordion").accordion({ autoHeight: false, active: false});
	}	
	
});

/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];

			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

  function xml_to_string(xml_node)
  {
      if (xml_node.xml)
          return xml_node.xml;
      else if (XMLSerializer)
      {
          var xml_serializer = new XMLSerializer();
          return xml_serializer.serializeToString(xml_node);
      }
      else
      {
        alert("ERROR: Extremely old browser");
      	return "";
      }
  }


function getIDattrOf(xmlNode,searchEName)
{
   if(xmlNode.hasChildNodes()) {
    for(var i=0; i<xmlNode.childNodes.length; i++){
      if((xmlNode.childNodes(i).nodeType==1)&&(xmlNode.childNodes(i).tagName==searchEName)){
        return xmlNode=xmlNode.childNodes(i).getAttribute("id");
      }
       foundID=getIDattrOf(xmlNode.childNodes(i),searchEName);
       if(foundID!=null){
         return foundID;
       }
    }
  }
}