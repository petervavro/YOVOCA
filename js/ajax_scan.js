
/* ARRAY WITH SENTENCES DATA */
var dataArray = new Array();

/* REMAINING SENTENCES */
var waitingSentences;

var defaultButtonValue;
var clickCounter = 0;

$(document).click( function() {
  if(window.clickCounter++ == 2){
    window.clickCounter = 0;
  } 
} );

function loadNextSentence(vSentenceID)
{

  if ($('#sNext').length) {
    // http://kammerer.boo.pl/code/jquery-busy/
    $('#sNext').click(function() { $(this).busy({ img : 'http://www.yovoca.com/images/busy.gif'}); });
  }

  window.jqxhr = $.ajax({url: "/ajax/get_sentence/"+window.movieID+"/",type: "POST",data: ({lng:document.changelangform.userlang.options[document.changelangform.userlang.selectedIndex].value,WordsData:reduceItemDuplicities(true)})}).success(function(dataOUT, textStatus,jqxhr){
    
    if(jqxhr.getResponseHeader("Content-Type") == "text/html"){

      console.log(dataOUT);
      $(window.location).attr('href', dataOUT);
      document.getElementById("contentArea").innerHTML='<a href="'+dataOUT+'" title="Redirect">GO TO LIST OF UNKNOWN WORDS</a>';
      // document.getElementById("contentArea").innerHTML=dataOUT;

    } else {
      // console.log('dataZAJAXU',dataOUT);
      load_aWords(dataOUT);
      showTranslation();
      $('#sentence').busy("hide");
    }

  }).error(function(jqXHR, textStatus, errorThrown) {alert("We are so sorry but you encounter some problem on our page ! Please refresh page ! AJAX error:"+textStatus);})
}

function load_aWords(xmlNode)
{
	window.dataArray.length = 0;
	
	if(!window.defaultButtonValue) {
		window.defaultButtonValue = document.getElementById('sNext').value;
	}

	var aLenght;
	var SentenceNode=xmlNode.documentElement.childNodes[1];
	var sentenceID;


	$(xmlNode).find("sentences").each(function()
	{
	  var sentenceNum = 0;
	  $(this).find("sentence").each(function()
	  {
	    var wordNum = 0;
	    var dataWords = new Array();
	    var sentencesData = new Array();
	    sentencesData['ID']=$(this).attr("id");
	    window.waitingSentences=$(this).attr("waitingsentences");
	
	    $(this).find("word").each(function() {
	        var EachWord = new Array();
	        EachWord["ID"]=$(this).find("id").text();
	        EachWord["Word"]=$(this).find("wordtext").text();
	        EachWord["Unknown"]=$(this).find("unknown").text();
	        EachWord["sID"]=sentencesData['ID'];

	        if($(this).find("gtranslationlng").text()){
	          EachWord["gTranslation"]=$(this).find("gtranslation").text();
	          EachWord["gTranslationLNG"]=$(this).find("gtranslationlng").text();
	        }
	
	        dataWords[wordNum++]=EachWord;
	    });
	
	    sentencesData['SENTENCE']=dataWords;
	    window.dataArray[sentenceNum++]=sentencesData;
	    
	  });
	});
	showTranslation();
	renderSentence();
}


function renderSentence()
{
   var showTEXT="";

    $.each(window.dataArray, function(indexS, valueS) {
        var sentenceID = valueS['ID'];

        $.each(valueS['SENTENCE'], function(indexW, valueW) {
          showTEXT=showTEXT+"<span ";
          if(valueW["ID"] != "N"){
            if(valueW["Unknown"]=="1"){
              showTEXT=showTEXT+"class=\"selectedWord\"";
            }
            showTEXT=showTEXT+" onclick=\"selectWord("+valueW["ID"]+");\">"+valueW["Word"];
          }
          else
          {
            showTEXT=showTEXT+">"+valueW["Word"];
          }
          showTEXT=showTEXT+"</span> "; // &nbsp;
        });
        showTEXT=showTEXT+"<br />";
    });
    document.getElementById("sentence").innerHTML=showTEXT;
    document.getElementById('sNext').setAttribute('onclick', 'loadNextSentence();');
    $('#sNext').busy("hide");
    $('#nwS').html(window.waitingSentences);
}

