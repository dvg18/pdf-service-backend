<?php
/**
 * Controller of pdf.backend API
 *
 * @todo Need to use links like http://pdf-service-backend/file/{id} + GET, because they looks more user
 * friendly.
 *
 * Actions:
 *   http://pdf-service-backend/?action=file&id={id} + GET     Get file
 *   http://pdf-service-backend/?action=file&id={id} + DELETE  Delete file
 *
 *   http://pdf-service-backend/?action=html&id={id} + GET     Get html code of file
 *   http://pdf-service-backend/?action=html&html={code} + POST    Create file with {code} content
 *
 *   http://pdf-service-backend/?action=pdf&id={id} + GET     Get pdf file from html file with {id}
 *
 */
class Controller
{
    /**
     *
     * @var Callable[] Defined methods which can be run
     */
    private $_actions = array();


    public function __construct()
    {

        $this->_actions = array(
            'html' => function ($requestMethod) {
                return self::Html($requestMethod, array('GET, POST, PUT, DELETE'));
            },
            'pdf' => function ($requestMethod) {
                return self::Pdf($requestMethod, array('GET, DELETE'));
            },
            'file' => function ($requestMethod) {
                return self::File($requestMethod, array('GET, DELETE'));
            },

        );
    }

    /**
     * Process actions
     *
     * @return void
     * @throws Exception
     */
    public function process()
    {
        $response = $this->_getProcessedActionState();
        if (!$response) {
            throw new Exception('Nothing to do or unknown error occurred.');
        }
    }

    /**
     * Process action
     *
     * @return boolean Process state
     * @throws Exception
     */
    private function _getProcessedActionState()
    {
        $action = empty($_GET['action'])
            ? ''
            : $_GET['action'];

        return key_exists($action, $this->_actions)
            ? call_user_func_array($this->_actions[$action], array(self::_getActionTypeByRequestMethod()))
            : FALSE;
    }

    /**
     * Get action type by request method
     *
     * @return string
     * @see Actionable
     */
    private static function _getActionTypeByRequestMethod()
    {
        return in_array($_SERVER['REQUEST_METHOD'], array('OPTIONS', 'POST', 'PUT', 'DELETE'))
            ? strtolower($_SERVER['REQUEST_METHOD'])
            : 'get';
    }

    public static function __callStatic($name, $arguments)
    {
        $name = strtolower($name);
        self::_sendCORSHeaders();
        header('Access-Control-Allow-Methods: ' . strtoupper(implode($arguments[1])));
        $className = 'Action' . ucfirst($name);
        $method = strtolower($arguments[0]);
        return $className::$method();
    }

    private static function _sendCORSHeaders()
    {
        header('Access-Control-Allow-Origin: ' . HTTP_ORIGIN);
        header('X-Frame-Options: ALLOW-FROM ' . HTTP_ORIGIN . '/');
    }

}
