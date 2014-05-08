<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 03/05/14
 * Time: 00:03
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class ForExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ForExpression:
 *     for ( ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression in Expression ) Statement
 */
class ForExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Rule\RuleInterface
     */
    protected $conditionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $variableListOrExpressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $expressionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function __invoke(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\ForKeyword $forKeyword */
        $forKeyword = $this->grammar->get('ForKeyword');
        $parent->addChild($forKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError(static::MESSAGE_MISSING_LEFT_BRACKET,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $token = $this->nextToken($tokenizer);
        if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
            $this->nextToken($tokenizer);

            $rule = $this->getExpressionRule();
            yield $rule($parent, $tokenizer);

            $token = $this->currentToken($tokenizer);

            if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                    null, $token->getLine(), $token->getLineOffset(), $token->getStart());
            }

            $rule = $this->getExpressionRule();
            yield $rule($parent, $tokenizer);

            $token = $this->currentToken($tokenizer);
        } else {
            $rule = $this->getVariableListOrExpressionRule();
            yield $rule($forKeyword, $tokenizer);
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                $this->nextToken($tokenizer);

                $rule = $this->getExpressionRule();
                yield $rule($parent, $tokenizer);

                $token = $this->currentToken($tokenizer);

                if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                    throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                        null, $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $rule = $this->getExpressionRule();
                yield $rule($parent, $tokenizer);

                $token = $this->currentToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_IN) {
                $this->nextToken($tokenizer);

                $rule = $this->getExpressionRule();
                yield $rule($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);
            } else {
                throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON_OR_IN_KEYWORD,
                    null, $token->getLine(), $token->getLineOffset(), $token->getLineOffset(), $token->getStart());
            }
        }

        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $this->nextToken($tokenizer);
    }

    /**
     * @return Rule\RuleInterface|Rule\Condition
     */
    public function getConditionRule()
    {
        if ($this->conditionRule === null) {
            $this->conditionRule = $this->rule->get('Condition');
        }

        return $this->conditionRule;
    }

    /**
     * @return Rule\RuleInterface|Rule\VariableListOrExpression
     */
    public function getVariableListOrExpressionRule()
    {
        if ($this->variableListOrExpressionRule === null) {
            $this->variableListOrExpressionRule = $this->rule->get('VariableListOrExpression');
        }

        return $this->variableListOrExpressionRule;
    }

    /**
     * @return Rule\RuleInterface|Rule\Expression
     */
    public function getExpressionRule()
    {
        if ($this->expressionRule === null) {
            $this->expressionRule = $this->rule->get('Expression');
        }
        return $this->expressionRule;
    }
}
