<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

class BooleanLiteral
    implements GrammarInterface
{
    use GrammarTrait;

    /**
     * @var string
     */
    protected $booleanLiteral = null;

    /**
     * @param bool $booleanLiteral
     */
    public function __construct($booleanLiteral)
    {
        $this->booleanLiteral = $booleanLiteral;
    }

    /**
     * @return bool
     */
    public function getBooleanLiteral()
    {
        return $this->booleanLiteral;
    }
}
