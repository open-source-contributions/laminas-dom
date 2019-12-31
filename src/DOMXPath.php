<?php

/**
 * @see       https://github.com/laminas/laminas-dom for the canonical source repository
 * @copyright https://github.com/laminas/laminas-dom/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-dom/blob/master/LICENSE.md New BSD License
 */
namespace Laminas\Dom;

use ErrorException;

/**
 * Extends DOMXpath to throw ErrorExceptions instead of raising errors.
 */
class DOMXPath extends \DOMXPath
{
    /**
     * A stack of ErrorExceptions created via addError()
     *
     * @var array
     */
    protected $errors = array(null);

    /**
     * Evaluates an XPath expression; throws an ErrorException instead of
     * raising an error
     *
     * @param string $expression The XPath expression to evaluate.
     * @return \DOMNodeList
     * @throws ErrorException
     */
    public function queryWithErrorException($expression)
    {
        $this->errors = array(null);

        set_error_handler(array($this, 'addError'), \E_WARNING);
        $nodeList = $this->query($expression);
        restore_error_handler();

        $exception = array_pop($this->errors);
        if ($exception) {
            throw $exception;
        }

        return $nodeList;
    }

    /**
     * Adds an error to the stack of errors
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @return void
     */
    public function addError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        $last_error = end($this->errors);
        $this->errors[] = new ErrorException(
            $errstr,
            0,
            $errno,
            $errfile,
            $errline,
            $last_error
        );
    }
}