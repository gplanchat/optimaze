<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 23:14
 */

namespace Gplanchat\Javascript\Lexer;


class Context
{
    /** @var bool  */
    protected $strictMode = false;

    /** @var int  */
    protected $level = 0;

    /** @var int  */
    protected $bracketLevel = 0;

    /** @var int  */
    protected $curlyLevel = 0;

    /** @var int  */
    protected $squareBracketLevel = 0;

    /** @var int  */
    protected $hookLevel = 0;

    /** @var bool  */
    protected $inFunction = false;

    /** @var bool  */
    protected $inForLoopInit = false;

    /**
     * @param bool $strictMode
     * @param int $level
     * @param int $bracketLevel
     * @param int $curlyLevel
     * @param int $squareBracketLevel
     * @param int $hookLevel
     * @param bool $inFunction
     * @param bool $inForLoopInit
     */
    public function __construct(
        $strictMode = false,
        $level = 0,
        $bracketLevel = 0,
        $curlyLevel = 0,
        $squareBracketLevel = 0,
        $hookLevel = 0,
        $inFunction = false,
        $inForLoopInit = false
    ) {
        $this->strictMode = $strictMode;
        $this->level = $level;
        $this->bracketLevel = $bracketLevel;
        $this->curlyLevel = $curlyLevel;
        $this->squareBracketLevel = $squareBracketLevel;
        $this->hookLevel = $hookLevel;
        $this->inFunction = $inFunction;
        $this->inForLoopInit = $inForLoopInit;
    }

    /**
     * @return int
     */
    public function getBracketLevel()
    {
        return $this->bracketLevel;
    }

    /**
     * @return int
     */
    public function getSquareBracketLevel()
    {
        return $this->squareBracketLevel;
    }

    /**
     * @return int
     */
    public function getCurlyLevel()
    {
        return $this->curlyLevel;
    }

    /**
     * @return int
     */
    public function getHookLevel()
    {
        return $this->hookLevel;
    }

    /**
     * @return boolean
     */
    public function getInForLoopInit()
    {
        return $this->inForLoopInit;
    }

    /**
     * @return boolean
     */
    public function getInFunction()
    {
        return $this->inFunction;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return boolean
     */
    public function getStrictMode()
    {
        return $this->strictMode;
    }

    /**
     * @param boolean $strictMode
     * @return $this
     */
    public function setStrictMode($strictMode)
    {
        $this->strictMode = $strictMode;

        return $this;
    }
}
