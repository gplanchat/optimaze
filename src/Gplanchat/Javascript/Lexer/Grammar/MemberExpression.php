<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:58
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class MemberExpression
    implements RecursiveGrammarInterface
{
    use LeftAssociativeGrammarTrait;
}
