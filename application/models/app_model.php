<?php

class App_model extends CI_Model {

    function __construct() {

        // Call the Model constructor
        parent::__construct();
        $this->load->helper('file');
    }


    function showUnknownWords($MovieID = null, $PrintMode = NULL) {

        $Language_value = $this->session->userdata('user_translation_lang');

        $this->db->select('DataWords.Word AS Word, DataWords.Language AS WordLNG, DataSentences.Sentence AS Sentence'); // ,connWordTranslation.GoogleTranslation AS gTranslation
        $this->db->from('connWordSentence');

        $this->db->join('connSentenceMovie', 'connSentenceMovie.SentenceID = connWordSentence.SentenceID AND connSentenceMovie.MovieID=' . $MovieID);

        $this->db->join('checkedUserWord', 'checkedUserWord.WordID=connWordSentence.WordID');
        $this->db->where('checkedUserWord.Unknown', TRUE);
        $this->db->where('checkedUserWord.UserInsertionID', $this->session->userdata('user_id'));

        $this->db->join('DataWords', 'DataWords.ID = connWordSentence.WordID', 'LEFT');
        $this->db->join('DataSentences', 'DataSentences.ID = connWordSentence.SentenceID', 'LEFT');
        $this->db->join('connWordTranslation', 'connWordTranslation.connWSID = connWordSentence.ID AND connWordTranslation.TranslationLanguage="' . $Language_value . '"', 'LEFT');
        $this->db->order_by("DataWords.Word,connWordSentence.ID", "asc");

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                $words[$row['Word']]['Sentences'][] = '<p>' . $row['Sentence'] . '</p>';
                $wordLNG = $row['WordLNG'];
            }

            $this->table->clear();
            $this->table->set_heading(get_language_name($wordLNG), $this->lang->line('index_sentence_label'));

            $tmpl = array('table_open' => '<table class="wordlist">');
            $this->table->set_template($tmpl);

            foreach ($words as $wordName => $row) {
                $this->table->add_row(array('data' => $wordName, 'class' => 'wordname'), array('data' => implode('', $row['Sentences']), 'class' => 'sentences'));
            }

            $tableResult = $this->table->generate();