function deleteTranslation(wID){
  $.each(window.dataArray, function(indexS, valueS) {
    $.each(valueS['SENTENCE'], function(indexW, valueW) {
      if(valueW["ID"] == wID)
      {
        if(valueW["Unknown"] == 1){
          valueW["gTranslation"] = null;
          valueW["gTranslationLNG"] = null;
        }
      }
    });
  });
  getTranslation(wID);
}

function selectWord(wID){
  var isUnknown=new Boolean(false);
  if(window.movielang != document.changelangform.userlang.options[document.changelangform.userlang.selectedIndex].value){
    if(wID != "N"){

      $.each(window.dataArray, function(indexS, valueS) {
          // var sentenceID = valueS['ID'];
          $.each(valueS['SENTENCE'], function(indexW, valueW) {
            if(valueW["ID"] == wID)
            {
              valueW["Unknown"] = ( valueW["Unknown"] == 0 ) ? 1 : 0;
              if(valueW["Unknown"] == 1){
                isUnknown = true;
              }
            }
          });
      });
      if(isUnknown == true){
        getTranslation(wID);      
      }
      showTranslation();
      renderSentence();
    }
  }
  else
  {
    alert("Please set target translation language! Target translation language must be different from subtitles language!");
  }
}

// Google translate implementation

google.load("language", "1");

function getTranslation(wID) {

	// Checking whether lng code is set
	if(window.movielang != document.changelangform.userlang.options[document.changelangform.userlang.selectedIndex].value) {

		var gTranslation;
		var gTranslationLNG;
		var tWord;
	
		// getting word(TEXT)
		/* Check if word is already saved on server , if yes get translation from there. */ 
		$.each(window.dataArray, function(indexS, valueS) {
	  	
			$.each(valueS['SENTENCE'], function(indexW, valueW) {
				
				if(valueW["ID"] == wID) {
					
					// save word(text) to variable
					tWord = valueW["Word"];
					
					// word translation is already saved in database on server == 1 / get translation to var  
					if(valueW["Unknown"] == 1){
						  
						if((valueW["gTranslation"] != undefined) && (valueW["gTranslationLNG"] != undefined)) {
	
							gTranslation = valueW["gTranslation"];
							gTranslationLNG = valueW["gTranslationLNG"];
	              			return false;
	            		}
	          		}
	        	}
	    	});

			/* if translation found exit */
			if((gTranslation != undefined) && (gTranslationLNG != undefined)){
				return false;
	    	}
		});

		if(tWord != undefined) {
	
			/* Get translation from google translate */
			if((gTranslation === undefined) || (gTranslationLNG === undefined)){
	
				google.language.translate(tWord, window.movielang, document.changelangform.userlang.options[document.changelangform.userlang.selectedIndex].value, function(result) {
	
					if (!result.error) {
	
						if(result.translation != tWord){
							
							$.each(window.dataArray, function(indexSi, valueSi) {
								$.each(valueSi['SENTENCE'], function(indexWi, valueWi) {
	
									if(valueWi["ID"] == wID) {
			
										if(valueWi["Unknown"] == 1){
											valueWi["gTranslation"] = result.translation;
											valueWi["gTranslationLNG"] = document.changelangform.userlang.options[document.changelangform.userlang.selectedIndex].value;                                  
										}
									}
								});
							});
	
							showTranslation();
							renderSentence();
						}
	
					} else {

						console.log(result.error);
						// Error : 403 : Please use Translate v2.  See http://code.google.com/apis/language/translate/overview.htm - PAID Service
						// getTranslation(wID);
					}

				}); 
			}
	
		} else {
	
			/* Write translation for all the same words in the array. */
			if((gTranslation != undefined) && (gTranslationLNG != undefined)) {
	
				// insertTranslation(wID, gTranslation, gTranslationLNG);
	
				$.each(window.dataArray, function(indexSi, valueSi) {
	
					$.each(valueSi['SENTENCE'], function(indexWi, valueWi){
	
						if(valueWi["ID"] == wID) {
	
							if(valueWi["Unknown"] == 1){
								valueWi["gTranslation"] = gTranslation;
								valueWi["gTranslationLNG"] = gTranslationLNG;
							}
						}
					});
				});
	
			} else {
	
				alert('Please choose a right language for translation!');
				return false;
			}
	    }
	}
}

