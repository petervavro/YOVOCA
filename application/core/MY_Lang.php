<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// CodeIgniter i18n library by Jérôme Jaglale
// http://maestric.com/en/doc/php/codeigniter_i18n
// version 10 - May 10, 2012

class MY_Lang extends CI_Lang {

	/**************************************************
	 configuration
	***************************************************/

	// languages
	var $languages = array(
            'en' => 'english',
            'sk' => 'slovak'
	);

	// special URIs (not localized)
	var $special = array (
		""
	);
	
	// where to redirect if no language in URI
	var $default_uri = ''; 

	/**************************************************/
	
	
	function __construct()
	{
		parent::__construct();		
		
		global $CFG;
		global $URI;
		global $RTR;
		
		$segment = $URI->segment(1);
		
		if (isset($this->languages[$segment]))	// URI with language -> ok
		{
			$language = $this->languages[$segment];
			$CFG->set_item('language', $language);

		}
		else if($this->is_special($segment)) // special URI -> no redirect
		{
			return;
		}
		else	// URI without language -> redirect to default_uri
		{
			// set default language
			$CFG->set_item('language', $this->languages[$this->default_lang()]);

			// redirect
			header("Location: " . $CFG->site_url($this->localized($this->default_uri)), TRUE, 302);
			exit;
		}
	}
	
	// get current language
	// ex: return 'en' if language in CI config is 'english' 
	function lang()
	{
		global $CFG;		
		$language = $CFG->item('language');
		
		$lang = array_search($language, $this->languages);
		if ($lang)
		{
			return $lang;
		}
		
		return NULL;	// this should not happen
	}
	
	function is_special($uri)
	{
		$exploded = explode('/', $uri);
		if (in_array($exploded[0], $this->special))
		{
			return TRUE;
		}
		if(isset($this->languages[$uri]))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	function switch_uri($lang)
	{
		$CI =& get_instance();

		$uri = $CI->uri->uri_string();
		if ($uri != "")
		{
			$exploded = explode('/', $uri);
			if($exploded[0] == $this->lang())
			{
				$exploded[0] = $lang;
			}
			$uri = implode('/',$exploded);
		}
		return $uri;
	}
	
	// is there a language segment in this $uri?
	function has_language($uri)
	{
		$first_segment = NULL;
		
		$exploded = explode('/', $uri);
		if(isset($exploded[0]))
		{
			if($exploded[0] != '')
			{
				$first_segment = $exploded[0];
			}
			else if(isset($exploded[1]) && $exploded[1] != '')
			{
				$first_segment = $exploded[1];
			}
		}
		
		if($first_segment != NULL)
		{
			return isset($this->languages[$first_segment]);
		}
		
		return FALSE;
	}
	
	// default language: first element of $this->languages
	function default_lang()
	{
		foreach ($this->languages as $lang => $language)
		{
			return $lang;
		}
	}
	
	// add language segment to $uri (if appropriate)
	function localized($uri)
	{
		if($this->has_language($uri)
				|| $this->is_special($uri)
				|| preg_match('/(.+)\.[a-zA-Z0-9]{2,4}$/', $uri))
		{
			// we don't need a language segment because:
			// - there's already one or
			// - it's a special uri (set in $special) or
			// - that's a link to a file
		}
		else
		{
			$uri = $this->lang() . '/' . $uri;
		}
		
		return $uri;
	}


/* Added by PV */
        
    // return whole uri for creating link or redirect 
    function getURIwLNG($lang_code = null) {

        $CI = & get_instance();
        $uri = $CI->uri->uri_string();

        // if is uri empty and lang_code is other then null
        if (!$uri) {

            if ($lang_code) {
                $uri = $lang_code . '/';
            } else {
                return false;
            }
        } else {

            $exploded = explode('/', $uri);

            if ($lang_code) {

                if (strlen($exploded[0]) == 2) {

                    // If we have an URI with a lang --> es/controller/method
                    if ($exploded[0] != $lang_code) {
                        $exploded[0] = $lang_code;
                    } else {
                        return false;
                    }
                } else {
                    // If we have an URI without lang --> /controller/method
                    // "Default language"
                    $exploded[0] = $lang_code . "/" . $exploded[0];
                }
            } else {

                unset($exploded[0]);
                // $lang_code = default_lang();
            }

            $uri = implode('/', $exploded);
        }

        return $uri;
    }

    // get user language from cookies , if not defined check language of browser
    function getUserLanguage() {

        global $IN;
        global $CFG;

        // if a language cookie available get its sanitized info
        $cookie_lang_code = $IN->cookie($CFG->item('cookie_prefix') . $CFG->item('lang_cookie_name'), true);

        if ($cookie_lang_code != '') {
            $language_code = $cookie_lang_code;
        }

        if (!isset($language_code)) {

            // no cookie/URI language code: check browser's languages
            $accept_langs = $IN->server('HTTP_ACCEPT_LANGUAGE');

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
                        $lang = substr($lang, 0, $pos);
                    }

                    if (isset($this->languages[$lang])) {    // if browser language exist -> ok
                        $language_code = $lang;
                        break;
                    }
                }
            }
        }

        if (isset($language_code)) {

            return $language_code;
        } else {

            return null;
        }
    }
    
    function redirectL($lang_code = null) {

        // $CI =& get_instance();
        // $CI->load->library('auth_connect');
        // || ($CI->auth_connect->is_logged_in())
        // (($CI->auth_connect->is_logged_in()) && ( $this->is_special($uri))))

        $uri = $this->getURIwLNG($lang_code);

        if (is_string($uri)) {
            // redirect
            header("Location: " . base_url() . $uri, TRUE, 302);
            exit;
        }
    }
}

/* End of file */
