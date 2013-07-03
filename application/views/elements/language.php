<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* SVN FILE: $Id: language.php 188 2009-04-10 07:06:02Z Roland $ */
/**
  |------------------------------------------------------------------------
  | view element to display a language selection.
  |
  | This element shows the flags of all supported languages in a row
  | and allows the user to select one of them.
  | The flag images are expected in the img/lang subdirectory of the
  | site's webroot. The image names must correspond to the keys of
  | the array in the configuration entry $config['lang_avail'].
  |
  | Per language there should be two images for the selected and the
  | unselected state, i.e. 'en.gif' and 'en_sel.gif'
  |
  | This library is free software; you can redistribute it and/or
  | modify it under the terms of the GNU Lesser General Public
  | License as published by the Free Software Foundation; either
  | version 2.1 of the License, or (at your option) any later version.
  |
  | This library is distributed in the hope that it will be useful,
  | but WITHOUT ANY WARRANTY; without even the implied warranty of
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  | Lesser General Public License for more details.
  |
  | You should have received a copy of the GNU Lesser General Public
  | License along with this library; if not, write to the Free Software
  | Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
  |
 */
/**
 * begin of user configurable items
 */
// base directory of the flag images
$_img_dir = isset($img_dir) ? $img_dir : base_url() . 'images/flags';

// CSS class for each flag
$_img_css_class = isset($img_css_class) ? $img_css_class : '';

// CSS style for each flag
$_img_css_style = isset($img_css_style) ? $img_css_style : '';

// base value of the tabindex for the links
$_tabindex_start = isset($tabindex_start) ? $tabindex_start : 20;

// surrounding html tags for each flag
$_html_tag_sf = isset($html_tag_sf) ? $html_tag_sf : 'li'; // span
$_html_tag_ef = isset($html_tag_ef) ? $html_tag_ef : '/li'; // /span

// surrounding html tags for all flags
$_html_tag_start = isset($html_tag_start) ? $html_tag_start : 'p';
$_html_tag_end = isset($html_tag_end) ? $html_tag_end : '/p';

/**
 * end of user configurable items
 */
// render the img tag
if (!function_exists('_render_img_tag')) {

    function _render_img_tag($img_dir, $title, $pic, $img_css_class, $img_css_style) {
        $fstr = "<img src='$img_dir/$pic'" .
                " alt='$title' title='$title'";
        if (!empty($img_css_class)) {
            $fstr .= " class='$img_css_class'";
        }
        if (!empty($img_css_style)) {
            $fstr .= " style='$img_css_style'";
        }
        $fstr .= " />";
        return $fstr;
    }

}

// get array of available languages
$_lang_avail = $this->lang->languages;

if ($_lang_avail !== false) {

    // get user's current language code
    $_sel_lang = $this->lang->lang();

    // load the respective language file
    if (!function_exists('anchor')) {
        $this->load->helper('url');
    }

    // > nulovanie premenných
    $_Output = Array();
    $v = 0;
    // <

    foreach ($_lang_avail as $_lang => $_language) {

        // get language name in currently selected language
        $_lng = $this->lang->line('lng_' . $_lang);
        if ($_sel_lang == $_lang) {
            // show selected language button
            // $fstr = _render_img_tag($_img_dir, sprintf($this->lang->line('title_localeSetTo'), $_lng),$_lang.'_sel.png', $_img_css_class, $_img_css_style);
            $fstr = _render_img_tag($_img_dir, sprintf($this->lang->line('title_localeSetTo'), $_lng), $_lang . '.png', $_img_css_class, $_img_css_style);
        } else {
            // show unselected language button
            $fstr = _render_img_tag($_img_dir, sprintf($this->lang->line('title_localeChgTo'), $_lng), $_lang . '.png', $_img_css_class, $_img_css_style);

            // just link to the same page again
            $selfuri = $this->uri->ruri_string();
            if ($this->uri->total_rsegments() == 1) {
                $selfuri .= 'index';
            }
            // (the MY_Config::site_url() method appends the new language code)
            // $fstr = anchor(site_url($selfuri, $_lang), $fstr, array('title'=>'','tabindex'=>($v + $_tabindex_start)));
            // echo anchor($this->lang->switch_uri('sk'),'Display current page in French');

            $fstr = anchor($this->lang->getURIwLNG($_lang), $fstr, array('title' => '', 'tabindex' => ($v + $_tabindex_start)));
            $v++;
        }
        $_Output[] = $fstr;
    }

    // echo "<$_html_tag_start><$_html_tag_sf>".implode("<$_html_tag_ef>\n<$_html_tag_sf>", $_Output)."<$_html_tag_ef><$_html_tag_end>\n";
    echo "<$_html_tag_sf>" . implode("<$_html_tag_ef>\n<$_html_tag_sf>", $_Output) . "<$_html_tag_ef>"; //\n
}