/* Write trnaslation into array for all the same words */
function insertTranslation(wID, gTranslation, gTranslationLNG) {

	$.each(window.dataArray, function(indexSi, valueSi) {

		$.each(valueSi['SENTENCE'], function(indexWi, valueWi){

			if(valueWi["ID"] == wID) {

				if(valueWi["Unknown"] == 1){
					valueWi["gTranslation"] = gTranslation;
					valueWi["gTranslationLNG"] = gTranslationLNG;
				}
			}
		});
	});
}

/* - TODO
function saveUserTranslation(wID){
  $.each(window.dataArray, function(indexS, valueS) {
      $.each(valueS['SENTENCE'], function(indexW, valueW) {
          if(valueW["ID"] == wID){
            
          }
      });
  });
}
*/

  function cleanTranslation(){
    var Parent = document.getElementById('tblTranslation');
    while(Parent.hasChildNodes())
    {
       Parent.removeChild(Parent.firstChild);
    }
  }
	/* RENDER TRANSLATIONS */
	function showTranslation(){

		var reducedArray=reduceItemDuplicities();
	    var tblTranslation = document.getElementById('tblTranslation');
	    cleanTranslation();
	    var thead = tblTranslation.appendChild(document.createElement('thead'));
	    var tbody = tblTranslation.appendChild(document.createElement('tbody'));
	    var tr = thead.appendChild(document.createElement('tr'));
	    var th1 = tr.appendChild(document.createElement('th'));
	    th1.appendChild(document.createTextNode(get_language_name(window.movielang)));
	    var th2 = tr.appendChild(document.createElement('th'));
	    var gbranding = document.createElement('span');

	    gbranding.setAttribute('class', 'gbranding');
	    gbranding.appendChild(document.createTextNode(""));
	    th2.appendChild(document.createTextNode(get_language_name(document.changelangform.userlang.options[document.changelangform.userlang.selectedIndex].value)));
	    th2.appendChild(google.language.getBranding());
	    
	    $.each(reducedArray, function(indexW, valueW) {

	        var approved = true;

	        if(valueW["Unknown"] == 1){

				var myRow = tbody.insertRow(0);

				var myCellb = myRow.insertCell(0);

				if(valueW["gTranslation"] !== undefined){
					myCellb.innerHTML = valueW["gTranslation"];
				} else {
					myCellb.innerHTML = 'Translation from Google is not provided free of charge anymore. Therefore you need to translate words by your self at the end.';
				}
				
				myCellb.setAttribute('onclick', 'deleteTranslation('+valueW['ID']+');');
				
				var myCella = myRow.insertCell(0);
				myCella.innerHTML = valueW["Word"];
			}
	        
	    });
    	// google.language.getBranding('gbranding');
	}

