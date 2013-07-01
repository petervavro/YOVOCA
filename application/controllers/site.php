<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Site extends CI_Controller {


    function __construct() {

        parent::__construct();

        switch ($this->input->post('formname')) {
            case 'changeSNform':

                if ($this->auth_connect->is_logged_in()) {
                    $this->App_model->_setUserSetting('NumberOfSentences', $this->input->post('numSentences'));
                }

                break;
            case 'uploadfileform':

                if ($this->auth_connect->is_logged_in()) {
                    unset($function_result);
                    $function_result = $this->_execute_upload();

                    if (is_numeric($function_result)) {
                        redirect(base_url() . 'site/scan_subs/' . $function_result);
                    }
                }

                break;
            case 'subslistmanipulationform':

                if ($this->auth_connect->is_logged_in()) {
                    $this->_delete_subs($this->input->post('btndel'));
                }

                break;
            case 'btnboxform':

                if ($this->auth_connect->is_logged_in()) {

                    if (isset($_REQUEST['numSentences'])) {
                        $this->App_model->_setUserSetting('NumberOfSentences', $this->input->post('numSentences'));
                    }
                }

                break;
        }

        // send raw HTTP headers to set the content type for MS IE
        $this->output->set_header("Content-Type: text/html; charset=UTF-8");
    }


    function _output($data_output = NULL) {

        // LOAD DEFAULT MAIN PAGE
        if (!$data_output) {

            $data_output['contentdouble'][] = array("identifier" => 'expl', "divclass" => 'whiteone', 'divid' => 'firstone');
            $data_output['contentdouble'][] = array("identifier" => 'hiw', "divclass" => 'blueone');

            unset($slideshow);

            $slideshow[] = array("identifier" => 'abt', "divclass" => 'transparentsection');
            $slideshow[] = array("identifier" => 'wtf', "divclass" => 'transparentsection');
            $slideshow[] = array("identifier" => 'wcu', "divclass" => 'transparentsection');
            $slideshow[] = array("identifier" => 'wmj', "divclass" => 'transparentsection');

            $data_output['right'][] = array("identifier" => 'vhts', "divclass" => 'video');
            $data_output['right'][]['slideshow'] = $slideshow;
            $data_output['right'][] = array("identifier" => 'wpf', "divclass" => 'transparentsection');
        }

        if (array_key_exists_recursive('slideshow', $data_output)) {

            $data_output['scripts'][] = script_tag(array('src' => 'js/jquery.cycle.lite.js', 'charset' => 'utf-8', 'type' => 'text/javascript'));
        }

        echo $this->load->view('template_layout', $data_output, TRUE);
    }


    function index() {

        $data_output = array();

        // IF USER IS NOT LOGGED IN

        $segment = $this->uri->segment(1);

        // SET LANGUAGE FORM URI
        if (!$segment)
        {

            // GET LANGUAGE FROM COOKIE
            $language_code = $this->input->cookie($this->config->item('lang_cookie_name'), TRUE);

            if(!$language_code) {

                // GET LANGUAGE FROM BROWSER

                // no cookie/URI language code: check browser's languages
                $accept_langs = $this->input->server('HTTP_ACCEPT_LANGUAGE');

                if ($accept_langs !== false) {

                    //explode languages into array
                    $accept_langs = strtolower($accept_langs);
                    $accept_langs = explode(",", $accept_langs);
                    //log_message('debug', __CLASS__.".detectLanguage(): browser languages: ".print_r($accept_langs, true));

                    // check all of them
                    foreach ($accept_langs as $lang) {

                        //log_message('debug', __CLASS__.".detectLanguage(): Check lang: $lang");

                        // remove all after ';'
                        $pos = strpos($lang, ';');

                        if ($pos !== false) {
                            $language_code = substr($lang, 0, $pos);
                        }
                    }
                }
            }

            $allowed_languages = $this->config->item('languages');

            if ((isset($allowed_languages[$language_code]))) {

                // Get full language name
                $language_name = $allowed_languages[$language_code];

                $this->config->set_item('language', $language_name);

                redirect('/'.$language_code, 'refresh', 302);
            }
        }

        $this->load->helper('language');
        $this->lang->load('lang');

        // SET/UPDATE COOKIES

            $this->input->set_cookie(array(
                'name' => $this->config->item('lang_cookie_name'),
                'value' => $this->lang->lang(),
                'expire' => $this->config->item('lang_expiration'),
                'domain' => $this->config->item('cookie_domain'),
                'path' => $this->config->item('cookie_path'),
                'prefix' => $this->config->item('cookie_prefix')
            ));


        if ($this->auth_connect->is_logged_in()) {

            $data_output['contentdouble'][] = array("identifier" => 'mlist');
            $data_output['contentdouble'][] = array("identifier" => 'usub');
            $data_output['contentdouble'][] = array("identifier" => 'ftr', "divclass" => 'blueone');

            $data_output['right'][] = array("identifier" => 'vlsf', "divclass" => 'video');
            $data_output['right'][] = 'ads';
            $data_output['right'][] = array("identifier" => 'nws', "divclass" => 'transparentsection');
        }

        $this->output->append_output($data_output);
    }


    function scan_subs($MovieID = NULL) {

        if ($MovieID != NULL) {

            if ($this->auth_connect->is_logged_in()) {

                $MovieData = $this->App_model->_get_movie_info($MovieID);

                $data_output['scripts'][] = '<script src="' . site_url() . '/js/ajax_scan.js"charset="utf-8"type="text/javascript"></script>';

                // loading Movie name used to send request in AJAX
                $data_output['scripts'][] = '<script type="text/javascript">var movielang="' . $MovieData['Language'] . '"; var movieID="' . $MovieID . '";</script>';

                // LOAD CHECKING PANEL
                $data_output['contentdouble'][] = array("identifier" => 'checking');
                $data_output['contentdouble'][] = array("identifier" => 'checkhelp', "divclass" => 'blueone');

                $data_output['right'][] = array("identifier" => 'translate');
                $data_output['right'][] = 'ads';

                // ACTIVATE AJAX FUNCTIONS
                $data_output['bodyset']['onload'] = "loadNextSentence();";

                $this->output->set_output($data_output);
            }
        }
    }


    function words_list($MovieID) {

        if ($this->auth_connect->is_logged_in()) {

            $MovieData = $this->App_model->_get_movie_info($MovieID);
            $data_output['contentwide'][] = array('Title' => $this->lang->line('title_unknownwordslist_label'), 'ArticleText' => $this->App_model->showUnknownWords($MovieID), 'divclass' => 'whiteone');

            $atts = array(
                'width' => '800',
                'height' => '600',
                'scrollbars' => 'yes',
                'status' => 'yes',
                'resizable' => 'yes',
                'screenx' => '0',
                'screeny' => '0'
            );

            // TODO :: print show - add CSS
            $data_output['left'][] = 'ads';
            $data_output['right'][] = 'ads';

            $this->output->append_output($data_output);
        }
    }

    function _delete_subs($MovieID) {

        $this->App_model->deleteSubs($MovieID);
    }

    function _execute_upload() {


        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'sub|srt|txt';
        $config['max_size'] = '300';

        $this->load->library('upload', $config);

        $this->form_validation->set_rules('moviename', 'MOVIE NAME', 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            if (!$this->upload->do_upload()) {

                $error = array('error' => $this->upload->display_errors());

            } else {
                $data_upload = array('upload_data' => $this->upload->data(), 'Moviename' => $this->input->post('moviename'), 'LNG' => $this->input->post('LNG'));
                return $this->App_model->Insert_MovieSubs($data_upload);
            }
        }
    }

}