<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 5/26/19
 * Time: 11:11 AM
 */
use JsonSchema\Validator;
//use JsonSchema\Constraints\Constraint;

class BaseService
{
    // Error Codes
    const VALIDATION_ERROR_CODE = 888;
    const VALIDATION_ERROR_MESSAGE = "";
    const SDK_ERROR_CODE = 999;
    const SERVER_ERROR_CODE = 500;

    public static $config;
    protected static $jsonSchema;
    private static $validator;


    public function __construct() {
        self::$config = require __DIR__ . '/../config/config.php';
        self::$validator = new Validator();
    }

    // validate options
    public static function validateOption($apiName, $option, $paramKey = 'query') {

        $header = (object)$option['headers'];
        $params = (object)($option[$paramKey]);
        $headerSchema = json_decode ( json_encode(self::$jsonSchema[$apiName]['header']));
        $paramSchema = json_decode ( json_encode(self::$jsonSchema[$apiName][$paramKey]));
        $result = [
            'validate' => true,
            'errorList' => ''
            ];
        $errorMessage = '';
        self::$validator->validate($header, $headerSchema);
        self::$validator->validate($params, $paramSchema);
        // handle error
        if (!self::$validator->isValid()) {
            foreach (self::$validator->getErrors() as $error) {
                $errorMessage = implode("; ", $error) . '; ';
            }
            $result['validate'] = false;
            $result['errorMessage'] = $errorMessage;
        }

        return $result;
    }

    // build http query for array parameters
    public function buildHttpQuery($params){

        $query = http_build_query($params ,null, '&');
        $httpQuery = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '[]=', $query);

        return $httpQuery;

    }

    public static function prepareQueryParam($params) {
        foreach ($params as $key => $value){
            if (is_array($value)) {
                $params[$key] = implode(",", $value);
            }
        }
        return $params;
    }

    public static function  filterNotEmptyValue($value) {
        if (is_array($value)){
            return !empty($value);
        }
        else{
            return strlen($value);
        }
    }

    public static function  prepareData($value) {
        if (is_string($value)){
            return trim($value);
        }
        else if (is_bool($value)){
            return $value ? "true" : "false";
        }
        else {
            return $value;
        }
    }

}