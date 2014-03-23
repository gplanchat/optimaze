<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 14:06
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class ConditionalExpression
    implements RecursiveGrammarInterface
{
    use LeftAssociativeGrammarTrait;
}
