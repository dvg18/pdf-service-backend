<?php

class Request
{

    /**
     * Container for all super global arrays
     * @var array
     */
    private $_data = array();

    /**
     * List of super global arrays for $this->_data.
     * WARNING! list must be ordered by loading sequence.
     * @var array
     */
    private $_sgas = array('get', 'post', 'server');

    /** @var \EngineSite|NULL */
    public $EngineSite = NULL;

    /**
     * This var sets to TRUE when system starts with default engine
     * @var bool
     */
    public $isDefaultEngine = FALSE;

    const IDX_HOST = 3;
    const IDX_ENGINE = 4;

    public function __construct()
    {
        $this->setSearchFormCookieName();
        $this->load();
    }

    /**
     * @return bool
     */
    public function isBot()
    {
        $bots = array(
            'rambler', 'googlebot', 'aport', 'yahoo', 'msnbot', 'turtle', 'mail.ru', 'omsktele',
            'yetibot', 'picsearch', 'sape.bot', 'sape_context', 'gigabot', 'snapbot', 'alexa.com',
            'megadownload.net', 'askpeter.info', 'igde.ru', 'ask.com', 'qwartabot', 'yanga.co.uk',
            'scoutjet', 'similarpages', 'oozbot', 'shrinktheweb.com', 'aboutusbot', 'followsite.com',
            'dataparksearch', 'google-sitemaps', 'appEngine-google', 'feedfetcher-google',
            'liveinternet.ru', 'xml-sitemaps.com', 'agama', 'metadatalabs.com', 'h1.hrn.ru',
            'googlealert.com', 'seo-rus.com', 'yaDirectBot', 'yandeG', 'yandex',
            'yandexSomething', 'Copyscape.com', 'AdsBot-Google', 'domaintools.com',
            'Nigma.ru', 'bing.com', 'dotnetdotcom'
        );
        foreach ($bots as $bot) {
            if (stripos($this->get('HTTP_USER_AGENT'), $bot) !== FALSE) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function setSearchFormCookieName()
    {
        $this->_data['SearchFormCookieName'] = 'hsf_state-' . preg_replace('|\.|',
                '_', empty($_SERVER['HTTP_HOST'])
                    ? '_'
                    : $_SERVER['HTTP_HOST']);
        // preg_replace
        // in javascript we set cookie <'hsf-state-'+location.host> (ex. hsf-state-www.jookit.com)
        // in php impossibly $_COOKIE['hsf-state-www.jookit.com'] correctly: $_COOKIE['hsf-state-www_jookit_com']
    }

    /**
     * Load all super global arrays into $this->_data
     * @return bool
     */
    public function load()
    {
        $this->_data = array_merge(
            $this->_data, $this->_loadNecessaryFilteredCookies()
        );

        $this->_data = array_merge($this->_data, $_GET);
        $this->_data = array_merge($this->_data, $_POST);
        $this->_data = array_merge($this->_data, $_SERVER);

//        // #3374 debug  if not exists HTTP_HOST
//        if (!array_key_exists('HTTP_HOST', $_SERVER) || array_key_exists('test', $_REQUEST)) {
//            $logName = '/srv/www/vehicle-project/tmp/common/engine/core/HTTP_HOST_error_' . date('Ymd') . '.log';
//            require_once "includes/functions.inc.php";
//            logFileMsg($logName, var_export($this->_data, 1));
//        }

        return TRUE;
    }

    /**
     * @return array
     */
    private function _loadNecessaryFilteredCookies()
    {
        $cookieVals = array();

        $preparedCookieFormData = $this->explodeURIstr(
            filter_input(
                INPUT_COOKIE, $this->_data['SearchFormCookieName']
            )
        );
        foreach ($preparedCookieFormData as $fieldName => $filedValue) {
            $cookieVals[$fieldName] = $filedValue;
        }
        $cookieVals['cookie_banner'] = filter_input(INPUT_COOKIE, 'cookie_banner');
        $cookieVals['active_search_form'] = (int) filter_input(INPUT_COOKIE, 'active_search_form');
        $moreOptions = filter_input(INPUT_COOKIE, 'show_more_options');
        if (isset($moreOptions)) {
            $cookieVals['show_more_options'] = (int) $moreOptions;
        }
        return $cookieVals;
    }

    /**
     * @param string $stringData
     * @return array
     */
    public function explodeURIstr($stringData)
    {
        $out = array();
        parse_str($stringData, $out);
        return $out;
    }

    /**
     * Get source by $sga. If is set $sga then value will get from
     * $_GET / $_POST / $_SERVER arrays, else from $this->_data array.
     * $sga must be 'get', 'post', 'server' or false
     * Also sga using in following methods
     * @param bool|string $sga
     * @return array|bool
     */
    public function getSource($sga = FALSE)
    {
        if (FALSE !== $sga && in_array(strtolower($sga), $this->_sgas)) {
            $sga = '_' . strtoupper($sga);
            global $$sga;
            $source = isset($$sga)
                ? $$sga
                : FALSE;
        } else {
            $source = $this->_data;
        }
        return $source;
    }

    /**
     * Get value for param
     * @param string $param
     * @param bool|string $sga
     * @return string
     */
    public function get($param, $sga = FALSE)
    {
        $source = $this->getSource($sga);
        return isset($source[$param])
            ? $source[$param]
            : '';
    }

    /**
     * Get all values for all $params
     * @param array $params
     * @param bool|string $sga
     * @return array
     */
    public function getAll($params, $sga = FALSE)
    {
        if (empty($params) || !is_array($params)) {
            return array();
        }

        $result = array();
        foreach ($params as $param) {
            $result[$param] = $this->get($param);
        }
        return $result;
    }

    /**
     * Check param with empty() function
     * @param string $param
     * @param bool|string $sga
     * @return bool
     */
    public function emptyParam($param, $sga = FALSE)
    {
        $source = $this->getSource($sga);
        return empty($source[$param]);
    }

    /**
     * @param string $param
     * @param bool|string $sga
     * @return bool
     */
    public function ifempty($param, $sga = FALSE)
    {
        return $this->emptyParam($param, $sga);
    }

    /**
     * Check all $params with empty() function
     * @param array $params
     * @param bool $sga
     * @return bool
     */
    public function ifEmptyAll($params, $sga = FALSE)
    {
        if (empty($params) || !is_array($params)) {
            return FALSE;
        }

        foreach ($params as $param) {
            if (!$this->emptyParam($param, $sga)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Check all $params with empty() function
     * @param array $params
     * @param bool|string $sga
     * @return bool
     */
    public function ifNotEmptyAll($params, $sga = FALSE)
    {
        if (empty($params) || !is_array($params)) {
            return FALSE;
        }

        foreach ($params as $param) {
            if ($this->emptyParam($param, $sga)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Check param with isset() function
     * @param string $param
     * @param bool|string $sga
     * @return bool
     */
    public function issetParam($param, $sga = FALSE)
    {
        $source = $this->getSource($sga);
        return isset($source[$param]);
    }

    /**
     * @param string $param
     * @param bool|string $sga
     * @return bool
     */
    public function ifset($param, $sga = FALSE)
    {
        return $this->issetParam($param, $sga);
    }

    /**
     * Check all $params with isset() function
     * @param array $params
     * @param bool|string $sga
     * @return bool
     */
    public function ifSetAll($params, $sga = FALSE)
    {
        if (empty($params) || !is_array($params)) {
            return FALSE;
        }

        foreach ($params as $param) {
            if (!$this->issetParam($param, $sga)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Set $value for $param
     * @param string $param
     * @param string $value
     * @return bool
     */
    public function set($param, $value = '')
    {
        if ($param == 'REQUEST_PAGE') {
            $this->_setRequestPage($value);
        } else {
            $this->_data[$param] = $value;
        }
        return TRUE;
    }

    /**
     * @param string $value
     */
    private function _setRequestPage($value)
    {
        $this->_data['REQUEST_MODULE_FOLDER'] = $this->_getModuleFolder();
        $clearValue = preg_replace(
            '{^(' . $this->_data['REQUEST_MODULE_FOLDER'] . ')}', '', $value);
        $this->_data['REQUEST_PAGE'] = $clearValue;
        $this->_data['REQUEST_PAGE_WITH_MODULE'] = $this->_data['REQUEST_MODULE_FOLDER'] . $clearValue;
    }

    /**
     * @return string
     */
    private function _getModuleFolder()
    {
        if ($this->issetParam('HTTP_X_MODULE')) {
            return sprintf('/%s', strtolower($this->get('HTTP_X_MODULE')));
        } else {
            return ((defined('NEW_RESPONSIVE_ENABLED') && NEW_RESPONSIVE_ENABLED))
                ? '/responsive'
                : '';
        }
    }

    /**
     * @param int $engineId
     */
    public function setEngine($engineId)
    {
        require_once CFG_PATH_DB_CLASS . 'EngineSite.class.php';
        $this->EngineSite = $GLOBALS['EngineSiteManager']->getById($engineId);
    }

    /**
     * Remove $param from $this->_data
     * @param string $param
     * @return bool
     */
    public function remove($param)
    {
        unset($this->_data[$param]);
        return TRUE;
    }

    public function upgradeByExtraFields()
    {
        if (isset($this->_data['HTTP_REAL_REQUEST_URI'])) {
            $moduleFolder = $this->_getModuleFolder();
            $this->_data['REQUEST_URI'] = preg_replace(
                '{^' . $moduleFolder . '}', '',
                $this->_data['HTTP_REAL_REQUEST_URI']
            );
            unset($this->_data['HTTP_REAL_REQUEST_URI']);
        }
        if (isset($this->_data['HTTP_X_REAL_IP'])) {
            $this->_data['REMOTE_ADDR'] = $this->_data['HTTP_X_REAL_IP'];
            unset($this->_data['HTTP_X_REAL_IP']);
        }
        if (isset($this->_data['HTTP_REAL_HOST'])) {
            $this->_data['HTTP_HOST'] = $this->_data['HTTP_REAL_HOST'];
            unset($this->_data['HTTP_REAL_HOST']);
        }
        if (!empty($this->_data['REQUEST_URI'])) {
            $uri = $this->_data['REQUEST_URI'];
            if (!empty($uri)) {
                $tmp = explode('?', $uri);
                $this->_data['SCRIPT_NAME'] = $tmp[0];
            }
        }
        if (!empty($this->_data['SCRIPT_NAME']) && $this->_data['SCRIPT_NAME'] == '/') {
            $this->_data['SCRIPT_NAME'] = '/index.php';
        }

        //$this->updateRequestByUserAgent();
    }

    public function updateRequestByUserAgent()
    {
        require_once 'DeviceInfoStorage.class.php';
        $this->_data = array_merge($this->_data, DeviceInfoStorage::getDeviceParams());

        if (isset($_GET['mobile_user'])) {
            setcookie('SwitchToMobileUser', $_GET['mobile_user'],
                time() + 365 * 24 * 60 * 60, '/');
            $_COOKIE['SwitchToMobileUser'] = $_GET['mobile_user'];
        }
        if (isset($_COOKIE['SwitchToMobileUser'])) {
            $this->_data['MobileUser'] = !empty($_COOKIE['SwitchToMobileUser'])
                ? 1
                : 0;
        }
        if (isset($_GET['use_mobile_mode'])) {
            setcookie('MobileModeWasChosen', $_GET['use_mobile_mode'],
                time() + 365 * 24 * 60 * 60, '/');
            $_COOKIE['MobileModeWasChosen'] = $_GET['use_mobile_mode'];
        }
        require_once 'SiteMode.class.php';
        $siteMode = $this->_data['MobileUser']
            ? SiteMode::MOBILE
            : SiteMode::GENERAL;
        if ($this->_data['MobileUser'] && isset($_COOKIE['MobileModeWasChosen'])) {
            $siteMode = empty($_COOKIE['MobileModeWasChosen'])
                ? SiteMode::GENERAL
                : SiteMode::MOBILE;
        }

        if (!$this->ifset('DEALER_HOST') && defined('NEW_RESPONSIVE_ENABLED') && NEW_RESPONSIVE_ENABLED) {
            $siteMode = SiteMode::GENERAL;
        }

        $this->_data['SiteMode'] = $siteMode;
    }

    /**
     * @param string $zone
     * @return bool
     */
    public function upgradeByDealerHost($zone)
    {
        if (defined('IS_DEALERCONNECT') && IS_DEALERCONNECT) {
            $host = $zone;
            $zone = 'dealerconnect.backend';
        } else {
            $host = empty($this->_data['HTTP_HOST'])
                ? FALSE
                : $this->_data['HTTP_HOST'];
        }
        if (empty($host)) {
            return;
        }
        $dlrKeys = array('dealer', 'seller');
        foreach ($dlrKeys as $key) {
            $testValue = '-' . $key . '.' . $zone;
            if (strpos($host, $testValue) > 0) {
                $this->_data['DEALER_HOST'] = str_replace(array('www.', $testValue), '', $host);
                return TRUE;
            }
        }
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

/** @TODO own _SESSION class */
    /**
     * @param string $name
     * @return mixed|null
     */
    public function getSessionValue($name)
    {
        return isset($this->_data['_SESSION'][$name])
            ? $this->_data['_SESSION'][$name]
            : NULL;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $commit TRUE write session immediately else just update local session storage. TRUE by default
     */
    public function setSessionValue($name, $value, $commit = TRUE)
    {
        $this->_data['_SESSION'][$name] = $value;
        if (!defined('IGNORE_SESSION_START') && $commit) {
            session_start();
            $_SESSION[$name] = $value;
            session_write_close();
        }
    }
/** @TODO own _SESSION class */

}

$GLOBALS['Request'] = new Request();
