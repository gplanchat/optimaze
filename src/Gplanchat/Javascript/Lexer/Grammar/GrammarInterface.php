<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

use Gplanchat\Javascript\Lexer\Context;
use Gplanchat\Tokenizer\Token;

interface GrammarInterface
{
    /**
     * @return int
     */
    public function getType();
}
