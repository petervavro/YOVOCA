-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 01, 2013 at 09:51 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yovoca`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkedusermoviesentence`
--

CREATE TABLE IF NOT EXISTS `checkedusermoviesentence` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `SentenceMovieID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of user',
  `Language` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=559 ;

-- --------------------------------------------------------

--
-- Table structure for table `checkeduserword`
--

CREATE TABLE IF NOT EXISTS `checkeduserword` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `WordID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of word',
  `Unknown` tinyint(1) DEFAULT '0' COMMENT 'Is word unknown',
  `Language` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=1423 ;

-- --------------------------------------------------------

--
-- Table structure for table `connsentencemovie`
--

CREATE TABLE IF NOT EXISTS `connsentencemovie` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `MovieID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of sentence',
  `SentenceID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of movie',
  `Time` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Time of happening',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=4138 ;

-- --------------------------------------------------------

--
-- Table structure for table `connusermovie`
--

CREATE TABLE IF NOT EXISTS `connusermovie` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `MovieID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of sentence',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` timestamp NULL DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `connusersettings`
--

CREATE TABLE IF NOT EXISTS `connusersettings` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `settingType` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'the unique id of the user at the provider',
  `settingValue` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'the name of the user at the provider',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `connwordsentence`
--

CREATE TABLE IF NOT EXISTS `connwordsentence` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `WordID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of word',
  `SentenceID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of sentence',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=19957 ;

-- --------------------------------------------------------

--
-- Table structure for table `connwordtranslation`
--

CREATE TABLE IF NOT EXISTS `connwordtranslation` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `connWSID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of connection WordSentence',
  `TranslationLanguage` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `GoogleTranslationID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of GoogleTranslation',
  `UserTranslationID` bigint(20) unsigned DEFAULT NULL COMMENT 'ID of UserTranslation',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `dataarticles`
--

CREATE TABLE IF NOT EXISTS `dataarticles` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `Identifier` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Article identification',
  `Language` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `Color` varchar(12) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Color of the Article',
  `Title` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Title of Article',
  `ArticleText` text COLLATE utf8_slovak_ci COMMENT 'Text of Article',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=28 ;

--
-- Dumping data for table `dataarticles`
--

