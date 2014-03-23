<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 04:07
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class PrimaryExpression
    implements RecursiveGrammarInterface
{
    use LeftAssociativeGrammarTrait;
}
