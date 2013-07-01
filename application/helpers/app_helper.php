<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function gen_navi_help($num, $label, $settings = NULL) {
    unset($tempstr);
    if (isset($settings['imgOrientation'])) {
        if ($settings['imgOrientation'] == 'r') { // right
            $arrowIMG = array(
                'src' => 'images/arrowRIGHT.png',
                'title' => $label,
            );
        }
    } else {
        $arrowIMG = array(
            'src' => 'images/arrowDOWN.png',
            'title' => $label,
        );
    }

    $tempstr = '<div';
    if (isset($settings['divStyle'])) {
        $tempstr .= ' style="' . $settings['divStyle'] . '"';
    }

    $tempstr .= ' class="navihelp"><table><tr><td class="imgNavi">' . img($arrowIMG) . '</td><td class="numNavi">' . $num . '</td><td class="labelNavi">';
    if (!is_array($label)) {
        unset($labeltemp);
        $labeltemp[] = $label;
        unset($label);
        $label = $labeltemp;
    }
    $tempstr .= ul($label);
    $tempstr .= '</td></tr></table></div>';

    return $tempstr;
}


function load_article($article) {

    unset($resulttemp);

    $ci = &get_instance();

    if (is_array($article)) {

        if (array_key_exists_recursive('accordion', $article)) {

            $resulttemp[] = '<div id="accordion">';

            foreach ($article['accordion'] as $accordion_article) {

                $accordion_article = array_merge($accordion_article, $ci->App_model->getArticle((is_array($accordion_article)) ? $accordion_article['identifier'] : $accordion_article));
                $accordion_article['nowrapper'] = true;
                $resulttemp[] = $ci->load->view('text_view', array('Article' => $accordion_article), true);
            }

            $resulttemp[] = '</div>';

        } elseif (array_key_exists_recursive('slideshow', $article)) {

            // http://jquery.malsup.com/cycle/
            $resulttemp[] = '<div class="slideshow">';

            foreach ($article['slideshow'] as $accordion_article) {

                $accordion_article = array_merge($accordion_article, $ci->App_model->getArticle((is_array($accordion_article)) ? $accordion_article['identifier'] : $accordion_article));
                $resulttemp[] = $ci->load->view('text_view', array('Article' => $accordion_article), true);
            }

            $resulttemp[] = '</div>';

        } else {

            if (isset($article['identifier'])) {

                switch ($article['identifier']) {
                    case 'translate':

                        unset($layoutdata, $content);

                        $layoutdata['divclass'] = 'blueone';
                        $layoutdata['Title'] = $ci->lang->line('index_translation_title');

                        $ci->table->clear();
                        $tmpl = array('table_open' => '<table id="tblTranslation">');
                        $ci->table->set_template($tmpl);
                        $ci->table->set_heading(null, null);

                        $layoutdata['ArticleText'] = $ci->table->generate();

                        $article = $layoutdata;

                        break;
                    default:

                        $article = array_merge($article, $ci->App_model->getArticle($article['identifier']));
                }
            }

            $resulttemp[] = $ci->load->view('text_view', array('Article' => $article), true);
        }
    } else {

        $article = $ci->App_model->getArticle($article);

        $resulttemp[] = $ci->load->view('text_view', array('Article' => $article), true);
    }

    return (isset($resulttemp)) ? implode('', $resulttemp) : null;
}

function convert_words_array_keys($arrayInput) {
    if (is_array($arrayInput)) {
        foreach ($arrayInput as $key => $value) {
            $arrayOutput[$key]["ID"] = (int) $value[0];
            $arrayOutput[$key]["Word"] = $value[1];
            $arrayOutput[$key]["Unknown"] = $value[2];
            $arrayOutput[$key]["sID"] = (int) $value[3];
            // if(isset($value[4]) && ($value[2] == 1)){
            if (isset($value[4])) {
                $arrayOutput[$key]["gTranslation"] = $value[4];
                $arrayOutput[$key]["gTranslationLNG"] = $value[5];
            }
        }
        return $arrayOutput;
    }
}

function get_language_name($keyLang) {

    $languages = load_languages_array();

    foreach ($languages as $key => $value) {
        if ($keyLang == $key) {
            return $value;
        }
    }
    return NULL;
}

