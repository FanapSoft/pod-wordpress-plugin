<?php

// Redirect the user back to the home page if logged in.
if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

require_once POD_SSO_DIR_PUBLIC . '/AuthorizeSSO.php';
require_once POD_SSO_DIR_SDK . '/PodBaseService/models/ClientInfo.php';
require_once POD_SSO_DIR_SDK . '/PodSSO/models/SSOService.php';

require_once POD_SSO_DIR_SDK . '/PodUserOperation/models/UserOperationService.php';
require_once POD_SSO_DIR_SDK . '/PodUserOperation/models/UserProfileVo.php';

$ssoObj = new AuthorizeSSO;

if (!isset($_GET['code'])) {
    $ssoObj->authorize();
    exit;
}

if (isset($_GET['code']) && !empty($_GET['code'])) {

    $clientInfo = new ClientInfo;
    $ssoObj->setClientInfoClass($clientInfo);
    $ssoObj->setClientInfoParam();
    $ssoService = new SSOService($ssoObj->getClientInfoClass());

    $authorization_code = sanitize_text_field($_GET['code']);

    $token = $ssoObj->getAccessTokenWp($authorization_code, $ssoService);

    $userProfileVoClass = new UserProfileVo;
    $ssoObj->setUserProfileVoClass($userProfileVoClass);

    $userOperationClass = new UserOperationService;

    $profile = $ssoObj->getUserProfile($token['access_token'], $userOperationClass);
    $userIDWP = $ssoObj->checkEmailExist($profile['email']);

    if ($userIDWP === false) {
        $userIDWP = $ssoObj->registerUser($profile);
    } else {
        $ssoObj->setUserIDWP($userIDWP);
    }

    if (gettype($userIDWP) === 'integer') {
        $ssoObj->updateUser($profile);
        $ssoObj->updateMetaDataUser($profile);
        $ssoObj->userLoggedIn();
        wp_redirect(home_url());
    }
}
