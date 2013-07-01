<?php echo doctype('xhtml1-trans'); ?>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <title>YOVOCA</title>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

        <?php
        echo meta(array(
            array('name' => 'author', 'content' => 'Peter Vavro'),
            array('name' => 'robots', 'content' => 'no-cache'),
            array('name' => 'description', 'content' => 'YOVOCA ' . $this->lang->line('page_description_meta')),
            array('name' => 'keywords', 'content' => $this->lang->line('page_keywords_meta')),
            array('name' => 'Content-type', 'content' => 'text/html; charset=utf-8'),
            array('property' => 'og:image', 'content' => base_url() . 'images/logo_fb.png')
        ));

        ?>
        <meta http-equiv='content-language' content='<?php echo $this->config->item('language'); ?>'/>

        <?php
        
        /* LOAD CSS STYLE FILES */ 

            $csslinks[] = 'main.css';

            foreach ($csslinks as $linkValue) {
                echo link_tag(array('href' => 'css/' . $linkValue, 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen'));
            }

            echo link_tag(array('href' => 'css/print.css', 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'print'));

        /* LOAD SCRIPTS */

            // LOAD JQUERY
            echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>';

            // GOOGLE TRANSLATE
            echo '<script type="text/javascript" src="https://www.google.com/jsapi?key='.$this->config->item('google_translate_api_key').'"></script>';

            echo script_tag(array('src' => 'js/general.js', 'charset' => 'utf-8', 'type' => 'text/javascript'));

            if(isset($scripts)){

                foreach ($scripts as $script) {
                    echo $script;
                }
            }
        ?>

        <?php // FIX FOR GRADIENTS ?>
        <!--[if gte IE 9]>
          <style type="text/css">
            .gradient {
               filter: none;
            }
          </style>
        <![endif]-->

    </head>

    <?php
        if ($this->auth_connect->is_logged_in() && isset($bodyset)) {

            foreach ($bodyset as $bkey => $bvalue) {
                $body_string[] = $bkey . '="' . $bvalue . '"';
            }
        }
    ?>

    <body <?php echo isset($body_string) ? implode(' ', $body_string) : ''; ?> >

    <?php echo $this->load->view('snippets/fb_initialization','',TRUE); ?>

    <?php // BUSY SCREEN  ?> 

        <div id="domMessage" style="display:none;">
            <img src="images/busy.gif" /><br />
            <h1><?php echo $this->lang->line('page_uploadmessage_busy') ?></h1><br />
        </div>


    <?php // CONTENT  ?>

        <div id="container">
            <div id="header">
            <?php
                $image_properties = array(
                    'src' => 'images/top_logo.png',
                    'alt' => 'LESU logo',
                    'title' => 'LESU logo',
                    'class' => 'logo_img',
                );

                echo heading($this->lang->line('title_pagename_title'), 1);

                // LOAD USER TOP PANEL
                echo '<div id="userpanel">';

                    // panel table data array 
                    unset($paneldata);
                    unset($dataView);

                    // LOAD TOP MENU
                    if (isset($topmenu_button)) {
                        $topmenu_button[] = $topmenu_button;
                    }

                    $topmenu_button[] = array('buttonname' => $this->lang->line('menu_mainmenu_btn'), 'link' => '', 'title' => $this->lang->line('menu_mainmenu_btn_title'));

                    // GET MENU
                    $paneldata[] = gen_menu_button($topmenu_button);

                // Is user logged in
                if ($this->auth_connect->is_logged_in()) {

                    $user = $this->session->userdata('user_info');

                    // GENERATE FORM WITH LANGUAGES SELECT 
                    $attributes = array('name' => 'changelangform', 'style' => "display: inline;", 'method' => "post"); // ,'class' => 'email', 'id' => 'myform'
                    $hidden = array('formname' => 'changelangform', 'testa' => 'funguje');
                    $jstemp = 'onChange="form.submit();"';

                    $lngform = form_open(uri_string(), $attributes, $hidden);

                    $lngform .= '<label>' . $this->lang->line('index_subslanguage_label') . '</label>' . show_language_select('userlang', $this->lang->lang(), $jstemp);
                    $lngform .= form_close();

                    // FB DATA
                    $paneldata[] = $lngform;
                    $paneldata[] = '<fb:like href="' . site_url() . '" send="true" layout="button_count" show_faces="false" font="trebuchet ms"></fb:like>';
                    $paneldata[] = $this->auth_connect->get_logout_button();

                } else {

                    // GET LOGIN BUTTON
                    $paneldata[] = $this->auth_connect->get_login_button();

                    // GET LANGUAGE FLAGS SWITCH
                    $paneldata[] = '<ul id="userlang">' . $this->load->view('elements/language', '', true) . '</ul>';
                }

                $this->table->clear();
                $this->table->add_row($paneldata);

                $tmpl = array('table_open' => '<table>');
                $this->table->set_template($tmpl);
                echo $this->table->generate();

            echo '</div>';

            // SHOW ARROW SUGGESTING WHERE TO LOGIN

                if (!$this->auth_connect->is_logged_in()) {
                    echo '<div id="howtostartinfo">';
                    $image_separator = array('src' => 'images/arrowUPt.png');
                    echo $this->lang->line('navi_howtostartinfo_label') . nbs(1) . img($image_separator);
                    echo '</div>';
                }

                echo '</div>';

            // FULL WIDE CONTENT

                if (isset($contentwide)) {

                    echo '<div id="ContentWide">';

                    // NOT APPLIED
                    foreach ($contentwide as $requested) {
                        echo load_article($requested);
                    }

                    echo '</div>';
                }

            echo '<div id="ColLeftDouble">';

            if (isset($contentdouble)) {

                echo '<div id="ContentDouble">';

                    if (is_array($contentdouble)) {

                        foreach ($contentdouble as $requested) {

                            if (isset($requested['identifier'])) {

                                switch ($requested['identifier']) {
                                    case 'mlist':

                                        if ($this->auth_connect->is_logged_in()) {

                                            // LOAD SUBTILES LIST UI
                                            $article['divclass'] = 'whiteone';
                                            $article['Title'] = $this->lang->line('index_movielist_label');

                                            $attributes = array('name' => 'subslistmanipulationform');
                                            $hidden = array('formname' => 'subslistmanipulationform');
                                            $article['ArticleText'] = form_open(uri_string(), $attributes, $hidden) . $this->App_model->_show_movie_list() . form_close();

                                            echo $this->load->view('text_view', array('Article' => $article), true);
                                        }

                                        break;
                                    case 'usub':

                                        if ($this->auth_connect->is_logged_in()) {

                                            // LOAD UPLOAD SUBTILES FORM
                                            $article['divclass'] = 'whiteone';
                                            $article['Title'] = $this->lang->line('index_addsubs_label');
                                            $article['ArticleText'] = $this->load->view('blocks/addSubFile', '', true);

                                            echo $this->load->view('text_view', array('Article' => $article), true);
                                        }

                                        break;
                                    case 'checking':

                                        unset($layoutdata, $content);
                                        $content = '';

                                        $layoutdata['divclass'] = 'whiteone';
                                        $layoutdata['divid'] = 'checking';

                                        if (isset($title)) {
                                            $layoutdata['Title'] = $title;
                                        }

                                        $content .= gen_navi_help(1, $this->lang->line('index_sentence_title'));
                                        $content .= '<div id="sentence"class="selectingplace" title="' . $this->lang->line('index_sentence_title') . '"></div>';

                                        $content .= gen_navi_help(2, $this->lang->line('navi_nextsentence_btn'));

                                        $content .= '<div class="buttons">';


                                        $content .= '<input id="sNext" type="button" value="' . $this->lang->line('index_nextsentence_btn') . '"/>';
                                        $content .= '<div id="ajax_error"></div>';

                                        $this->table->clear();

                                        // REMAINING SENTENCES
                                        $remain = array('data' => $this->lang->line('info_remainingsentences_label_prefix') . '<span id="nwS"></span>' . $this->lang->line('info_remainingsentences_label_sulfix'), 'class' => 'info');

                                        // SWICH - NUMBER OF SENTENCES
                                        $switchnum = show_sentencies_number(20);

                                        $this->table->add_row($switchnum, $remain);

                                        $tmpl = array('table_open' => '<table class="params">');
                                        $this->table->set_template($tmpl);
                                        $content .= $this->table->generate();


                                        $content .= '</div>';

                                        $content .= '<div style="clear:both;"></div>';

                                        $layoutdata['ArticleText'] = $content;

                                        echo $this->load->view('text_view', array('Article' => $layoutdata), true);

                                        break;
                                    default:

                                        echo load_article($requested);
                                }

                            } else {

                                echo load_article($requested);
                            }
                        }

                    } else {

                        echo $contentdouble;
                    }

                    echo '</div>';

                } else {

                    echo '<div id="ColLeft">';

                        if (isset($left)) {

                            foreach ($left as $k => $requested) {
                                echo load_article($requested);
                            }
                        }

                    echo '</div>';

                    echo '<div id="ColRightOne">';

                        if (isset($middle)) {

                            foreach ($middle as $k => $requested) {
                                echo load_article($requested);
                            }
                        }

                    echo '</div>';
                }

            echo '</div>';

            echo '<div id="ColRightTwo">';

                if (isset($right)) {

                    foreach ($right as $k => $requested) {
                        echo load_article($requested);
                    }
                }
            ?>
                <div class="textPlace">
                    <div class="wrapText">
                        <div class="gads" id="panelspace">
                            <?php
                            if (@file_exists(APPPATH."views/google_adsense/google_adsense_snippet_code_01.php")){
                                echo $this->load->view('google_adsense/google_adsense_snippet_code_01', '', true);
                            } ?>
                        </div>
                    </div>
                </div>
            </div>    
            <div id="footer">
                <span>Copyright© 2010 Peter Vavro / admin(at)yovoca(dot)com</span>
                <span>/ CI:<?php echo CI_VERSION; ?></span>
                <?php // echo ' - '.nbs(1).mailto('admin(at)yovoca(dot)com', 'Contact Me');?>
                <p><?php // echo $this->benchmark->elapsed_time();?></p>
            </div>
        </div>
    </body>
</html>