function load_languages_array() {

    return array(
        'af' => 'AFRIKAANS',
        'sq' => 'ALBANIAN',
        'am' => 'AMHARIC',
        'ar' => 'ARABIC',
        'hy' => 'ARMENIAN',
        'az' => 'AZERBAIJANI',
        'eu' => 'BASQUE',
        'be' => 'BELARUSIAN',
        'bn' => 'BENGALI',
        'bh' => 'BIHARI',
        'br' => 'BRETON',
        'bg' => 'BULGARIAN',
        'my' => 'BURMESE',
        'ca' => 'CATALAN',
        'chr' => 'CHEROKEE',
        'zh' => 'CHINESE',
        'zh-CN' => 'CHINESE_SIMPLIFIED',
        'zh-TW' => 'CHINESE_TRADITIONAL',
        'co' => 'CORSICAN',
        'hr' => 'CROATIAN',
        'cs' => 'CZECH',
        'da' => 'DANISH',
        'dv' => 'DHIVEHI',
        'nl' => 'DUTCH',
        'en' => 'ENGLISH',
        'eo' => 'ESPERANTO',
        'et' => 'ESTONIAN',
        'fo' => 'FAROESE',
        'tl' => 'FILIPINO',
        'fi' => 'FINNISH',
        'fr' => 'FRENCH',
        'fy' => 'FRISIAN',
        'gl' => 'GALICIAN',
        'ka' => 'GEORGIAN',
        'de' => 'GERMAN',
        'el' => 'GREEK',
        'gu' => 'GUJARATI',
        'ht' => 'HAITIAN_CREOLE',
        'iw' => 'HEBREW',
        'hi' => 'HINDI',
        'hu' => 'HUNGARIAN',
        'is' => 'ICELANDIC',
        'id' => 'INDONESIAN',
        'iu' => 'INUKTITUT',
        'ga' => 'IRISH',
        'it' => 'ITALIAN',
        'ja' => 'JAPANESE',
        'jw' => 'JAVANESE',
        'kn' => 'KANNADA',
        'kk' => 'KAZAKH',
        'km' => 'KHMER',
        'ko' => 'KOREAN',
        'ku' => 'KURDISH',
        'ky' => 'KYRGYZ',
        'lo' => 'LAO',
        'la' => 'LATIN',
        'lv' => 'LATVIAN',
        'lt' => 'LITHUANIAN',
        'lb' => 'LUXEMBOURGISH',
        'mk' => 'MACEDONIAN',
        'ms' => 'MALAY',
        'ml' => 'MALAYALAM',
        'mt' => 'MALTESE',
        'mi' => 'MAORI',
        'mr' => 'MARATHI',
        'mn' => 'MONGOLIAN',
        'ne' => 'NEPALI',
        'no' => 'NORWEGIAN',
        'oc' => 'OCCITAN',
        'or' => 'ORIYA',
        'ps' => 'PASHTO',
        'fa' => 'PERSIAN',
        'pl' => 'POLISH',
        'pt' => 'PORTUGUESE',
        'pt-PT' => 'PORTUGUESE_PORTUGAL',
        'pa' => 'PUNJABI',
        'qu' => 'QUECHUA',
        'ro' => 'ROMANIAN',
        'ru' => 'RUSSIAN',
        'sa' => 'SANSKRIT',
        'gd' => 'SCOTS_GAELIC',
        'sr' => 'SERBIAN',
        'sd' => 'SINDHI',
        'si' => 'SINHALESE',
        'sk' => 'SLOVAK',
        'sl' => 'SLOVENIAN',
        'es' => 'SPANISH',
        'su' => 'SUNDANESE',
        'sw' => 'SWAHILI',
        'sv' => 'SWEDISH',
        'syr' => 'SYRIAC',
        'tg' => 'TAJIK',
        'ta' => 'TAMIL',
        'tt' => 'TATAR',
        'te' => 'TELUGU',
        'th' => 'THAI',
        'bo' => 'TIBETAN',
        'to' => 'TONGA',
        'tr' => 'TURKISH',
        'uk' => 'UKRAINIAN',
        'ur' => 'URDU',
        'uz' => 'UZBEK',
        'ug' => 'UIGHUR',
        'vi' => 'VIETNAMESE',
        'cy' => 'WELSH',
        'yi' => 'YIDDISH',
        'yo' => 'YORUBA',
    );
}

function gen_menu_button($inputButtonsArray) {

    unset($list, $attributes);

    while (list($key, $button) = each($inputButtonsArray)) {

        unset($anchorAttributes);

        if (isset($button['title'])) {
            $anchorAttributes['title'] = $button['title'];
        }

        if (isset($button['id'])) {
            $anchorAttributes['id'] = $button['id'];
        }

        if ((isset($button['buttonname'])) && ((isset($button['link'])))) {

            $list[] = anchor($button['link'], strtoupper($button['buttonname']), $anchorAttributes);
        } elseif (isset($button['content'])) {

            $list[] = strtoupper($button['content']);
        }
    }

    $attributes = array('class' => 'topmenu');

    return ul($list, $attributes);
}

function show_language_select($tagname, $selectedOPT = 'en', $js = '', $onlyLanguages = NULL) {

    if ($selectedOPT == NULL) {
        $selectedOPT = 'en';
    }

    $languages = load_languages_array();

    if (is_array($onlyLanguages)) {

        while (list($key, $value) = each($languages)) {

            if (array_key_exists($key, $onlyLanguages)) {
                $onlyLanguagesChecked[$key] = $value;
            }
        }

    } else {

        $onlyLanguagesChecked = $languages;
    }

    return form_dropdown($tagname, $languages, $selectedOPT, $js);
}

