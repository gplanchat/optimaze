<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 29/03/14
 * Time: 17:53
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;

/**
 * Class Statement
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Statement:
 *     ;
 *     if Condition Statement
 *     if Condition Statement else Statement
 *     while Condition Statement
 *     for ( ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression in Expression ) Statement
 *     break ;
 *     continue ;
 *     with ( Expression ) Statement
 *     return Expression ;
 *     { StatementList }
 *     VariableListOrExpression ;
 */
class Statement
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\Statement $node */
        $node = $this->grammar->get('Statement');
        $parent->addChild($node);

        /** @var Rule\Expression $expressionRule */
        $expressionRule = $this->rule->get('Expression', [$this->rule, $this->grammar]);

        /** @var Rule\VariableListOrExpression $variableListOrExpressionRule */
        $variableListOrExpressionRule = $this->rule->get('VariableListOrExpression', [$this->rule, $this->grammar]);

        /** @var Rule\Condition $conditionRule */
        $conditionRule = $this->rule->get('Condition', [$this->rule, $this->grammar]);

        while (true) {
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_IF) {
                $this->parseIf($node, $tokenizer, $conditionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WHILE) {
                $this->parseWhile($node, $tokenizer, $conditionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_FOR) {
                $this->parseFor($node, $tokenizer, $expressionRule, $variableListOrExpressionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_BREAK) {
                $this->parseBreak($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_CONTINUE) {
                $this->parseContinue($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WITH) {
                $this->parseWith($node, $tokenizer, $expressionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_RETURN) {
                $this->parseReturn($node, $tokenizer, $expressionRule);
                break;
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_CURLY) {
                $this->parseCoumpoundStatement($node, $tokenizer);
                break;
            } else {
                $variableListOrExpressionRule->parse($node, $tokenizer);
            }
        }
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @param RuleInterface $conditionRule
     * @return void
     * @throws LexicalError
     */
    protected function parseIf(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer, RuleInterface $conditionRule)
    {
        /** @var Grammar\IfKeyword $ifKeyword */
        $ifKeyword = $this->grammar->get('IfKeyword');
        $parent->addChild($ifKeyword);

        $token = $this->nextToken($tokenizer);
        $conditionRule->parse($ifKeyword, $tokenizer);

        if ($token->getType() === TokenizerInterface::KEYWORD_ELSE) {
            /** @var Grammar\ElseKeyword $elseKeyword */
            $elseKeyword = $this->grammar->get('ElseKeyword');
            $ifKeyword->addChild($elseKeyword);

            $this->nextToken($tokenizer);
            $conditionRule->parse($elseKeyword, $tokenizer);
        }
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @param RuleInterface $conditionRule
     * @return void
     * @throws LexicalError
     */
    protected function parseWhile(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer, RuleInterface $conditionRule)
    {
        /** @var Grammar\WhileKeyword $whileKeyword */
        $whileKeyword = $this->grammar->get('WhileKeyword');
        $parent->addChild($whileKeyword);

        $this->nextToken($tokenizer);

        $conditionRule->parse($whileKeyword, $tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @param RuleInterface $expressionRule
     * @param RuleInterface $variableListOrExpressionRule
     * @return void
     * @throws LexicalError
     */
    protected function parseFor(
        RecursiveGrammarInterface $parent,
        TokenizerInterface $tokenizer,
        RuleInterface $expressionRule,
        RuleInterface $variableListOrExpressionRule)
    {
        /** @var Grammar\ForKeyword $forKeyword */
        $forKeyword = $this->grammar->get('ForKeyword');
        $parent->addChild($forKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError('Invalid expression : missing left bracket',
                null, $token->getLine(), $token->getStart());
        }

        $token = $this->nextToken($tokenizer);
        if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
            $token = $this->nextToken($tokenizer);
        } else {
            $variableListOrExpressionRule->parse($forKeyword, $tokenizer);
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                $this->nextToken($tokenizer);

                $expressionRule->parse($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);

                if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                    throw new LexicalError('Invalid expression : missing semicolon',
                        null, $token->getLine(), $token->getStart());
                }

                $expressionRule->parse($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);
            } else if ($token->getType() !== TokenizerInterface::KEYWORD_IN) {
                $this->nextToken($tokenizer);

                $expressionRule->parse($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);
            } else {
                throw new LexicalError('Invalid expression : missing semicolon or "in" keyword',
                    null, $token->getLine(), $token->getStart());
            }
        }

        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError('Invalid expression : missing right bracket',
                null, $token->getLine(), $token->getStart());
        }
        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseBreak(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\BreakKeyword $breakKeyword */
        $breakKeyword = $this->grammar->get('BreakKeyword');
        $parent->addChild($breakKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseContinue(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\ContinueKeyword $continueKeyword */
        $continueKeyword = $this->grammar->get('ContinueKeyword');
        $parent->addChild($continueKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @param RuleInterface $expressionRule
     * @return void
     * @throws LexicalError
     */
    protected function parseWith(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer, RuleInterface $expressionRule)
    {
        /** @var Grammar\WithKeyword $withKeyword */
        $withKeyword = $this->grammar->get('WithKeyword');
        $parent->addChild($withKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError('Invalid expression : missing left bracket',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
        $expressionRule->parse($withKeyword, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError('Invalid expression : missing right bracket',
                null, $token->getLine(), $token->getStart());
        }
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @param RuleInterface $expressionRule
     * @return void
     * @throws LexicalError
     */
    protected function parseReturn(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer, RuleInterface $expressionRule)
    {
        /** @var Grammar\ReturnKeyword $returnKeyword */
        $returnKeyword = $this->grammar->get('ReturnKeyword');
        $parent->addChild($returnKeyword);

        $this->nextToken($tokenizer);
        $expressionRule->parse($returnKeyword, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseCoumpoundStatement(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\CompoundStatement $compoundStatement */
        $compoundStatement = $this->grammar->get('CompoundStatement');
        $parent->addChild($compoundStatement);

        $this->nextToken($tokenizer);

        /** @var Rule\StatementList $statementListRule */
        $statementListRule = $this->rule->get('StatementListRule', [$this->rule, $this->grammar]);
        $statementListRule->parse($compoundStatement, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
            throw new LexicalError('Invalid expression : missing right curly brace',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }
}
