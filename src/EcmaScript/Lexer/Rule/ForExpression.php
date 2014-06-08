<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 03/05/14
 * Time: 00:03
 */

namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\EcmaScript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class ForExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
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
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\ForKeyword $forKeyword */
        $forKeyword = $this->grammar->get('ForKeyword');
        $parent->addChild($forKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError(static::MESSAGE_MISSING_LEFT_BRACKET,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $token = $this->nextToken($tokenizer);
        if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
            $this->nextToken($tokenizer);

            yield $this->getExpressionRule()->run($parent, $tokenizer, $level + 1);

            $token = $this->currentToken($tokenizer);

            if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
            }
            $this->nextToken($tokenizer);

            yield $this->getExpressionRule()->run($parent, $tokenizer, $level + 1);

            $token = $this->currentToken($tokenizer);
        } else {
            yield $this->getVariableListOrExpressionRule()->run($forKeyword, $tokenizer, $level + 1);
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                $this->nextToken($tokenizer);

                yield $this->getExpressionRule()->run($parent, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);

                if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                    throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
                $this->nextToken($tokenizer);

                yield $this->getExpressionRule()->run($parent, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_IN) {
                $this->nextToken($tokenizer);

                yield $this->getExpressionRule()->run($forKeyword, $tokenizer, $level + 1);
                $token = $this->currentToken($tokenizer);
            } else {
                throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON_OR_IN_KEYWORD,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getLineOffset(), $token->getStart());
            }
        }

        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
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
