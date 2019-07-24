<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 6/1/19
 * Time: 2:40 PM
 */
require_once("UserOperationBaseVo.php");

class EditProfileWithConfirmationVo extends UserOperationBaseVo
{
    protected $firstName;                       // scope: profile
    protected $lastName;                        // scope: profile
    protected $nickName;                        // scope: profile
    protected $email;                           // scope: email
    protected $phoneNumber;                     // scope: address
    protected $cellphoneNumber;                 // scope: phone
    protected $nationalCode;                    // scope: legal
    protected $gender;                          // scope: profile
    protected $address;                         // scope: address
    protected $birthDate;                       // scope: legal
    protected $country;                         // scope: address
    protected $state;                           // scope: address
    protected $city;                            // scope: address
    protected $postalcode;                      // scope: address
    protected $sheba;                           // scope: legal
    protected $profileImage;                    // scope: profile
    protected $birthState;                      // scope: address
    protected $client_metadata;
    protected $identificationNumber;
    protected $fatherName;

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setNickName($nickName) {
        $this->nickName = $nickName;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    public function setCellPhoneNumber($cellphoneNumber) {
        $this->cellphoneNumber = $cellphoneNumber;
    }

    public function setNationalCode($nationalCode) {
        $this->nationalCode = $nationalCode;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setPostalCode($postalCode) {
        $this->postalcode = $postalCode;
    }

    public function setSheba($sheba) {
        $this->sheba = $sheba;
    }

    public function setProfileImage($profileImage) {
        $this->profileImage = $profileImage;
    }

    public function setClientMetaData($client_metadata) {
        $this->client_metadata = $client_metadata;
    }

    public function setBirthState($birthState) {
        $this->birthState = $birthState;
    }

    public function setIdentificationNumber($identificationNumber) {
        $this->identificationNumber = $identificationNumber;
    }

    public function setFatherName($fatherName) {
        $this->fatherName = $fatherName;
    }

    public function objectToArray()
    {
        return array_filter(get_object_vars($this));
    }


}