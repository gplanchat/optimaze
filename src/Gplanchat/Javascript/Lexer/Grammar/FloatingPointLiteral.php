<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class FloatingPointLiteral
    implements GrammarInterface
{
    use GrammarTrait;

    /**
     * @var string
     */
    protected $floatingPointLiteral = null;

    /**
     * @param float $floatingPointLiteral
     */
    public function __construct($floatingPointLiteral)
    {
        $this->floatingPointLiteral = $floatingPointLiteral;
    }

    /**
     * @return float
     */
    public function getFloatingPointLiteral()
    {
        return $this->floatingPointLiteral;
    }
}
