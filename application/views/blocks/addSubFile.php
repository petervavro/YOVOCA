<?php

    /* ADD MOVIE SUBTITLES FORM */

    $moviename = array('name' => 'moviename', 'id' => '', 'size' => 30);
    $filename = array('name' => 'userfile');

    $this->load->library('table');
    $this->table->clear();

    unset($cell);

    $hidden = array('formname' => 'uploadfileform');
    echo form_open_multipart($this->uri->uri_string(), '', $hidden);

    if ($this->input->post('formname') == 'uploadfileform') {

        if ($this->upload->display_errors()) {
            $cell = array('data' => $this->upload->display_errors(), 'class' => 'errorForm', 'colspan' => 3);
            $this->table->add_row($cell);
        }

        if (validation_errors() != NULL) {
            $cell = array('data' => validation_errors(), 'class' => 'errorForm', 'colspan' => 3);
            $this->table->add_row($cell);
        }
    }

    $genNaviHelpSettings['imgOrientation'] = 'r';

    $helprow = array('data' => gen_navi_help(1, $this->lang->line('navi_moviename_input'), $genNaviHelpSettings), 'colspan' => 3); // 'colspan' => 2
    $formElementCell = array('data' => form_label($this->lang->line('index_moviename_label'), $moviename['name']) . nbs(1) . form_input($moviename), 'class' => 'highlight', 'colspan' => 3);
    $this->table->add_row($helprow);
    $this->table->add_row($formElementCell);

    $subtitles_page[] = array('name' => "opensubtitles", 'link' => "http://www.opensubtitles.org/");
    $subtitles_page[] = array('name' => "subscene", 'link' => "http://subscene.com/");
    $subtitles_page[] = array('name' => "divxsubtitles", 'link' => "http://www.divxsubtitles.net/");
    $subtitles_page[] = array('name' => "allsubs", 'link' => "http://www.allsubs.org/");
    $subtitles_page[] = array('name' => "tvsubtitles", 'link' => "http://www.tvsubtitles.net/");
    $subtitles_page[] = array('name' => "subtitlesource", 'link' => "http://www.subtitlesource.org/");
    $subtitles_page[] = array('name' => "moviesubtitles", 'link' => "http://www.moviesubtitles.org/");
    $subtitles_page[] = array('name' => "mysubtitles", 'link' => "http://www.mysubtitles.com/");

    shuffle($subtitles_page);

    foreach ($subtitles_page as $valueSP) {
        $listsubpages[] = '<a href="' . $valueSP['link'] . '" target="_blank">' . strtoupper($valueSP['name']) . '</a>';
    }

    unset($navi_points);

    $navi_points[] = $this->lang->line('navi_subsource_todo_1');
    $navi_points[] = $this->lang->line('navi_subsource_todo_2');

    $helprow = array('data' => gen_navi_help(2, $navi_points, $genNaviHelpSettings), 'colspan' => 3);
    $conCell = array('data' => form_label($this->lang->line('index_pageswithsubs_label')) . '<p>' . implode(' / ', $listsubpages) . '</p>', 'class' => 'highlight', 'colspan' => 3);
    $this->table->add_row($helprow);
    $this->table->add_row($conCell);

    unset($navi_points);
    $navi_points[] = $this->lang->line('navi_subfile_getfile');
    $navi_points[] = $this->lang->line('navi_subfile_rule_1');
    $navi_points[] = $this->lang->line('navi_subfile_rule_2');
    $helprow = array('data' => gen_navi_help(3, $navi_points, $genNaviHelpSettings), 'colspan' => 3);
    $formElementCell = array('data' => form_label($this->lang->line('index_subtitlesfile_label'), $filename['name']) . nbs(1) . form_upload($filename), 'class' => 'highlight', 'colspan' => 3);
    $this->table->add_row($helprow);
    $this->table->add_row($formElementCell);

    $helprow = array('data' => gen_navi_help(4, $this->lang->line('navi_sublang_select'), $genNaviHelpSettings), 'colspan' => 3);
    $formElementCell = array('data' => form_label($this->lang->line('index_subslanguage_label')) . nbs(1) . show_language_select('LNG'), 'class' => 'highlight', 'colspan' => 3);
    $this->table->add_row($helprow);
    $this->table->add_row($formElementCell);

    $helprow = array('data' => gen_navi_help(5, $this->lang->line('navi_subupl_btn'), $genNaviHelpSettings), 'colspan' => 3);

    $dataUploadButton = array(
        'name' => 'upload',
        'id' => 'uploadBTN',
        'value' => $this->lang->line('index_uploadbutton_label'),
    );

    $formElementCell = array('data' => form_submit($dataUploadButton), 'class' => 'highlight', 'colspan' => 3);
    $this->table->add_row($helprow);
    $this->table->add_row($formElementCell);

    $this->table->set_template(array('table_open' => '<table class="addSub">'));
    echo $this->table->generate();
    echo form_close();
