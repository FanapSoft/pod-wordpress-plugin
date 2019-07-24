<?php

class AuthorizeSSO
{
    private $profileUserPOD; // Save POD User Profile Information.
    private $podOptions; //  pod_options stored in wordpress. 
    private $userIDWP; // ID user in wordpress.
    private $redirectUri; // Redirect uri (when go back from POD Platform)
    private $ClientInfoClass;
    private $UserProfileClass;

    public function __construct()
    {
        $this->podOptions = get_option('pod_options');
        $this->redirectUri = site_url('?pod-sso');

        /*
            address services
            http://docs.pod.land/v1.0.8.0/Developer/Introduction/327/Urls 
        */
        $this->env = 'Production';
        $this->config  = (require_once(POD_SSO_DIR_PUBLIC . '/config.php'))[$this->env];
    }

    /**
     * ID user wordpress
     */
    public function setUserIDWP($id)
    {
        $this->userIDWP = $id;
    }

    public function authorize()
    {
        $params = array(
            'oauth' => 'authorize',
            'response_type' => 'code',
            'client_id' => $this->podOptions['client_id'],
            'redirect_uri' => $this->redirectUri,
            'scope' => 'profile email'
        );
        $params = http_build_query($params);

        wp_redirect($this->config['SSO_ADDRESS'] . "/oauth2/authorize/" . '?' . $params);
    }

    /**
     * set instance of ClientInfo class
     */
    public function setClientInfoClass($obj)
    {
        $this->ClientInfoClass = $obj;
    }

    /**
     * return instance of ClintInfo Class
     */
    public function getClientInfoClass()
    {
        return $this->ClientInfoClass;
    }

    /**
     * set parameter clientInfo object
     */
    public function setClientInfoParam()
    {
        $this->ClientInfoClass->setClientId($this->podOptions['client_id']);
        $this->ClientInfoClass->setClientSecret($this->podOptions['client_secret']);
        $this->ClientInfoClass->setRedirectUri(site_url('?pod-sso'));
    }

    /**
     * Get Access Token 
     * 
     * @param string $obj 
     * @param string $code
     */
    public function getAccessTokenWp($authorization_code, $obj)
    {
        $param  = array(
            'code' => $authorization_code,
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->podOptions['client_id'],
            'client_secret' => $this->podOptions['client_secret'],
        );

        return $obj->getAccessToken($param, $this->ClientInfoClass);
    }

    /**
     * set instance of UserProfileVo class
     */
    public function setUserProfileVoClass($obj)
    {
        $this->UserProfileClass = $obj;
        $this->UserProfileClass->setClientId($this->podOptions['client_id']);
        $this->UserProfileClass->setClientSecret($this->podOptions['client_secret']);
    }

    /**
     * Get Profile Information from POD Service.
     * 
     */
    public function getUserProfile($access_token, $userOperationClass)
    {
        $this->profileUserPOD = $userOperationClass->getUserProfile(
            [
                "_token_" => $access_token,
                "_token_issuer_" => 1
            ],
            $this->UserProfileClass
        );
        return  $this->profileUserPOD['result'];
    }

    /**
     * Check user exist by email.
     *
     * @param string $email
     * @return int|false The user's ID on success, and false on failure.
     */
    public function checkEmailExist($email)
    {
        return email_exists($email);
    }

    /**
     * Register POD user in Wordpress
     * Creates a new user with just the username, password, and email. For more
     *
     * @param obejct $userInfo
     * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not
     *                      be created.
     */
    public function registerUser($userInfo)
    {
        $random_password = wp_generate_password();
        $userIDWP = wp_create_user($userInfo['email'], $random_password, $userInfo['email']);
        $id = is_wp_error($userIDWP) ? null : $userIDWP;

        $this->setUserIDWP($id);
        return $id;
    }

    /**
     * 
     * @param obejct $userInfo
     */
    public function updateUser($userInfo)
    {
        $firstName = isset($userInfo['firstName']) ? $userInfo['firstName'] : '';
        $lastName = isset($userInfo['lastName']) ? $userInfo['lastName'] : '';
        $nickName = isset($userInfo['nickName']) ? $userInfo['nickName'] : '';

        $updateUserArray = array(
            'ID' => $this->userIDWP,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'nickname' => $nickName,
            'display_name' => $firstName
        );
        wp_update_user($updateUserArray);
    }

    /**
     * Update user meta field based on user ID.
     *
     * @param object    $userInfo    userInfo.
     * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
     */
    public function updateMetaDataUser($userInfo)
    {
        return update_user_meta($this->userIDWP, 'pod_user_id', $userInfo['userId']);
        return update_user_meta($this->userIDWP, 'pod_sso_id', $userInfo['ssoId']);
    }

    /**
     * Logged In user base on user id
     */
    public function userLoggedIn()
    {
        wp_clear_auth_cookie();
        wp_set_current_user($this->userIDWP);
        wp_set_auth_cookie($this->userIDWP);
    }
}