/* REDUCE DUPLICATED ROWS / WORDS IN TRANSLATIONS */
function reduceItemDuplicities(useNumberKeys){

  var newReducedArray=new Array(); //  = window.dataArray;
  $.each(window.dataArray, function(indexS, valueS) {
      $.each(valueS['SENTENCE'], function(indexW, valueW) {
        var approved=new Boolean(true);
        $.each(newReducedArray, function(indexSWr, valueSWr) {
          if(valueSWr["ID"]==valueW['ID'] || valueW['ID']=="N"){
              approved = false;
              return false;
          }
        });
        if(approved == true){
            newReducedArray[newReducedArray.length] = valueW;
        }
      });
  });

  if(useNumberKeys==true){
    var newReducedArrayNumKeys=new Array();
    $.each(newReducedArray, function(index, value){
      if((value["Unknown"] ==1 ) && (value["gTranslation"] !== undefined )){
        newReducedArrayNumKeys[index]=[value["ID"],value["Word"],value["Unknown"],value["sID"],value["gTranslation"],value["gTranslationLNG"]];
      }
      else
      {
        newReducedArrayNumKeys[index]=[value["ID"],value["Word"],value["Unknown"],value["sID"]];
      }
    });
    newReducedArray=newReducedArrayNumKeys;
  }

  return newReducedArray;
}

  function get_language_name(langkey){

    var languageName;
    var languages = {
      'AFRIKAANS' : 'af',
      'ALBANIAN' : 'sq',
      'AMHARIC' : 'am',
      'ARABIC' : 'ar',
      'ARMENIAN' : 'hy',
      'AZERBAIJANI' : 'az',
      'BASQUE' : 'eu',
      'BELARUSIAN' : 'be',
      'BENGALI' : 'bn',
      'BIHARI' : 'bh',
      'BRETON' : 'br',
      'BULGARIAN' : 'bg',
      'BURMESE' : 'my',
      'CATALAN' : 'ca',
      'CHEROKEE' : 'chr',
      'CHINESE' : 'zh',
      'CHINESE_SIMPLIFIED' : 'zh-CN',
      'CHINESE_TRADITIONAL' : 'zh-TW',
      'CORSICAN' : 'co',
      'CROATIAN' : 'hr',
      'CZECH' : 'cs',
      'DANISH' : 'da',
      'DHIVEHI' : 'dv',
      'DUTCH': 'nl',
      'ENGLISH' : 'en',
      'ESPERANTO' : 'eo',
      'ESTONIAN' : 'et',
      'FAROESE' : 'fo',
      'FILIPINO' : 'tl',
      'FINNISH' : 'fi',
      'FRENCH' : 'fr',
      'FRISIAN' : 'fy',
      'GALICIAN' : 'gl',
      'GEORGIAN' : 'ka',
      'GERMAN' : 'de',
      'GREEK' : 'el',
      'GUJARATI' : 'gu',
      'HAITIAN_CREOLE' : 'ht',
      'HEBREW' : 'iw',
      'HINDI' : 'hi',
      'HUNGARIAN' : 'hu',
      'ICELANDIC' : 'is',
      'INDONESIAN' : 'id',
      'INUKTITUT' : 'iu',
      'IRISH' : 'ga',
      'ITALIAN' : 'it',
      'JAPANESE' : 'ja',
      'JAVANESE' : 'jw',
      'KANNADA' : 'kn',
      'KAZAKH' : 'kk',
      'KHMER' : 'km',
      'KOREAN' : 'ko',
      'KURDISH': 'ku',
      'KYRGYZ': 'ky',
      'LAO' : 'lo',
      'LATIN' : 'la',
      'LATVIAN' : 'lv',
      'LITHUANIAN' : 'lt',
      'LUXEMBOURGISH' : 'lb',
      'MACEDONIAN' : 'mk',
      'MALAY' : 'ms',
      'MALAYALAM' : 'ml',
      'MALTESE' : 'mt',
      'MAORI' : 'mi',
      'MARATHI' : 'mr',
      'MONGOLIAN' : 'mn',
      'NEPALI' : 'ne',
      'NORWEGIAN' : 'no',
      'OCCITAN' : 'oc',
      'ORIYA' : 'or',
      'PASHTO' : 'ps',
      'PERSIAN' : 'fa',
      'POLISH' : 'pl',
      'PORTUGUESE' : 'pt',
      'PORTUGUESE_PORTUGAL' : 'pt-PT',
      'PUNJABI' : 'pa',
      'QUECHUA' : 'qu',
      'ROMANIAN' : 'ro',
      'RUSSIAN' : 'ru',
      'SANSKRIT' : 'sa',
      'SCOTS_GAELIC' : 'gd',
      'SERBIAN' : 'sr',
      'SINDHI' : 'sd',
      'SINHALESE' : 'si',
      'SLOVAK' : 'sk',
      'SLOVENIAN' : 'sl',
      'SPANISH' : 'es',
      'SUNDANESE' : 'su',
      'SWAHILI' : 'sw',
      'SWEDISH' : 'sv',
      'SYRIAC' : 'syr',
      'TAJIK' : 'tg',
      'TAMIL' : 'ta',
      'TATAR' : 'tt',
      'TELUGU' : 'te',
      'THAI' : 'th',
      'TIBETAN' : 'bo',
      'TONGA' : 'to',
      'TURKISH' : 'tr',
      'UKRAINIAN' : 'uk',
      'URDU' : 'ur',
      'UZBEK' : 'uz',
      'UIGHUR' : 'ug',
      'VIETNAMESE' : 'vi',
      'WELSH' : 'cy',
      'YIDDISH' : 'yi',
      'YORUBA' : 'yo',
      'UNKNOWN' : ''
    };

    $.each(languages, function(index, value) {
      if(langkey==value){
        languageName = index;
        return this;
      }
    });
    return languageName;
  }

