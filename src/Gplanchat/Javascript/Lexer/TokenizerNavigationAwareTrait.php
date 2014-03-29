<?php

namespace Gplanchat\Javascript\Lexer;

use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Tokenizer\Token;

trait TokenizerNavigationAwareTrait
{
    /**
     * @param TokenizerInterface $tokenizer
     * @return Token
     * @throws LexicalError
     */
    protected function nextToken(TokenizerInterface $tokenizer)
    {
        $tokenizer->next();
        $token = $this->currentToken($tokenizer);

        return $token;
    }

    /**
     * @param TokenizerInterface $tokenizer
     * @return Token
     * @throws LexicalError
     */
    protected function currentToken(TokenizerInterface $tokenizer)
    {
        if (!$valid = $tokenizer->valid()) {
            throw new LexicalError('Invalid $end reached');
        }
        $token = $tokenizer->current();

        return $token;
    }
}
