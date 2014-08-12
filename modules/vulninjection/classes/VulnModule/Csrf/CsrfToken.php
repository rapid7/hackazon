<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.08.2014
 * Time: 14:36
 */


namespace VulnModule\Csrf;


class CsrfToken
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param string $id    The token ID
     * @param string $value The actual token value
     */
    public function __construct($id, $value)
    {
        $this->id = (string) $id;
        $this->value = (string) $value;
    }

    /**
     * Returns the ID of the CSRF token.
     *
     * @return string The token ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of the CSRF token.
     *
     * @return string The token value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the value of the CSRF token.
     *
     * @return string The token value
     */
    public function __toString()
    {
        return $this->value;
    }
}