<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class ShiftOperator
    implements GrammarInterface
{
    use GrammarTrait;

    /**
     * @var string
     */
    protected $operator = null;

    public function __construct($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}
