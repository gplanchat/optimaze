<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 13:25
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class Expression
    implements RecursiveGrammarInterface
{
    use LeftAssociativeGrammarTrait;
}
