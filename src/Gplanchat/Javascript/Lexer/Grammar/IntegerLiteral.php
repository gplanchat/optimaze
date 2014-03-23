<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class IntegerLiteral
    implements GrammarInterface
{
    use GrammarTrait;

    /**
     * @var string
     */
    protected $integerLiteral = null;

    /**
     * @param int $integerLiteral
     */
    public function __construct($integerLiteral)
    {
        $this->integerLiteral = $integerLiteral;
    }

    /**
     * @return int
     */
    public function getIntegerLiteral()
    {
        return $this->integerLiteral;
    }
}