            return $tableResult;
        }

        return $this->lang->line('error_no_words_are_selected');
    }

    function _setUserSetting($settingType, $settingValue) {
        $this->db->set('settingValue', $settingValue);

        if (!$this->_getUserSettingValue($settingType)) {
            $this->db->set('settingType', $settingType);
            $this->db->set('UserInsertionID', $this->session->userdata('user_id'));
            $this->db->insert('connUserSettings');
        } else {
            $this->db->where('settingType', $settingType);
            $this->db->where('UserInsertionID', $this->session->userdata('user_id'));
            $this->db->update('connUserSettings');
        }
        return TRUE;
    }


    function getArticleText($artIdentifier, $LNG = NULL) {

        $this->db->from('DataArticles');
        $this->db->where('Identifier', $artIdentifier);

        if (($LNG == NULL) && (!$LNG)) {

            $LNG = $this->lang->lang();
        }

        $this->db->where('language', $LNG);
        $query = $this->db->get();
        return $query->row_array();
    }


    function getArticleContentText($artIdentifier, $LNG = NULL) {
        $articletemp = $this->getArticle($artIdentifier, $LNG);
        return '<span>' . $articletemp['ArticleText'] . '</span>';
    }


    function getArticle($artIdentifier, $LNG = NULL) {

        $row = $this->getArticleText($artIdentifier, $LNG);

        // Shows default article in english if there no article in current language
        if (!$row) {

            if ($LNG != $this->lang->lang()) {
                return $this->getArticle($artIdentifier, $this->lang->lang());
            }

            return array();
        }

        return $row;
    }


    function _insertArticle($article) {

        if (isset($article['Title'])) {
            if (is_array($article['Title'])) {
                $articletexttemp = '<' . $article['Title']['Tag'];
                if (isset($article['Title']['Style'])) {
                    $articletexttemp .= ' style="' . $article['Title']['Style'] . '"';
                }
                $articletexttemp .= '>' . $article['Title']['Text'] . '</' . $article['Title']['Tag'] . '>';
            } else {
                $articletexttemp .= '<span>' . $article['Title'] . '</span>';
            }
            $article['Title'] = $articletexttemp;
        }

        if (is_array($article['ArticleText'])) {
            $articletexttemp = '';
            foreach ($article['ArticleText'] as $key => $value) {
                if (is_array($value)) {
                    if (isset($value['Title'])) {
                        if (is_array($value['Title'])) {
                            $articletexttemp .= '<' . $value['Title']['Tag'];
                            if (isset($value['Title']['Style'])) {
                                $articletexttemp .= ' style="' . $value['Title']['Style'] . '"';
                            }
                            $articletexttemp .= '>' . $value['Title']['Text'] . '</' . $value['Title']['Tag'] . '>';
                        } else {
                            $articletexttemp .= '<h1>' . $value['Title'] . '</h1>';
                        }
                    }
                    if (isset($value['Text'])) {
                        $articletexttemp .='<';
                        if (isset($value['Tag'])) {
                            $articletexttemp .= $value['Tag'];
                        } else {
                            $articletexttemp .= 'p';
                        }
                        if (isset($value['Style'])) {
                            $articletexttemp .= ' style="' . $value['Style'] . '"';
                        }
                        $articletexttemp .='>' . $value['Text'] . '</p><br />';
                    }
                } else {
                    $articletexttemp.='<p>' . $value . '</p>';
                }
            }
            $article['ArticleText'] = $articletexttemp;
        }

        if (!$this->getArticleText($article['Identifier'], $article['Language'])) {
            $this->db->set('Title', $article['Title']);
            $this->db->set('ArticleText', $article['ArticleText']);
            $this->db->set('Identifier', $article['Identifier']);
            $this->db->set('Language', $article['Language']);
            $this->db->set('Color', $article['Color']);
            $this->db->set('UserInsertionID', $this->session->userdata('user_id'));
            $this->db->insert('DataArticles');
            // return $this->db->insert_id();
        }

        return TRUE;
    }

    // GET REQUESTE VALUE OF 
    function _getUserSettingValue($settingType) {

        $this->db->from('connUserSettings');
        $this->db->where('settingType', $settingType);
        $this->db->where('UserInsertionID', $this->session->userdata('user_id'));
        $query = $this->db->get();
        $row = $query->row_array();

        if (!$row) {
            return NULL;
        } else {
            return $row['settingValue'];
        }
    }

    function addUser($ProviderName, $user_profile) {

        if ($ProviderName == 'facebook') {

            $this->db->set($ProviderName . '_auth_id', $user_profile['id']);
            $this->db->set('UserDesc', json_encode($user_profile));
            $this->db->set('UserInsertionID', 999999);
            $this->db->insert('DataLoginUsers');
            return $this->db->insert_id();
        }
    }

    function _check_user_exist($ProvidersUserID, $ProviderName = 'facebook') {

        $this->db->from('DataLoginUsers');
        $this->db->where('DataLoginUsers.' . $ProviderName . '_auth_id', $ProvidersUserID); // $this->session->userdata('user_id')
        $query = $this->db->get();
        return $query->row_array();
    }

    function _get_movie_info($MovieID) {
        $this->db->from('connUserMovie');
        $this->db->where('connUserMovie.MovieID', $MovieID);
        $this->db->where('connUserMovie.UserInsertionID', $this->session->userdata('user_id'));
        $this->db->join('DataMovies', 'DataMovies.ID = connUserMovie.MovieID');
        $query = $this->db->get();
        $row = $query->row_array();
        return $row;
    }

    // Shows list of movies of user
    function _show_movie_list() { 

        $this->db->select('connUserMovie.ID AS ID,DataMovies.ID AS MovieID,DataMovies.MovieName AS MovieName');
        $this->db->from('connUserMovie');
        $this->db->where('connUserMovie.UserInsertionID', $this->session->userdata('user_id'));
        $this->db->join('DataMovies', 'DataMovies.ID = connUserMovie.MovieID');
        $query = $this->db->get();
        $counter = 1;
        $this->table->clear();

        foreach ($query->result_array() as $row) {

            $image_separator = array('src' => 'images/separator.png');

            $img_chk = array('src' => 'images/btn_chkG.png', 'alt' => $this->lang->line('btn_checksubsentences_label'), 'title' => $this->lang->line('btn_checksubsentences_title'));
            $img_lst = array('src' => 'images/btn_lst.png', 'alt' => $this->lang->line('btn_unknowwordslist_label'), 'title' => $this->lang->line('btn_unknowwordslistl_title'));

            $imgsep = array('data' => img($image_separator), 'class' => 'separator'); //width:100%;
            $counterTD = array('data' => $counter++, 'class' => 'counter');
            $MovieNameCell = array('data' => anchor('site/scan_subs/' . $row['ID'], '<span style="width:100%;">' . $row['MovieName'] . '</span>'), 'class' => 'moviename'); //width:100%;

            $btnChkSubs = array('data' => anchor('site/scan_subs/' . $row['ID'], img($img_chk), array('title' => $this->lang->line('btn_checksubsentences_label'))), 'class' => 'chkbtn');
            $btnUnknownWords = array('data' => anchor('site/words_list/' . $row['ID'], img($img_lst), array('title' => $this->lang->line('btn_unknowwordslist_title'))), 'class' => 'unkbtn');
            $btnOth = array('data' => form_submit('btndel', $row['ID'], 'title="' . $this->lang->line('btn_delsubfile_title') . '" class="btndel"'), 'class' => 'othbtn');
            $datareturn[] = array($counterTD, $MovieNameCell, $imgsep, $btnChkSubs, $imgsep, $btnUnknownWords, $imgsep, $btnOth);
        }

        if ((isset($datareturn)) && (!is_null($datareturn))) {
            $this->table->set_template(array('table_open' => '<table class="movieList">'));
            $this->table->set_heading('-', $this->lang->line('index_moviename_label'), '-', '(button)', '-', '(button)', '-', '-');
            return $this->table->generate($datareturn);
        } else {
            return '<p>' . $this->lang->line('error_user_nomovie_exist') . '</p>';
        }
    }

    function _selectWords($sWords) {

        unset($checkedSentencesArray);

        if ($sWords) {

            unset($JustSavedWordIDs);
            $JustSavedWordIDs = array();

            // Run all received words and save or update them
            foreach ($sWords as $saveWord) {

                if (!in_array($saveWord['ID'], $JustSavedWordIDs)) {

                    $saveresult = $this->saveCheckedWord($saveWord['ID'], $saveWord["Unknown"]);

                    // ak je zapis prijate slovo ako checknute
                    if ($saveresult) {

                        // pretoze sa slovo vo vete moze vyskytovat viac krat zapis ho do premennej, aby sa v tomto cykle neobiavila duplicita
                        $JustSavedWordIDs[] = $saveWord['ID'];

                        // if system received word translation save translation 
                        if (((isset($saveWord["gTranslation"])) && ($saveWord["gTranslation"] != NULL)) OR ((isset($saveWord["uTranslation"])) && ($saveWord["uTranslation"] != NULL))) {

                            // LOAD all words for sentence and add information if thay have been checkbefore or not 
                            $this->db->select('connWordSentence.ID');
                            $this->db->from('connWordSentence');
                            $this->db->where('connWordSentence.SentenceID', $saveWord["ID"]);
                            $this->db->where('connWordSentence.SentenceID', $saveWord["sID"]);
                            $query = $this->db->get();

                            // find out if there is some words

                            if (($query->num_rows() > 0) AND ($row = $query->row())) {

                                $this->saveUserTranslationData($row['ID'], $saveWord["gTranslationLNG"], $saveWord["gTranslation"], (isset($saveWord["uTranslation"]) ? $saveWord["uTranslation"] : NULL));
                            }
                        }
                    }
                }

                $checkedSentencesArray[] = $saveWord['sID'];
            }

            // SAVE JUST RECEVED SENTENCES
            if ((isset($checkedSentencesArray)) AND ($checkedSentencesArray)) {

                foreach (array_unique($checkedSentencesArray) as $sID) {
                    $this->_selectSentence($sID);
                }
            }
        }
    }

    function _selectWords_v1($sWords) {

        unset($checkedSentencesArray);

        if ($sWords) {

            unset($JustSavedWordIDs);

            // run all received words
            foreach ($sWords as $valueUnknownWords) {

                // LOAD all words for sentence and add information if thay have been checkbefore or not 
                $this->db->select('connWordSentence.ID AS mainID,checkedUserWord.ID AS checkedID,connWordSentence.WordID AS WordID,connWordSentence.SentenceID AS SentenceID,checkedUserWord.Unknown AS Unknown');
                $this->db->from('connWordSentence');
                $this->db->where('connWordSentence.SentenceID', $valueUnknownWords["sID"]);
                $this->db->join('checkedUserWord', 'checkedUserWord.WordID=connWordSentence.WordID AND checkedUserWord.Language="' . $this->session->userdata('user_translation_lang') . '" AND checkedUserWord.UserInsertionID=' . $this->session->userdata('user_id'), 'LEFT');
                $query = $this->db->get();

                // find out if there is some words
                if ($query->num_rows() > 0) {

                    // run words stored in the database
                    foreach ($query->result_array() as $row) {

                        // zisti ci je slovo v databaze
                        if ((is_numeric($valueUnknownWords["ID"])) AND ($valueUnknownWords["ID"] == $row['WordID']) AND (((isset($JustSavedWordIDs)) AND !(in_array($row['WordID'], $JustSavedWordIDs))) || (!isset($JustSavedWordIDs)))) {
                            // ak je zapis prijate slovo ako checknute
                            if ($this->saveCheckedWord($row['WordID'], $valueUnknownWords["Unknown"], (isset($row['checkedID']) ? $row['checkedID'] : NULL)) == TRUE) {
                                // pretoze sa slovo vo vete moze vyskytovat viac krat zapis ho do premennej, aby sa v tomto cykle neobiavila duplicita
                                $JustSavedWordIDs[] = $row['WordID'];
                                // ulož prave prijatý preklad
                                if (((isset($valueUnknownWords["gTranslation"])) && ($valueUnknownWords["gTranslation"] != NULL)) OR ((isset($valueUnknownWords["uTranslation"])) && ($valueUnknownWords["uTranslation"] != NULL))) {
                                    $this->saveUserTranslationData($row['mainID'], $valueUnknownWords["gTranslationLNG"], $valueUnknownWords["gTranslation"], (isset($valueUnknownWords["uTranslation"]) ? $valueUnknownWords["uTranslation"] : NULL));
                                }
                                break;
                            }
                        }
                    }
                }

                if (isset($checkedSentencesArray)) {
                    if (!(in_array($valueUnknownWords['sID'], $checkedSentencesArray))) {
                        $checkedSentencesArray[] = $valueUnknownWords['sID'];
                    }
                } else {

                    $checkedSentencesArray[] = $valueUnknownWords['sID'];
                }
            }

            if ((isset($checkedSentencesArray)) && ($checkedSentencesArray <> NULL)) {
                foreach ($checkedSentencesArray as $sID) {
                    $this->_selectSentence($sID);
                }
            }
        }
    }


    // CHECKED SENTENCE SAVING 
    function _selectSentence($sID) {

        $this->db->from('checkedUserMovieSentence');
        $this->db->where('SentenceMovieID', $sID);
        $this->db->where('Language', $this->session->userdata('user_translation_lang'));
        $this->db->where('UserInsertionID', $this->session->userdata('user_id'));
        $query = $this->db->get();

        if ($query->num_rows() == 0) {

            $this->db->set('SentenceMovieID', $sID);
            $this->db->set('Language', $this->session->userdata('user_translation_lang'));
            $this->db->set('UserInsertionID', $this->session->userdata('user_id'));
            $this->db->insert('checkedUserMovieSentence');
        }
    }

    // DELETE SUBS
    function deleteSubs($MovieID) {

        $this->db->where('connUserMovie.ID', $MovieID);
        $this->db->where('connUserMovie.UserInsertionID', $this->session->userdata('user_id'));
        $this->db->delete('connUserMovie');
    }

    function getNumberOfRows($tableName, $conditionsArray) {

        $queryA = $this->db->get_where($tableName, $conditionsArray);

        $returnArray['numROWS'] = $queryA->num_rows();

        foreach ($query->result_array() as $row) {
            $returnArray['ROWS'][] = $row;
        }

        return $returnArray;
    }

    function saveUserTranslationData($connWSID, $tLang, $tWordGoogle = NULL, $tWordUser = NULL) {

        if (($tWordGoogle != NULL) OR ($tWordUser != NULL)) {

            if ($tWordGoogle != NULL) {
                $tWordGoogleID = $this->saveTranslation("Google", $tLang, $tWordGoogle);
                if (is_numeric($tWordGoogleID)) {
                    $this->db->set('GoogleTranslationID', $tWordGoogleID);
                }
            }

            if ($tWordUser != NULL) {
                $tWordUserID = $this->saveTranslation("User", $tLang, $tWordUser);
                if (is_numeric($tWordUserID)) {
                    $this->db->set('UserTranslationID', $tWordUserID);
                }
            }

            $this->db->set('connWSID', $connWSID);
            $this->db->set("TranslationLanguage", $tLang);
            $this->db->set('UserInsertionID', $this->session->userdata('user_id'));

            $this->db->select('*,connWordTranslation.ID AS MainID');
            $this->db->from("connWordTranslation");
            $this->db->where("connWordTranslation.connWSID", $connWSID);
            $this->db->where("connWordTranslation.TranslationLanguage", $tLang);
            $this->db->where('connWordTranslation.UserInsertionID', $this->session->userdata('user_id'));

            // TranslationLanguage
            $query = $this->db->get();

            if ($query->num_rows() == 0) {
                $this->db->insert('connWordTranslation');
            } else {
                $firstOne = FALSE;

                foreach ($query->result_array() as $row) {
                    if ($firstOne == FALSE) {
                        $this->db->where('ID', $row['MainID']);
                        $this->db->update('connWordTranslation');
                    } else {
                        // zmaze ak je tam viac zaznamov pretoze potrebujeme iba jeden
                        $this->db->where('ID', $row['MainID']);
                        $this->db->delete('connWordTranslation');
                    }
                }
            }
        }
    }

    function saveTranslation($Kind, $TranslationLanguage, $Translation) {

        $TableName = 'Data' . $Kind . 'Translation';

        $this->db->from($TableName);
        $this->db->where("TranslationLanguage", $TranslationLanguage);
        $this->db->where("Translation", $Translation);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            $this->db->set("TranslationLanguage", $TranslationLanguage);
            $this->db->set("Translation", $Translation);
            $this->db->set('UserInsertionID', $this->session->userdata('user_id'));
            $this->db->insert($TableName);

            return $this->db->insert_id();
        } else {
            $row = $query->row_array();
            return $row['ID'];
        }
    }

    // Funkcia uklada spojenie uzivatel a slovo a parametre tj. ci je zname alebo nezname
    function saveCheckedWord($WordID, $Unknown) {

        if (is_numeric($Unknown)) {

            if ($Unknown == 1) {

                // IF word was marked
                $this->db->set('Unknown', TRUE);
            } else {

                $this->db->set('Unknown', FALSE);
            }

            // Chek if exist to decide if update or insert
            $this->db->where('WordID', $WordID);
            $this->db->where('UserInsertionID', $this->session->userdata('user_id'));
            $this->db->where('Language', $this->session->userdata('user_translation_lang'));
            $query = $this->db->get('checkedUserWord');
            ;

            if ($query->num_rows() > 0) {

                $row = $query->row();

                if ($row->Unknown != $Unknown) {

                    // UPDATE 
                    $this->db->where('WordID', $WordID);
                    $this->db->where('UserInsertionID', $this->session->userdata('user_id'));
                    $this->db->where('Language', $this->session->userdata('user_translation_lang'));
                    $this->db->update('checkedUserWord');
                    return TRUE;
                }
            } else {

                // INSERT
                $this->db->set('WordID', $WordID);
                $this->db->set('UserInsertionID', $this->session->userdata('user_id'));
                $this->db->set('Language', $this->session->userdata('user_translation_lang'));
                $this->db->insert('checkedUserWord');
                return TRUE;
            }
        }

        return false;
    }

    // funkcia uklada spojenie uzivatel a slovo a parametre tj. ci je zname alebo nezname
    function saveCheckedWord_v1($WordID, $Unknown, $Update_checkedID = NULL) {

        if (is_numeric($Unknown)) {

            // Set Word ID
            $this->db->set('WordID', $WordID);

            if ($Unknown == 1) {
                // IF word was marked
                $this->db->set('Unknown', TRUE);
            } else {

                $this->db->set('Unknown', FALSE);
            }

            if (($Update_checkedID != NULL) && (is_numeric($Update_checkedID))) {

                $this->db->where('ID', $Update_checkedID);
                $this->db->where('Language', $this->session->userdata('user_translation_lang'));
                $this->db->update('checkedUserWord');
                return TRUE;
            } elseif ($Update_checkedID == NULL) {

                $this->db->set('UserInsertionID', $this->session->userdata('user_id'));
                $this->db->set('Language', $this->session->userdata('user_translation_lang'));
                $this->db->insert('checkedUserWord');
                return TRUE;
            }
        }

        return false;
    }

    function checkKnownWord($wordTEXT) {

        $this->db->select('checkedUserWord.ID AS KnowID,checkedUserWord.Unknown ID AS Unknown');
        $this->db->from('DataWords');
        $this->db->where('DataWords.UserInsertionID', $this->session->userdata('user_id'));
        $this->db->where('DataWords.Word', $wordTEXT);
        $this->db->join('checkedUserWord', 'DataWords.ID = checkedUserWord.WordID');
        $this->db->where('checkedUserWord.Unknown', TRUE);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['Unknown'];
        } else {
            return 0;
        }
    }

    function Insert_MovieSubs($data) {

        $LNG = $data['LNG'];
        $Moviename = $data['Moviename'];

        $file_content = addslashes(read_file($data['upload_data']['full_path']));

        $query_MovieCheck = $this->db->get_where('DataMovies', array('Language' => $LNG, 'FileName' => $data['upload_data']['file_name'], 'FileType' => $data['upload_data']['file_type'], 'FileSize' => $data['upload_data']['file_size'], 'FileContent' => $file_content), 1);

        if ($query_MovieCheck->num_rows() == 0) {

            $insertDataMovies = array('MovieName' => $Moviename, 'Language' => $LNG, 'FileName' => $data['upload_data']['file_name'], 'FileType' => $data['upload_data']['file_type'], 'FileSize' => $data['upload_data']['file_size'], 'FileContent' => $file_content, 'UserInsertionID' => $this->session->userdata('user_id'));

            $this->db->set($insertDataMovies);
            $this->db->insert('DataMovies');
            $ID_Movie = $this->db->insert_id();
        } else {
            $row = $query_MovieCheck->row_array();
            $ID_Movie = $row['ID'];
        }

        $query_connUserMovieCheck = $this->db->get_where('connUserMovie', array('MovieID' => $ID_Movie, 'UserInsertionID' => $this->session->userdata('user_id')), 1);

        if ($query_connUserMovieCheck->num_rows() == 0) {

            $this->db->set(array('MovieID' => $ID_Movie, 'UserInsertionID' => $this->session->userdata('user_id')));
            $this->db->insert('connUserMovie');
            $FunctionResult = $this->db->insert_id();

            // Upload / Download from Database - http://www.php-mysql-tutorial.com/wikis/mysql-tutorials/uploading-files-to-mysql-database.aspx
            $file_ID = $this->db->insert_id();
            $file_data = read_file_by_line($data['upload_data']['full_path']);

            while (list($row_id, $content) = each($file_data)) {
                if (is_numeric($content)) {

                    $Line_Number = $content;
                    $Time_Duration = current($file_data);

                    $Whole_Sentence = next($file_data);
                    $line_content = next($file_data);

                    while ($line_content <> '') {
                        $Whole_Sentence .= ' ' . $line_content;
                        $line_content = next($file_data);
                    }

                    $Whole_Sentence = divide_text_to_sentences($Whole_Sentence);

                    if (!is_null($Whole_Sentence)) {
                        foreach ($Whole_Sentence as $Sentence_id => $Sentence) {

                            $Sentence = trim(preg_replace('/^[-]/', '', trim($Sentence)));

                            if ($Sentence <> '') {

                                $contentA = array("Line_number" => $Line_Number, "Time_Duration" => $Time_Duration, "Whole_Sentence" => $Sentence, "Words" => get_words_of_sentence($Sentence));

                                unset($rValues);

                                $query = $this->db->get_where('DataSentences', array('Sentence' => $contentA['Whole_Sentence']), 1);

                                if ($query->num_rows() == 0) {

                                    $this->db->set(array('Language' => $LNG, 'Sentence' => $contentA['Whole_Sentence'], 'UserInsertionID' => $this->session->userdata('user_id')));
                                    $this->db->insert('DataSentences');
                                    $ID_Sentence = $this->db->insert_id();

                                    $this->db->set(array('MovieID' => $ID_Movie, 'SentenceID' => $ID_Sentence, 'Time' => $contentA['Time_Duration'], 'UserInsertionID' => $this->session->userdata('user_id')));
                                    $this->db->insert('connSentenceMovie');

                                    foreach ($contentA['Words'] as $Word_id => $Word) {

                                        if ($Word <> '') {

                                            $queryA = $this->db->get_where('DataWords', array('Word' => $Word, 'Language' => $LNG), 1);

                                            if ($queryA->num_rows() == 0) {

                                                $this->db->set(array('Language' => $LNG, 'Word' => $Word, 'UserInsertionID' => $this->session->userdata('user_id')));
                                                $this->db->insert('DataWords');
                                                $ID_Word = $this->db->insert_id();
                                            } else {
                                                $row = $queryA->row_array();
                                                $ID_Word = $row['ID'];
                                            }

                                            $queryA = $this->db->get_where('connWordSentence', array('SentenceID' => $ID_Sentence, 'WordID' => $ID_Word), 1);

                                            if ($queryA->num_rows() == 0) {

                                                $this->db->set(array('SentenceID' => $ID_Sentence, 'WordID' => $ID_Word, 'UserInsertionID' => $this->session->userdata('user_id')));
                                                $this->db->insert('connWordSentence');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    unset($Whole_Sentence);
                    unset($Line_Number);
                    unset($Time_Duration);
                    unset($Words);
                }
            }

            unlink($data['upload_data']['full_path']);

        } else {

            unset($FunctionResult);
            $FunctionResult = $this->lang->line('moviealreadyexist');
        }

        delete_files('./uploads/', TRUE);
        return $FunctionResult;
    }

    function CreateMySQL_COMMAND($rValues = NULL) {

        if (!isset($rValues['cCommand'])) {
            $rValues['cCommand'] = 'SELECT';
        }

        if ((!isset($rValues['TBL_NAME'])) && (isset($rValues['Join']))) {
            $rValues['TBL_NAME'] = $rValues['Join'][0]['TBL_NAME'];
        }

        if ($rValues['cCommand'] == 'UPDATE') {
            $sql_query = 'UPDATE ' . $rValues['TBL_NAME'] . ' SET ' . $rValues['UpdateValues'];
        } else {

            if (!isset($rValues['cColumns'])) {
                $rValues['cColumns'] = '*';
            }

            $sql_query = $rValues['cCommand'] . ' ' . $rValues['cColumns'] . ' FROM ' . $rValues['TBL_NAME'];

            if (isset($rValues['Join'])) {

                foreach ($rValues['Join'] as $LeftJoinValue) {

                    $sql_query = $sql_query . ' JOIN ' . $LeftJoinValue['JOIN_TBL_NAME'];
                    $sql_query = $sql_query . ' ON ' . $rValues['TBL_NAME'] . '.' . $LeftJoinValue['Field_Name'] . '=' . $LeftJoinValue['JOIN_TBL_NAME'] . '.' . $LeftJoinValue['JOIN_Field_Name'];
                }
            }
        }

        $sql_query = $sql_query . ' WHERE ';

        $sql_query = $sql_query . '(';

        if (isset($rValues['Condition'])) {
            if (is_array($rValues['Condition'])) {

                foreach ($rValues['Condition'] as &$value) {

                    unset($ThisLogicalOperator);

                    $sql_query = $sql_query . $value['Name'];

                    if (isset($value['Operator'])) {
                        $sql_query = $sql_query . $value['Operator'];
                    } else {
                        $sql_query = $sql_query . '=';
                    }

                    if (is_string($value['Value'])) {
                        $sql_query = $sql_query . '"' . $value['Value'] . '" ';
                    } else {
                        $sql_query = $sql_query . $value['Value'] . ' ';
                    }

                    if (isset($rValues['cConditionLogicalOperators'])) {
                        $ThisLogicalOperator = $rValues['cConditionLogicalOperators'] . ' ';
                    } else {

                        if (isset($value['LogicalOperator'])) {
                            $ThisLogicalOperator = $value['LogicalOperator'] . ' ';
                        } else {
                            $ThisLogicalOperator = 'AND';
                        }
                    }

                    $sql_query = $sql_query . $ThisLogicalOperator . ' ';
                }

                $sql_query = substr($sql_query, 0, -(2 + strlen($ThisLogicalOperator)));
            } else {

                if ($rValues['Condition'] <> '') {
                    $sql_query = $sql_query . $rValues['Condition'];
                }
            }
        }

        if (($rValues['cCommand'] <> 'UPDATE') && ($rValues['cCommand'] <> 'INSERT')) {
            if ((isset($rValues['Condition'])) && ($rValues['Condition'] <> NULL)) {
                $sql_query = $sql_query . ' AND '; //'rOnOff=TRUE';
            }

            if (isset($rValues['Join'])) {

                $sql_query = $sql_query . $rValues['TBL_NAME'] . '.' . 'rOnOff=TRUE AND ';

                foreach ($rValues['Join'] as $LeftJoinValue) {
                    $sql_query = $sql_query . $LeftJoinValue['JOIN_TBL_NAME'] . '.' . 'rOnOff=TRUE';
                }
            } else {
                $sql_query = $sql_query . 'rOnOff=TRUE';
            }
        }

        $sql_query = $sql_query . ')';

        if ((isset($rValues['OrderBy'])) && ($rValues['OrderBy'] <> '')) {
            $sql_query = $sql_query . ' ORDER BY ' . $rValues['OrderBy'];
        }

        if ((isset($rValues['cLimit'])) && ($rValues['cLimit'] <> '')) {
            $sql_query = $sql_query . ' LIMIT ' . $rValues['cLimit'];
        }

        $sql_query = $sql_query . ';';

        return $sql_query;
    }

    function DropTBL_SQL($rValues = NULL) {
        $sql_command = '';
        $sql_command = 'DROP TABLE IF EXISTS ' . $rValues['Table_Name'] . ';';
        return $sql_command;
    }

    function CreateTBL_SQL($rValues = NULL) {

        global $TblDataField;

        $this->rLoadArray_TBL_Properties($rValues['Table_Name']);
        $sql_command = '';

        $sql_command.='CREATE TABLE ' . $rValues['Table_Name'] . ' (';

        foreach ($GLOBALS['TblDataField'][$rValues['Table_Name']] as $NULLkey => $NULLvalue) {

            $sql_command = $sql_command . $NULLvalue['Field_Name'] . ' ' . $NULLvalue['Field_Type'];

            if (($NULLvalue['Field_Type'] == 'VARCHAR') || ($NULLvalue['Field_Type'] == 'INT')) {
                $sql_command = $sql_command . '(';
                if ($NULLvalue['Field_Size'] <> NULL) {
                    $sql_command = $sql_command . $NULLvalue['Field_Size'];
                }
                $sql_command = $sql_command . ')';
            }

            if ((isset($NULLvalue['Unsigned'])) && ($NULLvalue['Unsigned'] == True)) {
                $sql_command = $sql_command . ' UNSIGNED ';
            }

            if ((isset($NULLvalue['Field_DefaultValue'])) && ($NULLvalue['Field_DefaultValue'] <> NULL)) {

                if (($NULLvalue['Field_Type'] == "VARCHAR") || ($NULLvalue['Field_Type'] == "TEXT")) {
                    $sql_command = $sql_command . ' DEFAULT "' . $NULLvalue['Field_DefaultValue'] . '"';
                } else {
                    $sql_command = $sql_command . ' DEFAULT ' . $NULLvalue['Field_DefaultValue'];
                }
            }

            if ((isset($NULLvalue['Field_Comment'])) && ($NULLvalue['Field_Comment'] <> NULL)) {
                $sql_command = $sql_command . ' COMMENT \'' . $NULLvalue['Field_Comment'] . '\'';
            }

            $sql_command = $sql_command . ',';

            if ((isset($NULLvalue['Value'])) && ($NULLvalue['Value'] == '')) {
                $field_rowdata[$NULLkey]['Value'] = NULL;
            }
        }

        $sql_command = $sql_command . ' PRIMARY KEY (ID)) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_slovak_ci;';

        return $sql_command;
    }

    function rLoadArray_TBL_Properties($Table_Name) {

        unset($GLOBALS['TblDataField']);

        // Hlavička tabuľky

        $GLOBALS['TblDataField'][$Table_Name][0] = array('Field_Name' => "ID",
            'Field_Type' => "SERIAL",
            'Field_Size' => NULL,
            'Field_DefaultValue' => NULL,
            'Field_Comment' => "ID");

        switch ($Table_Name) {
            case "DataArticles":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Identifier",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Article identification");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Language",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Color",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 12,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Color of the Article");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Title",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Title of Article");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "ArticleText",
                    'Field_Type' => "TEXT",
                    'Field_Size' => NULL,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Text of Article");
                break;
            case "DataWords":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Language",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Word",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Word");

                break;
            case "DataSentences":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Language",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Sentence",
                    'Field_Type' => "TEXT",
                    'Field_Size' => NULL,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Sentence");
                break;
            case "DataMovies":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "MovieName",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "The name of movie");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Language",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "FileName",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Filename");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "FileType",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "File type");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "FileSize",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Field_Comment' => "Size of file");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "FileContent",
                    'Field_Type' => "MEDIUMBLOB",
                    'Field_Comment' => "File Content");

                break;
            case "DataGoogleTranslation":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "TranslationLanguage",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Translation",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => 'know',
                    'Field_Comment' => "Translation by Google");
                break;
            case "DataUserTranslation":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "TranslationLanguage",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Translation",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => 'know',
                    'Field_Comment' => "Translation by user");
                break;
            case "checkedUserMovieSentence":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "SentenceMovieID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of user");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Language",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                break;
            case "checkedUserWord":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "WordID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of word");
                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Unknown",
                    'Field_Type' => "BOOL",
                    'Field_Size' => NULL,
                    'Field_DefaultValue' => "FALSE",
                    'Field_Comment' => "Is word unknown");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Language",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");


                break;
            case "connWordTranslation":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "connWSID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of connection WordSentence");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "TranslationLanguage",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Language");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "GoogleTranslationID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of GoogleTranslation");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "UserTranslationID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of UserTranslation");
                break;
            case "connUserMovie":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "MovieID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of sentence");
                break;
            case "DataLoginUsers":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "facebook_auth_id",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "the unique id of the user at the provider");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "facebook_auth_username",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "the name of the user at the provider");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "facebook_auth_token",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "the permanent access token of the user at the provider");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "facebook_auth_token_verifier",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "only needed if we use dynamic callback URLs");
                break;
            case "connUserSettings":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "settingType",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "the unique id of the user at the provider");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "settingValue",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "the name of the user at the provider");
                break;
            case "connSentenceMovie":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "MovieID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of sentence");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "SentenceID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of movie");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "Time",
                    'Field_Type' => "VARCHAR",
                    'Field_Size' => 255,
                    'Field_DefaultValue' => NULL,
                    'Field_Comment' => "Time of happening");

                break;
            case "connWordSentence":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "WordID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of word");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "SentenceID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of sentence");

                break;
            case "connWSUser":

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "connWSID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of connection WordSentence");

                $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "UserID",
                    'Field_Type' => "BIGINT",
                    'Field_Size' => NULL,
                    'Unsigned' => True,
                    'Field_DefaultValue' => 0,
                    'Field_Comment' => "ID of sentence");
                break;
            case "DEMO":
                break;
        }

        // Päta Tabulky
        $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "UserInsertionID",
            'Field_Type' => "BIGINT",
            'Field_Size' => NULL,
            'Unsigned' => True,
            'Field_DefaultValue' => NULL,
            'Field_Comment' => "Užívateľ ktorý to vytvoril");

        $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "DateTimeOfInsertion",
            'Field_Type' => "DATETIME",
            'Field_Size' => NULL,
            'Field_DefaultValue' => NULL,
            'Field_Comment' => "Dátum a čas vytvorenia");

        $GLOBALS['TblDataField'][$Table_Name][] = array('Field_Name' => "OnOff",
            'Field_Type' => "BOOL",
            'Field_Size' => NULL,
            'Field_DefaultValue' => "TRUE",
            'Field_Comment' => "Vypnuť_Zapnuť záznam");
    }

}