/*
 * jQuery-busy v1.0.4
 * Copyright 2010 Tomasz Szymczyszyn
 *
 * Examples available at:
 * http://kammerer.boo.pl/code/jquery-busy
 *
 * This plug-in is dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function($) {
  // Helper object factory 
  function Busy(options) {
    this.options = $.extend({}, Busy.defaults, options);
  };

  // Remembers currently "busied" targets along with options
  Busy.instances = [];

  Busy.repositionAll = function() {
    for (var i = 0; i < Busy.instances.length; i++) {
      if (! Busy.instances[i])
        continue;

      var options = Busy.instances[i].options;
      new Busy(options).positionImg($(Busy.instances[i].target), $.data(Busy.instances[i].target, "busy"), options.position);
    }
  };

  Busy.prototype.hide = function(targets) {
    targets.each(function() {
      var busyImg = $.data(this, "busy");
      if (busyImg)
        busyImg.remove();

      $(this).css("visibility", "");

      $.data(this, "busy", null);
      for (var i = 0; i < Busy.instances.length; i++)
        if (Busy.instances[i] != null && Busy.instances[i].target == this)
          Busy.instances[i] = null;
    });
  };

  Busy.prototype.show = function(targets) {
    var that = this;

    targets.each(function() {
      if ($.data(this, "busy"))
        return;

      var target = $(this);

      var busyImg = that.buildImg();
      busyImg.css("visibility", "hidden");
      busyImg.load(function() { 
        that.positionImg(target, busyImg, that.options.position);
        busyImg.css("visibility", "");
        busyImg.css("zIndex", that.options.zIndex);
      });

      $("body").append(busyImg);

      if (that.options.hide)
        target.css("visibility", "hidden");

      $.data(this, "busy", busyImg);
      Busy.instances.push({ target : this, options : that.options });
    });
  };

  Busy.prototype.preload = function() {
    var busyImg = this.buildImg();
    busyImg.css("visibility", "hidden");
      busyImg.load(function() {
        $(this).remove();
      });

      $("body").append(busyImg);
  };

  // Creates image node, wraps it in $ object and returns.
  Busy.prototype.buildImg = function() {
    var html = "<img src='" + this.options.img + "' alt='" + this.options.alt + "' title='" + this.options.title + "'";

    if (this.options.width)
      html += " width='" + this.options.width + "'";
   
    if (this.options.height)
      html += " height='" + this.options.height + "'";

    html += " />";

    return $(html);
  };

  Busy.prototype.positionImg = function(target, busyImg, position) {
    var targetPosition = target.offset();
    var targetWidth = target.outerWidth();
    var targetHeight = target.outerHeight();

    var busyWidth = busyImg.outerWidth();
    var busyHeight = busyImg.outerHeight();

    if (position == "left") {
      var busyLeft = targetPosition.left - busyWidth - this.options.offset;
    }
    else if (position == "right") {
      var busyLeft = targetPosition.left + targetWidth + this.options.offset;
    }
    else {
      var busyLeft = targetPosition.left + (targetWidth - busyWidth) / 2.0;
    }

    var busyTop = targetPosition.top + (targetHeight - busyHeight) / 2.0;

    busyImg.css("position", "absolute");
    busyImg.css("left", busyLeft + "px");
    busyImg.css("top", busyTop + "px");
  };

  Busy.defaults = {
    img : 'busy.gif',
    alt : 'Please wait...',
    title : 'Please wait...',
    hide : true,
    position : 'center',
    zIndex : 1001,
    width : null,
    height : null,
    offset : 10
  };

  $.fn.busy = function(options, defaults) {
    if ($.inArray(options, ["clear", "hide", "remove"]) != -1) {
      // Hide busy image(s)
      new Busy(options).hide($(this));     
    }
    else if (options == "defaults") {
      // Overwrite defaults
      $.extend(Busy.defaults, defaults || {});
    }
    else if (options == "preload") {
      // Preload busy image
      new Busy(options).preload();
    }
    else if (options == "reposition") {
      // Update positions of all existing busy images
      Busy.repositionAll();
    }
    else {
      // Show busy image(s)
      new Busy(options).show($(this));
      return $(this);
    }
  };
})(jQuery);

