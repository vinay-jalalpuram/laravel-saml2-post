<?php

namespace SamlPost\Saml2;

use OneLogin\Saml2\Auth;

/**
 * A simple class that represents the user that 'came' inside the saml2 assertion
 * Class Saml2User
 * @package SamlPost\Saml2
 */
class Saml2User implements \Illuminate\Contracts\Auth\Authenticatable
{

    protected $auth;

    function __construct(Auth $auth = null)
    {
        $this->auth = $auth;
    }

    /**
     * @return string User Id retrieved from assertion processed this request
     */
    function getUserId()
    {
        $auth = $this->auth;

        return $auth->getNameId();

    }

    /**
     * @return array attributes retrieved from assertion processed this request
     */
    function getAttributes()
    {
        $auth = $this->auth;

        return $auth->getAttributes();
    }

    /**
     * Returns the requested SAML attribute
     *
     * @param string $name The requested attribute of the user.
     * @return array|null Requested SAML attribute ($name).
     */
    function getAttribute($name) {
        $auth = $this->auth;

        return $auth->getAttribute($name);
    }

    /**
     * @return string the saml assertion processed this request
     */
    function getRawSamlAssertion()
    {
        return app('request')->input('SAMLResponse'); //just this request
    }

    function getIntendedUrl()
    {
        $relayState = app('request')->input('RelayState'); //just this request

        $url = app('Illuminate\Contracts\Routing\UrlGenerator');

        if ($relayState && $url->full() != $relayState) {

            return $relayState;
        }
    }

    /**
     * Parses a SAML property and adds this property to this user or returns the value
     *
     * @param string $samlAttribute
     * @param string $propertyName
     * @return array|null
     */
    function parseUserAttribute($samlAttribute = null, $propertyName = null) {
        if(empty($samlAttribute)) {
            return null;
        }
        if(empty($propertyName)) {
            return $this->getAttribute($samlAttribute);
        }

        return $this->{$propertyName} = $this->getAttribute($samlAttribute);
    }

    /**
     * Parse the saml attributes and adds it to this user
     *
     * @param array $attributes Array of properties which need to be parsed, like this ['email' => 'urn:oid:0.9.2342.19200300.100.1.3']
     */
    function parseAttributes($attributes = array()) {
        foreach($attributes as $propertyName => $samlAttribute) {
            $this->parseUserAttribute($samlAttribute, $propertyName);
        }
    }

    function getSessionIndex()
    {
        return $this->auth->getSessionIndex();
    }

    function getNameId()
    {
        return $this->auth->getNameId();
    }

    // Authenticatable functions

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getNameId();;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getUserId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return "";
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->getSessionIndex();
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        //
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return "";
    }

}
