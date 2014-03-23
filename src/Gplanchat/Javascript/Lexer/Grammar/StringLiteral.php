<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class StringLiteral
    implements GrammarInterface
{
    use GrammarTrait;

    /**
     * @var string
     */
    protected $stringLiteral = null;

    public function __construct($stringLiteral)
    {
        $this->stringLiteral = $stringLiteral;
    }

    /**
     * @return string
     */
    public function getStringLiteral()
    {
        return $this->stringLiteral;
    }
}
