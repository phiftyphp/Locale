<?php
namespace Phifty;
/**
  @VERSION 1.1.0

_('en')
_('ja')
_('zh_TW')
_('zh_CN')
_('en_US')
_('fr')
*/

use Exception;

class Locale
{
    const LOCALE_KEY = 'locale';

    const COOKIE_LIFETIME = 2592000;

    public $domain;

    public $localedir;

    public $codeset = 'UTF-8';

    public $current;

    public $langList = array();


    public $defaultLang;

    public function __construct($domain, $localeDir, array $languages = array())
    {
        $this->domain = $domain;
        $this->localedir = $localeDir;
        foreach ($languages as $idx => $localeName) {
            if (is_numeric($idx)) {
                $this->addLanguage($localeName);
            } else {
                $this->addLanguage($idx, $localeName);
            }
        }
    }
    

    public function setDefaultLanguage($lang)
    {
        $this->defaultLang = $lang;
        return $this;
    }



    public function init($overrideLanguage = null)
    {
        $lang = null;
        if ($overrideLanguage)
            $lang = $overrideLanguage;

        if (! $lang && isset($_GET[self::LOCALE_KEY]))
            $lang = $_GET[self::LOCALE_KEY];
        if (! $lang && isset($_COOKIE[self::LOCALE_KEY]))
            $lang = @$_COOKIE[self::LOCALE_KEY];
        if (! $lang)
            $lang = $this->defaultLang;
        if (! $lang) {
            throw new Exception( 'Locale: Language is not define.' );
        }
        $this->speak($lang);
        return $this;
    }

    public function saveSession()
    {
        if ( isset($_SESSION) ) {
            $_SESSION[self::LOCALE_KEY] = $this->current;
        }
    }

    protected function setCookie()
    {
        $time = time() + self::COOKIE_LIFETIME;
        @setcookie(self::LOCALE_KEY , $this->current , $time , '/');
    }

    public function getCurrentLang()
    {
        return $this->current;
    }

    public function speak($lang)
    {
        $this->current = $lang;
        $this->setCookie();
        $this->saveSession();
        $this->initGettext();
        return $this;
    }

    public function isSpeaking( $lang )
    {
        return $this->current == $lang;
    }

    public function current()
    {
        return $this->current;
    }

    public function speaking()
    {
        return $this->current;
    }

    public function available()
    {
        return $this->getLanguages();
    }

    /**
     * Get available language list
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->langList;
    }

    public function setLanguages(array $list)
    {
        $this->langList = $list;
    }

    /**
     * Add available language to the list
     *
     * @param string $lang
     * @param string $name
     */
    protected function addLanguage($lang, $name = null)
    {
        if (! $name) {
            $name = _($lang);
        }
        $this->langList[$lang] = $name;
        return $this;
    }

    public function remove( $lang )
    {
        unset( $this->langList[ $lang ] );

        return $this;
    }

    public function currentName() {
        return $this->name( $this->current );
    }

    // get language name from language hash
    public function name( $lang )
    {
        return @$this->langList[$lang];
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function setLocaleDir($dir)
    {
        $this->localedir = $dir;
    }

    public function getMessageDir($lang) {
        return $this->localedir
            . DIRECTORY_SEPARATOR
            . $lang
            . DIRECTORY_SEPARATOR
            . 'LC_MESSAGES'
            ;
    }

    /**
     * Return the LC_MESSAGES path of a language
     *
     * @return string
     */
    public function getLocalePoFile($lang)
    {
        return $this->getMessageDir($lang) . DIRECTORY_SEPARATOR . $this->domain . '.po';
    }

    protected function setupEnv()
    {
        $lang = $this->current;
        putenv("LC_ALL=$lang");
        @header('Content-Language: '. strtolower(str_replace('_', '-', $lang)) );
        setlocale(LC_ALL,  "$lang.UTF-8" );
        setlocale(LC_TIME, "$lang.UTF-8");
    }

    protected function initGettext()
    {
        $this->setupEnv();
        bindtextdomain($this->domain, $this->localedir);
        bind_textdomain_codeset($this->domain, $this->codeset);
        textdomain($this->domain);
    }

    /**
     * @deprecated
     */
    public function setDefault($lang)
    {
        $this->defaultLang = $lang;
        return $this;
    }

    /**
     * @deprecated
     */
    public function getDefault()
    {
        return $this->defaultLang;
    }
}

