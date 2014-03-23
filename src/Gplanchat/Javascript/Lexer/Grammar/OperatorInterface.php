<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

interface OperatorInterface
    extends GrammarInterface
{
    /**
     * @param string $operator
     */
    public function __construct($operator);

    /**
     * @return string
     */
    public function getOperator();
}
