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
use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\Tokenizer\Token;

trait RuleTrait
{
    /**
     * @var ServiceManagerInterface
     */
    protected $rule = null;

    /**
     * @var ServiceManagerInterface
     */
    protected $grammar = null;

    /**
     * @param ServiceManagerInterface $ruleServiceManager
     * @param ServiceManagerInterface $grammarServiceManager
     */
    public function __construct(ServiceManagerInterface $ruleServiceManager, ServiceManagerInterface $grammarServiceManager)
    {
        $this->rule = $ruleServiceManager;
        $this->grammar = $grammarServiceManager;
    }

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
