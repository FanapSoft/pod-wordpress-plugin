<?php
defined('ROOT_DIR_POD_USER_OPERATION') or define('ROOT_DIR_POD_USER_OPERATION', dirname(__FILE__, 2));

require ROOT_DIR_POD_USER_OPERATION . '/vendor/autoload.php';
require_once(dirname(__FILE__, 3) . '/PodBaseService/models/ApiRequestHandler.php');
require_once(dirname(__FILE__, 3) . '/PodBaseService/models/BaseService.php');

class UserOperationService extends BaseService
{

    private $header;
    private $baseUri;
    private static $userOperationApi =
    [
        // #1, tag: user_operations -> getUserProfile
        'getUserProfile' => [
            //            'baseUri'   => 'PLATFORM-ADDRESS',
            'subUri'    => 'nzh/getUserProfile/',
            'method'    => 'GET'
        ],

        // #2, tag: user_operations -> editProfile
        'editProfile' => [
            //            'baseUri'   => 'PLATFORM-ADDRESS',
            'subUri'    => 'nzh/editProfile/',
            'method'    => 'POST'
        ],

        // #3, tag: user_operations -> editProfileWithConfirmation
        'editProfileWithConfirmation' => [
            //            'baseUri'   => 'PLATFORM-ADDRESS',
            'subUri'    => 'nzh/editProfileWithConfirmation/',
            'method'    => 'POST'
        ],

        // #4, tag: user_operations -> confirmEditProfile
        'confirmEditProfile' => [
            //            'baseUri'   => 'PLATFORM-ADDRESS',
            'subUri'    => 'nzh/confirmEditProfile/',
            'method'    => 'POST'
        ],

        // #5, tag: user_operations -> listAddress
        'listAddress' => [
            //            'baseUri'   => 'PLATFORM-ADDRESS',
            'subUri'    => 'nzh/listAddress/',
            'method'    => 'GET'
        ],

    ];

    public function __construct()
    {
        parent::__construct();

        self::$jsonSchema = json_decode(file_get_contents(ROOT_DIR_POD_USER_OPERATION . '/jsonSchema.json'), true);
        // $this->baseUri = (BaseInfo::getServerType() == "Production" ? self::$config["baseUri"]["PLATFORM-ADDRESS"]  : self::$config["baseUri"]["SANDBOX-PLATFORM-ADDRESS"]);
        $this->baseUri = 'https://api.pod.land/srv/core/';
        $this->header = [
            "_token_issuer_" => 1
        ];
    }


    /**
     * @param array $header
     *      @option string "_token_"
     *      @option string "_token_issuer_"
     *
     * @param UserProfileVo $params
     *      @option string "client_id"
     *      @option string "client_secret"
     * @throws
     * @return mixed
     */
    public function getUserProfile($header, UserProfileVo $params)
    {
        $apiName = 'getUserProfile';

        $header = array_merge($this->header, (array) $header);
        $params = $params->objectToArray();

        $paramKey = self::$userOperationApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';

        $option = [
            'headers' => array_filter($header, 'strlen'),
            $paramKey => array_map('trim', $params),
        ];

        $validateResult = self::validateOption($apiName, $option, $paramKey);
        if ($validateResult['validate']) {
            return ApiRequestHandler::Request(
                $this->baseUri,
                self::$userOperationApi[$apiName]['method'],
                self::$userOperationApi[$apiName]['subUri'],
                $option
            );
        } else {
            throw new Exception($validateResult['errorMessage'], self::VALIDATION_ERROR_CODE);
        }
    }

    /**
     * @param array $header
     *      @option string "_token_"
     *      @option string "_token_issuer_"
     *
     * @param EditProfileWithConfirmationVo $params
     *      @option string  "client_id"
     *      @option string  "client_secret"
     *      @option string  "firstName"
     *      @option string  "lastName"
     *      @option string  "nickName"
     *      @option string  "email"
     *      @option string  "phoneNumber"
     *      @option string  "cellphoneNumber"
     *      @option string  "nationalCode"
     *      @option string  "gender"
     *      @option string  "address"
     *      @option string  "birthDate"
     *      @option string  "country"
     *      @option string  "state"
     *      @option string  "city"
     *      @option string  "postalcode"
     *      @option string  "sheba"
     *      @option string  "profileImage"
     *      @option string  "client_metadata"
     *      @option string  "birthState"
     *      @option string  "identificationNumber"
     *      @option string  "fatherName"
     * @throws

     * @return mixed
     */

    public function editProfileWithConfirmation($header, EditProfileWithConfirmationVo $params)
    {
        $apiName = 'editProfileWithConfirmation';

        $header = array_merge($this->header, (array) $header);
        $params = $params->objectToArray();

        $paramKey = self::$userOperationApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';

        $option = [
            'headers' => array_filter($header, 'strlen'),
            $paramKey => array_map('trim', $params),
        ];

        $validateResult = self::validateOption($apiName, $option, $paramKey);
        if ($validateResult['validate']) {
            return ApiRequestHandler::Request(
                $this->baseUri,
                self::$userOperationApi[$apiName]['method'],
                self::$userOperationApi[$apiName]['subUri'],
                $option
            );
        } else {
            throw new Exception($validateResult['errorMessage'], self::VALIDATION_ERROR_CODE);
        }
    }

    /**
     * @param array $header
     *      @option string "_token_"
     *      @option string "_token_issuer_"
     *
     * @param ListAddressVo $params
     *      @option string  "client_id"
     *      @option string  "client_secret"
     *      @option string  "offset"
     *      @option string  "lastName"
     * @return mixed
     * @throws
     */

    public function listAddress($header, ListAddressVo $params)
    {
        $apiName = 'listAddress';

        $header = array_merge($this->header, (array) $header);
        $params = $params->objectToArray();

        $paramKey = self::$userOperationApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';

        $option = [
            'headers' => array_filter($header, 'strlen'),
            $paramKey => array_map('trim', $params),
        ];

        $validateResult = self::validateOption($apiName, $option, $paramKey);
        if ($validateResult['validate']) {
            return ApiRequestHandler::Request(
                $this->baseUri,
                self::$userOperationApi[$apiName]['method'],
                self::$userOperationApi[$apiName]['subUri'],
                $option
            );
        } else {
            throw new Exception($validateResult['errorMessage'], self::VALIDATION_ERROR_CODE);
        }
    }
}
