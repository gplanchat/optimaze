<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 22/03/14
 * Time: 19:11
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\ServiceManager\ServiceManagerAwareTrait;
use Gplanchat\Tokenizer\Token;

trait RuleTrait
{
    use ServiceManagerAwareTrait {
        ServiceManagerAwareTrait::getServiceManager as getRuleServiceManager;
        ServiceManagerAwareTrait::setServiceManager as setRuleServiceManager;
    };
    use ServiceManagerAwareTrait {
        ServiceManagerAwareTrait::getServiceManager as getGrammarServiceManager;
        ServiceManagerAwareTrait::setServiceManager as setGrammarServiceManager;
    };

    /**
     * @param TokenizerInterface $tokenizer
     * @return Token
     * @throws LexicalError
     */
    protected function nextToken(TokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);
        $tokenizer->next();

        return $token;
    }

    /**
     * @param TokenizerInterface $tokenizer
     * @return Token
     * @throws LexicalError
     */
    protected function currentToken(TokenizerInterface $tokenizer)
    {
        if (!$tokenizer->valid()) {
            throw new LexicalError('Invalid $end reached');
        }
        $token = $tokenizer->current();

        return $token;
    }
}
