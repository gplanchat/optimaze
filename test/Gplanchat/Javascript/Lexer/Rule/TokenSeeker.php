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

class TokenSeeker
    implements RuleInterface
{
    use TokenizerNavigationAwareTrait;

    /**
     * @var int|string
     */
    protected $tokenType = null;

    /**
     * @var string
     */
    protected $expectedValue = null;

    /**
     * @var bool
     */
    protected $isGreedy = false;

    /**
     * @param int|string $tokenType
     * @param string $expectedValue
     * @param bool $isGreedy
     */
    public function __construct($tokenType, $expectedValue, $isGreedy = false)
    {
        $this->tokenType = $tokenType;
        $this->expectedValue = $expectedValue;
        $this->isGreedy = $isGreedy;
    }

    /**
     * @return string
     */
    public function getExpectedValue()
    {
        return $this->expectedValue;
    }

    /**
     * @return int|string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @return bool
     */
    public function getIsGreedy()
    {
        return $this->isGreedy;
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Token $token */
        $token = $this->currentToken($tokenizer);

        while (true) {
            if ($token->getType() === $this->getTokenType() &&
                $token->getValue() === $this->getExpectedValue()) {
                break;
            }

            $token = $this->nextToken($tokenizer);
        }

        if ($this->getIsGreedy() === true) {
            $this->nextToken($tokenizer);
        }
    }
}
