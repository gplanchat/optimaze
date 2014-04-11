<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 11/04/14
 * Time: 23:33
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\TokenizerNavigationAwareTrait;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\Token;

class TokenNullSeeker
    implements RuleInterface
{
    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        // no operation
    }
}