INSERT INTO `dataarticles` (`ID`, `Identifier`, `Language`, `Color`, `Title`, `ArticleText`, `UserInsertionID`, `DateTimeOfInsertion`, `OnOff`) VALUES
(1, 'expl', 'sk', NULL, 'UČENIE SA NOVÝCH SLOV V CUDZOM JAZYKU KEĎ POZERÁME  FILM ALEBO SERIAL ?<br />AKO NA TO ?', '<h1>Pozeranie cudzojazyčných filmov alebo seriálov a rozširovanie si slovnej zásoby zároveň ?</h1><p>Pozeranie zahraničných filmov a seriálov je vždy príležitosťou pre zdokonalenie svojich schopností v cudzom jazyku. Touto formou trávenia voľného času je napríklad možné učenie sa nových slov, čiže rozširovanie si slovnej zásoby. </p><br /><p>Pomocou týchto stánok sa máte možnosť naučiť neznáme slová použité vo filme ešte pred tým ako film uvidíte a následne počas pozerania filmu nové slová už len precvičovať. V prípade, ak ste film už videli Vám tieto stránky pomôžu nájsť neznáme slová a vety v ktorých sa vyskytli a tak môžete slová precvičovať neskôr.</p><br />', 1, NULL, 1),
(2, 'expl', 'en', NULL, 'LEARNING NEW WORDS IN FOREIGN LANGUAGES WHILE YOU ARE WATCHING A MOVIE ? HOW ?!', '<h1>Watching movies, serials or soap operas in foreign language and improve your vocabulary simultaneously ?</h1><p>One of the ways we like to spend our free time is watching movies, serials or soap operas but many of them are played in foreign languages. To understand them we are using subtitles, but even of that, sometimes is difficult to comprehend all of the dialogues.</p><br /><p>Using this page you can get the translation of every word you do not know from the subtitles and in the meantime, you can get more skilled in a foreign language improving your vocabulary. Remember that to speak more languages than your native one became really an advantage now.</p><br /><p>We are giving you the chance to learn new words from the movies, series or soap operas you want to see, even before you watch them.</p><br /><p><b>It''s really easy and you can start just now!</b></p><br />', 1, NULL, 1),
(3, 'wtf', 'sk', NULL, 'NA ČO TO SLÚŽI ?', '<p>Tieto stránky slúžia ako pomôcka na pohodlné rozširovanie si slovnej zásoby v cudzom jazyku.</p>', 1, NULL, 1),
(4, 'wtf', 'en', NULL, 'WHAT IS THIS FOR ?', '<p>This site is a useful tool for your vocabulary improvement in a foreign language.</p>', 1, NULL, 1),
(5, 'wpf', 'sk', NULL, 'PRE KOHO JE TO URČENÉ ?', '<ul><li>Pozeráte filmy v cudzom jazyku ?</li><li>Pri pozeraní filmu občas narazíte na slová ktorým nerozumiete ?</li><li>Máte záujem zlepšovať sa v cudzom jazyku ?</li></ul><p>Ak je Vaša odpoveď na tieto otázky kladná, ste na správnej adrese, pretože tieto stránky sú určené luďom, ktorí majú záujem zlepšovať sa v cudzích jazykoch.</p>', 1, NULL, 1),
(6, 'wpf', 'en', NULL, 'WHOM IS THIS FOR ?', '<ul><li>Are you watching films in foreign languages?</li><li>Do you find words and phrases you do not understand in this movies?</ li><li>Wouldn''t be nice if you can understand all the dialogues and improve your skills in foreign languages?</ li></ul><p>If you answer is "Yes" to these questions, you`re in the right place!</p>', 1, NULL, 1),
(7, 'wcu', 'sk', NULL, 'KEDY TO VYUŽIJEM ?', '<h1>POZERANIE FILMOV/SERIALOV/TELEVIZIE</h1><p>Pomocou týchto stránkok si neznáme slová pred začiatkom filmu vyhľadáte a následne počas filmu slová už len precvičením hlbšie v pamäti uložíte.</p><br />', 1, NULL, 1),
(8, 'wcu', 'en', NULL, 'WHEN CAN I USE THIS ?', '<h1>WATCHING MOVIES / SERIES / SOAP OPERAS</h1><p>This site will assist you to understand better the subtitles and so the movies, series, soap operas or programs you want to see.</p><br />', 1, NULL, 1),
(9, 'wmj', 'sk', NULL, 'ČO JE VAŠOU ÚLOHOU ?', '<p>Vašou úlohou je jednoduchým kliknutím určiť slová ktorých významu v kontexte vety nerozumiete.</p>', 1, NULL, 1),
(10, 'wmj', 'en', NULL, 'HOW CAN I USE THIS ?', '<p>It''s easy. You just go through the subtitles and click on the words you don''t understand.</p>', 1, NULL, 1),
(11, 'hiw', 'sk', NULL, 'AKO TO FUNGUJE ?', '<ol><li>Je potrebné sa prihlásiť pomocou konta na Facebook-u.</li><li>Vložia sa titulky k filmu, ktorý idete pozerať. </li><li>Systém vám ponúkne vybrané vety z tituliek filmu a Vašou úlohou je označiť slová, ktorím nerozumiete.<div style="padding-top:10px;padding-bottom:10px;width:100%;text-align:center;"><iframe width="560" height="349" src="http://www.youtube.com/embed/6AqwEeBsGWg" frameborder="0" allowfullscreen></iframe></div></li><li>Na konci sa Vám ukáže zoznam vybraných slov s prekladom, ktorý si môžete vytlačiť.</li><li>Slová sa naučte a môžte začať pozerať film, kde práve naučené slová si precvičíte.</li></ol>', 1, NULL, 1),
(12, 'hiw', 'en', NULL, 'HOW DOES IT WORK ?', '<ol><li>You should log into your Facebook account.</li><li>Find the subtitles file on the internet for the movie or whatever program you want to watch.</li><li>Upload that subtitles file to this site.</li><li>Then you go through dialogues, checking sentences and selecting unknown words by clicking on them.<div style="padding-top:10px;padding-bottom:10px;width:100%;text-align:center;"><iframe width="560" height="349" src="http://www.youtube.com/embed/6AqwEeBsGWg" frameborder="0" allowfullscreen></iframe></div></li><li>At the end you will find a list of the selected words with their translations.</li><li>You can print out this list and learn these words.</li><li>And now you are ready to watch your movie or program, understanding and practicing new words!</li></ol>', 1, NULL, 1),
(13, 'ftr', 'sk', NULL, 'VLASTNOSTI SYSTÉMU', '<ul><li>Slová, ktoré Vám systém ukázal si systém ukladá ako slová Vami už videné. Každé videné slovo si systém môže zapamätať nasledovne:<ul><li>Slová, ktoré ste  kliknutím vo vetách označili si systém zapamätá ako slová ktoré nepoznáte, ktoré sú Vám neznáme.</li><li>Tie slová, ktoré ste neoznačili si systém zapamätá ako slová Vám známe, slová ktorým rozumiete a ktorých význam je Vám jasný.</li></ul></li><li>Vaša slovná zásoba je v systéme len jedna, tj. ak dnes v titulkách č.1 označíte niektoré slovo ako neznáme a zajtra v titulkách č.2 toto isté slovo neoznačíte, systém bude toto slovo považovať ako známe a viac slovo nezobrazí v „ZOZNAME NEZNÁMYCH SLOV“ pre žiadne titulky v systéme.</li></ul>', 1, NULL, 1),
(14, 'ftr', 'en', NULL, 'SYSTEM FEATURES', '<p>The system remembers all the words you check and stores them as words you have already seen. Every word you have checked, the system will remember as follows :</p><ul><li>A word in a sentence you selected by clicking on it, the system remembers as a word you do not know, as a word that is unfamiliar to you.</li><li>A word in a sentence you do not selected, the system remembers as a word you know, as a word you understand and so, the word`s mean in the sentence is clear for you.</li></ul>', 1, NULL, 1),
(15, 'abt', 'sk', NULL, 'ČO JE TO ?', '<p>Je to systém, ktorý uchováva Vašu slovnú zásobu v cudzom jazyku, slová ktorým rozumiete a tiež slová, ktorých ekvivalenty v rodnom jazyku nepoznáte.</p>', 1, NULL, 1),
(16, 'abt', 'en', NULL, 'WHAT IS YOVOCA ?', '<p>It is a web system that helps you to learn new words in foreign languages.</p>', 1, NULL, 1),
(17, 'rtxt', 'sk', NULL, 'ČÍTANIE TEXTU', '', 1, NULL, 1),
(18, 'rtxt', 'en', NULL, 'TEXT READING', '', 1, NULL, 1),
(19, 'iart', 'sk', NULL, 'ZAUJÍMAVE ČLÁNKY', '', 1, NULL, 1),
(20, 'iart', 'en', NULL, 'INTERESTING ARTICLES', '', 1, NULL, 1),
(21, 'nws', 'sk', '', 'NOVINKY', 'BETA systému spustená !', 1, NULL, 1),
(22, 'nws', 'en', '', 'NEWS', 'We launch BETA !', 1, NULL, 1),
(23, 'vhts', 'en', '', 'HOW TO START ?', '<iframe width="300" height="200" src="http://www.youtube.com/embed/G6FOgDjF20Q" frameborder="0" allowfullscreen></iframe>', 1, NULL, 1),
(24, 'vhts', 'sk', '', 'AKO ZAČAŤ ?', '<iframe width="300" height="200" src="http://www.youtube.com/embed/G6FOgDjF20Q" frameborder="0" allowfullscreen></iframe>', 1, NULL, 1),
(25, 'vlsf', 'en', '', 'HOW TO LOAD SUBTITLES ?', '<iframe width="300" height="200" src="http://www.youtube.com/embed/pyWLI3WJHzM" frameborder="0" allowfullscreen></iframe>', 1, NULL, 1),
(26, 'vlsf', 'sk', '', 'AKO VLOŽIŤ TITULKY ?', '<iframe width="300" height="200" src="http://www.youtube.com/embed/pyWLI3WJHzM" frameborder="0" allowfullscreen></iframe>', 1, NULL, 1),
(27, 'checkhelp', 'sk', NULL, 'VYSVETLIVKY', '<table>\r\n<tbody>\r\n<tr>\r\n<td class=''selectingplace''><span>Slovo</span>\r\n</td>\r\n<td>známe slovo - neoznačené</td></tr>\r\n<tr>\r\n<td class=''selectingplace''>\r\n<span class="selectedWord">Slovo</span></td>\r\n<td>neznáme slovo - označené</td></tr>\r\n</tbody>\r\n</table>\r\n<br />\r\nKedykoľvek môžete prerušiť tento proces. Systém si pamätuje kde ste skončili a z tohoto miesta budete môcť nabudúce pokračovať.', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `datagoogletranslation`
--

CREATE TABLE IF NOT EXISTS `datagoogletranslation` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `TranslationLanguage` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `Translation` varchar(255) COLLATE utf8_slovak_ci DEFAULT 'know' COMMENT 'Translation by Google',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `dataloginusers`
--

CREATE TABLE IF NOT EXISTS `dataloginusers` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `facebook_auth_id` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'the unique id of the user at the provider',
  `facebook_auth_username` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'the name of the user at the provider',
  `UserDesc` text COLLATE utf8_slovak_ci NOT NULL COMMENT 'User Details',
  `facebook_auth_token` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'the permanent access token of the user at the provider',
  `facebook_auth_token_verifier` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'only needed if we use dynamic callback URLs',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` timestamp NULL DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `datamovies`
--

CREATE TABLE IF NOT EXISTS `datamovies` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `MovieName` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'The name of movie',
  `Language` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `FileName` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Filename',
  `FileType` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'File type',
  `FileSize` bigint(20) DEFAULT NULL COMMENT 'Size of file',
  `FileContent` mediumblob COMMENT 'File Content',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `datasentences`
--

CREATE TABLE IF NOT EXISTS `datasentences` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `Language` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `Sentence` text COLLATE utf8_slovak_ci COMMENT 'Sentence',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=4138 ;

-- --------------------------------------------------------

--
-- Table structure for table `datausertranslation`
--

CREATE TABLE IF NOT EXISTS `datausertranslation` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `TranslationLanguage` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `Translation` varchar(255) COLLATE utf8_slovak_ci DEFAULT 'know' COMMENT 'Translation by user',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `datawords`
--

CREATE TABLE IF NOT EXISTS `datawords` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `Language` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Language',
  `Word` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL COMMENT 'Word',
  `UserInsertionID` bigint(20) unsigned DEFAULT NULL COMMENT 'Užívateľ ktorý to vytvoril',
  `DateTimeOfInsertion` datetime DEFAULT NULL COMMENT 'Dátum a čas vytvorenia',
  `OnOff` tinyint(1) DEFAULT '1' COMMENT 'Vypnuť_Zapnuť záznam',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=4054 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
