<?php

class Ajax extends CI_Controller {

    function get_print_show($MovieID) {

        $MovieData = $this->App_model->_get_movie_info($MovieID);
        $dataView['moviename'] = $this->lang->line('index_printpage_label') . $MovieData['MovieName'];
        $dataView['content'] = $this->App_model->showUnknownWords($MovieID);
        $this->load->view('printList_view', $dataView);
    }

    function get_sentence($MovieID, $SentenceID = NULL) {

        $sentencesCounter = 0;

        if ((isset($_REQUEST['WordsData'])) && ($_REQUEST['WordsData'])) {
            $this->App_model->_selectWords(convert_words_array_keys($this->input->post('WordsData')));
        }

        if ((isset($_REQUEST['lng'])) && ($_REQUEST['lng'] <> '') && ($_REQUEST['lng'] <> null)) {
            $currentLNG = $this->input->post('lng');
        } else {
            $currentLNG = $this->session->userdata('user_translation_lang'); // $this->App_model->_getUserSettingValue('userlang');
        }

        $this->db->select('connSentenceMovie.ID,DataSentences.Sentence');
        $this->db->from('connSentenceMovie');
        $this->db->where('connSentenceMovie.MovieID', $MovieID);

        $this->db->join('connUserMovie', 'connUserMovie.MovieID = connSentenceMovie.MovieID');
        $this->db->where('connUserMovie.UserInsertionID', $this->session->userdata('user_id'), FALSE);

        if ((isset($SentenceID)) && ($SentenceID <> NULL)) {
            $this->db->where('connSentenceMovie.ID >', $SentenceID);
        } else {
            $this->db->join('checkedUserMovieSentence', 'checkedUserMovieSentence.SentenceMovieID = connSentenceMovie.ID AND checkedUserMovieSentence.Language="' . $this->session->userdata('user_translation_lang') . '"', 'left');
            $this->db->where('checkedUserMovieSentence.ID IS ', "NULL", FALSE);
        }

        $this->db->join('DataSentences', 'DataSentences.ID = connSentenceMovie.SentenceID');

        $this->db->order_by("LENGTH(DataSentences.Sentence)", "DESC");

        $query = $this->db->get();

        if ($query->num_rows() != 0) {

            //Creates XML string and XML document using the DOM
            $dom = new DomDocument('1.0', 'UTF-8');
            $x_sentences = $dom->appendChild($dom->createElement('sentences'));

            $sentencesNumber = $this->App_model->_getUserSettingValue('NumberOfSentences');

            if ($sentencesNumber == NULL) {
                $sentencesNumber = 5;
                $this->App_model->_setUserSetting('NumberOfSentences', $sentencesNumber);
            }

            foreach ($query->result_array() as $row) {

                /*
                 * Tu sa zistuje ci slová vo vete uz náhodou užívateľ nevidel. Ak nájde vetu v ktorej všetky slová
                 * už užívateľ videl vetu nezobrazí.
                 * Ak uzívatel videl vsetky slova vo vete vysledkom bude zobrazenie riadku ak nevidel alebo videl iba niektore select neukaze nic cize ziadny riadok.
                 */

                $this->db->select('connWordSentence.ID');
                $this->db->from('connWordSentence');
                $this->db->where('connWordSentence.SentenceID', $row['ID'], FALSE);

                $this->db->join('checkedUserWord', 'checkedUserWord.wordid=connWordSentence.wordid AND checkedUserWord.UserInsertionID=' . $this->session->userdata('user_id') . ' AND checkedUserWord.Language="' . $this->session->userdata('user_translation_lang') . '"', 'LEFT');
                $this->db->having('COUNT(connWordSentence.SentenceID)=COUNT(checkedUserWord.Language)', '', FALSE);

                $queryCheckIfWordsAreUnknown = $this->db->get();

                if ($queryCheckIfWordsAreUnknown->num_rows() == 0) {

                    $x_sentence = $x_sentences->appendChild($dom->createElement('sentence'));
                    $x_sentenceID = $x_sentence->appendChild($dom->createAttribute('id'));
                    $x_sentenceID->appendChild($dom->createTextNode($row['ID']));
                    $x_waitingsentences = $x_sentence->appendChild($dom->createAttribute('waitingsentences'));
                    $x_waitingsentences->appendChild($dom->createTextNode((int) $query->num_rows() - (++$sentencesCounter)));

                    $actualSentenceID = $row['ID'];

                    $this->db->select('connWordSentence.WordID AS ID,DataWords.Word AS Word,checkedUserWord.Unknown AS Unknown');
                    $this->db->from('connWordSentence');
                    $this->db->where('connWordSentence.SentenceID', $row['ID'], FALSE);
                    $this->db->join('DataWords', 'DataWords.ID = connWordSentence.WordID');
                    $this->db->join('checkedUserWord', 'DataWords.ID = checkedUserWord.WordID AND checkedUserWord.Language="' . $this->session->userdata('user_translation_lang') . '"  AND checkedUserWord.UserInsertionID=' . $this->session->userdata('user_id'), 'LEFT');

                    $queryWords = $this->db->get();

                    // prebehne každé slovo vo vete pomocou rozdelenia texu v aktualnej vete
                    foreach (get_words_of_sentence($row['Sentence'], "SHOW") as $skey => $sWords) {

                        $sWords = trim($sWords);

                        // Write to XLS

                        foreach ($queryWords->result_array() as $rowWords) {
                            if (strtolower($sWords) == strtolower($rowWords['Word'])) {
                                if ($rowWords['Unknown'] == TRUE) {
                                    $valuesToAdd['unknown'] = 1;
                                } else {
                                    $valuesToAdd['unknown'] = 0;
                                }

                                $valuesToAdd['ID'] = $rowWords['ID'];

                                // Get translations
                                $this->db->select('connWordSentence.SentenceID AS SentenceID,DataGoogleTranslation.Translation AS gTranslation,DataUserTranslation.Translation AS uTranslation');
                                $this->db->from('connWordSentence');
                                $this->db->where('connWordSentence.WordID', $rowWords['ID']);
                                $this->db->join('connWordTranslation', 'connWordTranslation.connWSID = connWordSentence.ID AND connWordTranslation.TranslationLanguage="' . $currentLNG . '"'); // AND connWordTranslation.UserInsertionIDa='.$this->session->userdata('user_id')
                                $this->db->join('DataGoogleTranslation', 'DataGoogleTranslation.ID=connWordTranslation.GoogleTranslationID AND DataGoogleTranslation.TranslationLanguage=connWordTranslation.TranslationLanguage', 'LEFT');
                                $this->db->join('DataUserTranslation', 'DataUserTranslation.ID=connWordTranslation.UserTranslationID AND DataUserTranslation.TranslationLanguage=connWordTranslation.TranslationLanguage', 'LEFT');
                                $queryTranslations = $this->db->get();

                                unset($rowTranslations);

                                if ($queryTranslations->num_rows() != 0) {
                                    foreach ($queryTranslations->result_array() as $rowTranslations) {
                                        /*
                                         * V pripade ak je preklad pre tento konkretny kontext (slovo vo vete) už raz užívateľom zadaný
                                         * znamená to že užívateľ nebol spokojný s prekladom Googlu a vložil preklad sám a preto systém
                                         * v tomto kroku vloží preklad ktorý bol zadaný užívateľom.Ak existuje iná logika prosím zmeniť.
                                         */

                                        if (($rowTranslations['SentenceID'] == $row['ID']) && (isset($rowTranslations['uTranslation']) && ($rowTranslations['uTranslation'] != NULL))) {
                                            $valuesToAdd['gTranslationLNG'] = $currentLNG;
                                            $valuesToAdd['gTranslation'] = $rowTranslations['uTranslation'];
                                        } else {
                                            if (isset($rowTranslations['gTranslation']) && ($rowTranslations['gTranslation'] != NULL)) {
                                                $valuesToAdd['gTranslationLNG'] = $currentLNG;
                                                $valuesToAdd['gTranslation'] = $rowTranslations['gTranslation'];
                                            } else {
                                                $valuesToAdd['gTranslationLNG'] = NULL;
                                                $valuesToAdd['gTranslation'] = NULL;
                                            }
                                        }

                                        // TODO: je potrebne dorobit zobrazovanie viacerych prekladov , momentalne je zapnuty iba jeden
                                        break;
                                    }
                                }
                                break;
                            }
                        }

                        if (!isset($valuesToAdd['ID'])) {
                            $valuesToAdd['ID'] = 'N';
                            $valuesToAdd['unknown'] = 0;
                            $valuesToAdd['gTranslationLNG'] = NULL;
                            $valuesToAdd['gTranslation'] = NULL;
                        }

                        if (!isset($valuesToAdd['gTranslation'])) {
                            $valuesToAdd['gTranslation'] = NULL;
                        }

                        if (!isset($valuesToAdd['gTranslationLNG'])) {
                            $valuesToAdd['gTranslationLNG'] = NULL;
                        }

                        // Write to XLS
                        $x_word = $x_sentence->appendChild($dom->createElement('word'));

                        $x_unknown = $x_word->appendChild($dom->createElement('unknown'));
                        $x_unknown->appendChild($dom->createTextNode($valuesToAdd['unknown']));
                        $x_ID = $x_word->appendChild($dom->createElement('id'));
                        $x_ID->appendChild($dom->createTextNode($valuesToAdd['ID']));
                        $x_wordtext = $x_word->appendChild($dom->createElement('wordtext'));
                        $x_wordtext->appendChild($dom->createTextNode($sWords));

                        if (($valuesToAdd['gTranslation'] != NULL) && ($valuesToAdd['unknown'] == 1)) {
                            // if(($valuesToAdd['gTranslation'] != NULL)){
                            $x_gTranslation = $x_word->appendChild($dom->createElement('gtranslation'));
                            $x_gTranslation->appendChild($dom->createTextNode($valuesToAdd['gTranslation']));
                            $x_gTranslationLNG = $x_word->appendChild($dom->createElement('gtranslationlng'));
                            $x_gTranslationLNG->appendChild($dom->createTextNode($valuesToAdd['gTranslationLNG']));
                        }

                        unset($valuesToAdd);
                    }

                    if (--$sentencesNumber <= 0) {
                        break;
                    }
                } else {

                    // Oznaci vetu v ktorej su vsetky zname slova ako videnu
                    $this->App_model->_selectSentence($row['ID']);
                }
            }

            if ($dom->getElementsByTagName('sentence')->item(0) <> NULL) {
                $dom->formatOutput = true; // set the formatOutput attribute of
                header("Content-type: text/xml");
                echo $dom->saveXML();
            } else {
                echo site_url('site/words_list/' . $MovieID);
            }
        } else {
            echo site_url('site/words_list/' . $MovieID);
        }
    }
}