function show_sentencies_number($maxNumber = NULL) {

    $ci = &get_instance();

    if ($maxNumber == NULL) {
        $maxNumber = 20;
    }

    $selectedOPT = $ci->App_model->_getUserSettingValue('NumberOfSentences');

    if (!$selectedOPT) {
        $selectedOPT = 5;
    }

    for ($i = 1; $i <= $maxNumber; $i++) {
        $arrayNumSentences[$i] = $i;
    }

    $tempforprint = '<label>' . $ci->lang->line('index_numberofsentences_label') . '</label>';
    $tempforprint .= form_dropdown('numSentences', $arrayNumSentences, $selectedOPT, 'onChange="form.submit();"');

    return $tempforprint;
}

function google_translate($text, $to_lang) {

    $ci = &get_instance();
    $api_key = $ci->config->item('google_translate_api_key');

    if($api_key){

        $from_lang = 'en';
        $link = 'https://www.googleapis.com/language/translate/v2?key=' . $api_key . '&amp;q=' . $text . '&amp;source=' . $from_lang . '&amp;target=' . $to_lang;
        echo $link;

        $response = @file_get_contents($link);
        $array = json_decode($response);
        return $array->data->translations[0]->translatedText;
        
    }

    return array();
}

function google_translateO($text, $destLang = 'es', $srcLang = 'en') {

    $text = urlencode($text);
    $destLang = urlencode($destLang);
    $srcLang = urlencode($srcLang);

    $trans = @file_get_contents("http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q={$text}&langpair={$srcLang}|{$destLang}");
    $json = json_decode($trans, true);

    if ($json['responseStatus'] != '200')
        return false;
    else
        return $json['responseData']['translatedText'];
}

function divide_text_to_sentences($textS, $pattern = '/.*?[?.!]+\"?/s') {
    $textS = preg_replace("/[\n\r]/", "", $textS);
    preg_match_all($pattern, $textS, $textS);
    $textS = $textS[0];

    foreach ($textS as $sentence_id => $sentence) {

        if (!preg_match('/^\.+$/', $sentence)) {
            $finalSentence[] = $sentence;
        }
    }

    if (!empty($finalSentence)) {
        return $finalSentence;
    }
}

function get_words_of_sentence($Sentence, $Switch = NULL) { // return array
    if ($Switch == "SHOW") {

        $Words = preg_split('/([.,?!:"\[\]\-\s]+)/', $Sentence, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($Words as $Word_id => &$Word) {
            $Word = trim($Word);
            if ($Word != '') {
                $Words_Final[] = $Word;
            }
        }
        $Words = $Words_Final;

    } else {

        $Words = preg_split('/([.,"\[\]\-\s]+)/', $Sentence, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($Words as $Word_id => &$Word) {
            $Word = trim($Word);
            $Word = strtolower($Word);
            $Word = preg_replace('/[,.-?!"]|[[:space:]]/', '', $Word);
            if (!preg_match("/\\V/", $Word)) {
                unset($Words[$Word_id]);
            }
        }
    }
    return $Words;
}

/*
 * Recursive array_key_exists
 * USED IN : site.php
 */

function array_key_exists_recursive($needle, $haystack) {

    $result = array_key_exists($needle, $haystack);

    if ($result) return $result;

    foreach ($haystack as $v) {

        if (is_array($v) || is_object($v))
            $result = array_key_exists_recursive($needle, $v);

        if ($result)
            return $result;
    }

    return $result;
}

/**
 * Script
 *
 * Generates a script inclusion of a JavaScript file
 * Based on the CodeIgniters original Link Tag.
 *
 * Author(s): Isern Palaus <ipalaus@ipalaus.es>
 *            David Mulder <david@greatslovakia.com>
 *
 * @access    public
 * @param    mixed    javascript sources or an array
 * @param    string    language
 * @param    string    type
 * @param    boolean    should index_page be added to the javascript path
 * @return    string
 */
// http://codeigniter.com/forums/viewthread/72027/

if (!function_exists('script_tag')) {

    function script_tag($src = '', $language = 'javascript', $type = 'text/javascript', $index_page = FALSE) {
        $CI = & get_instance();

        $script = '<scr' . 'ipt';

        if (is_array($src)) {
            foreach ($src as $k => $v) {
                if ($k == 'src' AND strpos($v, '://') === FALSE) {
                    if ($index_page === TRUE) {
                        $script .= ' src="' . $CI->config->site_url($v) . '"';
                    } else {
                        $script .= ' src="' . $CI->config->slash_item('base_url') . $v . '"';
                    }
                } else {
                    $script .= "$k=\"$v\"";
                }
            }

            $script .= "></scr" . "ipt>\n";
        } else {
            if (strpos($src, '://') !== FALSE) {
                $script .= ' src="' . $src . '" ';
            } elseif ($index_page === TRUE) {
                $script .= ' src="' . $CI->config->site_url($src) . '" ';
            } else {
                $script .= ' src="' . $CI->config->slash_item('base_url') . $src . '" ';
            }

            $script .= 'language="' . $language . '" type="' . $type . '"';

            $script .= ' /></script>' . "\n";
        }


        return $script;
    }

}
?>
