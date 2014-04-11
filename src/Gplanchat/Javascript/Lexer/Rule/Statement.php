<?php
/**
 * This file is part of gplanchat/php-javascript-tokenizer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Grégory Planchat <g.planchat@gmail.com>
 * @licence GNU General Public Licence
 * @package Gplanchat\Tokenizer
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

        /** @var Rule\Expression $expressionRule */
        $expressionRule = $this->rule->get('Expression');;

        /** @var Rule\VariableListOrExpression $variableListOrExpressionRule */
        $variableListOrExpressionRule = $this->rule->get('VariableListOrExpression');;

        /** @var Rule\Condition $conditionRule */
        $conditionRule = $this->rule->get('Condition');;

        while (true) {
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
//                echo $token->dump();
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_IF) {
//                echo $token->dump();
//                return;
                $this->parseIf($node, $tokenizer, $conditionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WHILE) {
//                echo $token->dump();
//                return;
                $this->parseWhile($node, $tokenizer, $conditionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_FOR) {
//                echo $token->dump();
//                return;
                $this->parseFor($node, $tokenizer, $expressionRule, $variableListOrExpressionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_BREAK) {
//                echo $token->dump();
//                return;
                $this->parseBreak($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_CONTINUE) {
//                echo $token->dump();
//                return;
                $this->parseContinue($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WITH) {
//                echo $token->dump();
//                return;
                $this->parseWith($node, $tokenizer, $expressionRule);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_RETURN) {
//                echo $token->dump();
//                return;
                $this->parseReturn($node, $tokenizer, $expressionRule);
                break;
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_CURLY) {
//                echo $token->dump();
//                return;
                $this->parseCoumpoundStatement($node, $tokenizer);
                break;
            } else {
                $variableListOrExpressionRule->parse($node, $tokenizer);
                $token = $this->nextToken($tokenizer);
//                echo $token->dump();

                if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                    break;
                }
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
        $statementListRule = $this->rule->get('StatementListRule');;
        $statementListRule->parse($compoundStatement, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
            throw new LexicalError('Invalid expression : missing right curly brace',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }
}
