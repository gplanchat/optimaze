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
 * @package Gplanchat\EcmaScript\Lexer
 */

namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class SpecialObjectEntry
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * SpecialObjectEntry:
 *     get Identifier ( empty ) { StatementList }
 *     get Identifier ( ParameterList ) { StatementList }
 *     set Identifier ( empty ) { StatementList }
 *     set Identifier ( ParameterList ) { StatementList }
 *
 * ObjectEntryKey:
 *     Identifier
 *     StringLiteral
 *     FloatingPointLiteral
 *     IntegerLiteral
 */
class SpecialObjectEntry
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var ParameterList
     */
    protected $parameterListRule = null;

    /**
     * @var StatementList
     */
    protected $statementListRule = null;

    /**
     * @var AssignmentExpression
     */
    protected $assignmentExpressionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        $token = $this->currentToken($tokenizer);

        if ($token->is(TokenizerInterface::KEYWORD_GET)) {
            $method = $this->grammar->get('AccessorExpression', [$token->getValue()]);
        } else if ($token->is(TokenizerInterface::KEYWORD_SET)) {
            $method = $this->grammar->get('MutatorExpression', [$token->getValue()]);
        } else {
            throw new LexicalError(RuleInterface::MESSAGE_UNEXPECTED_TOKEN,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $parent->addChild($method);

        $token = $this->nextToken($tokenizer);
        if (!$token->is(TokenizerInterface::TOKEN_IDENTIFIER)) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_IDENTIFIER,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $token = $this->nextToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_LEFT_BRACKET)) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_BRACKET,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $this->nextToken($tokenizer);

        yield $this->getParameterListRule()->run($method, $tokenizer, $level + 1);

        $token = $this->currentToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_RIGHT_BRACKET)) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $token = $this->nextToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_LEFT_CURLY)) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_CURLY_BRACE,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);
        yield $this->getStatementListRule()->run($method, $tokenizer, $level + 1);

        $token = $this->currentToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_RIGHT_CURLY)) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $this->nextToken($tokenizer);
    }

    /**
     * @return Expression
     */
    public function getParameterListRule()
    {
        if ($this->parameterListRule === null) {
            $this->parameterListRule = $this->rule->get('ParameterList');
        }

        return $this->parameterListRule;
    }

    /**
     * @return Expression
     */
    public function getStatementListRule()
    {
        if ($this->statementListRule === null) {
            $this->statementListRule = $this->rule->get('StatementList');
        }

        return $this->statementListRule;
    }

    /**
     * @return Expression
     */
    public function getAssignmentExpressionRule()
    {
        if ($this->assignmentExpressionRule === null) {
            $this->assignmentExpressionRule = $this->rule->get('AssignmentExpression');
        }

        return $this->assignmentExpressionRule;
    }
} 