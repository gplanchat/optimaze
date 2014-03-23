<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 12:37
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Tokenizer\Token;

/**
 * Class PrimaryExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * PrimaryExpression:
 *     ( Expression )
 *     Identifier
 *     IntegerLiteral
 *     FloatingPointLiteral
 *     StringLiteral
 *     false
 *     true
 *     null
 *     this
 */
class PrimaryExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $validTokenTypes = [
        TokenizerInterface::OP_LEFT_BRACKET,
        TokenizerInterface::TOKEN_IDENTIFIER,
        TokenizerInterface::TOKEN_NUMBER_INTEGER,
        TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT,
        TokenizerInterface::TOKEN_STRING,
        TokenizerInterface::KEYWORD_TRUE,
        TokenizerInterface::KEYWORD_FALSE,
        TokenizerInterface::KEYWORD_NULL,
        TokenizerInterface::KEYWORD_THIS
    ];

    /**
     * @param Token $token
     * @return bool
     */
    public function match(Token $token)
    {
        return in_array($token->getType(), $this->validTokenTypes);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);
        if (!$this->match($token)) {
            return;
        }

        /** @var Grammar\PrimaryExpression $node */
        $node = $this->getGrammarServiceManager()->get('PrimaryExpression');
        $parent->addChild($node);

        if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
            $this->nextToken($tokenizer);

            /** @var Expression $rule */
            $rule = $this->getRuleServiceManager()->get('Expression');
            $rule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                throw new LexicalError('Invalid expression : missing ending bracket',
                    null, $token->getLine(), $token->getStart());
            }
        } else if ($token->getType() === TokenizerInterface::TOKEN_IDENTIFIER) {
            /** @var Grammar\Identifier $child */
            $child = $this->getGrammarServiceManager()
                ->get('Identifier', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::TOKEN_NUMBER_INTEGER) {
            /** @var Grammar\IntegerLiteral $child */
            $child = $this->getGrammarServiceManager()
                ->get('IntegerLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT) {
            /** @var Grammar\FloatingPointLiteral $child */
            $child = $this->getGrammarServiceManager()
                ->get('FloatingPointLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::TOKEN_STRING) {
            /** @var Grammar\StringLiteral $child */
            $child = $this->getGrammarServiceManager()
                ->get('StringLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_FALSE) {
            /** @var Grammar\BooleanLiteral $child */
            $child = $this->getGrammarServiceManager()
                ->get('BooleanLiteral', [false])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_TRUE) {
            /** @var Grammar\BooleanLiteral $child */
            $child = $this->getGrammarServiceManager()
                ->get('BooleanLiteral', [true])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_THIS) {
            /** @var Grammar\ThisKeyword $child */
            $child = $this->getGrammarServiceManager()
                ->get('ThisKeyword')
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_NULL) {
            /** @var Grammar\NullKeyword $child */
            $child = $this->getGrammarServiceManager()
                ->get('NullKeyword')
            ;
            $node->addChild($child);
        } else {
            throw new LexicalError('Invalid expression',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }
}
