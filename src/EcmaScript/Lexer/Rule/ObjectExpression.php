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
 * Class ObjectExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * ObjectExpression:
 *     { empty }
 *     { ObjectEntryList }
 *
 * ObjectEntry:
 *     ObjectEntryKey : AssignmentExpression
 *     ObjectEntryKey : AssignmentExpression ( empty )
 *     ObjectEntryKey : AssignmentExpression ( ParameterList )
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
 *
 * ObjectEntryList:
 *     ObjectEntry
 *     ObjectEntry , ObjectEntryList
 */
class ObjectExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var SpecialObjectEntry
     */
    protected $specialObjectEntryRule = null;

    /**
     * @var ObjectEntry
     */
    protected $objectEntryRule = null;

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
     * @var array
     */
    protected static $specialEntryKeywords = [
        TokenizerInterface::KEYWORD_GET,
        TokenizerInterface::KEYWORD_SET
    ];

    /**
     * @var array
     */
    protected static $entryKeywords = [
        TokenizerInterface::TOKEN_IDENTIFIER,
        TokenizerInterface::TOKEN_STRING,
        TokenizerInterface::TOKEN_NUMBER_INTEGER,
        TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\ObjectExpression $node */
        $node = $this->grammar->get('ObjectExpression');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_LEFT_CURLY)) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_CURLY_BRACE,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $token = $this->nextToken($tokenizer);

        if ($token->is(TokenizerInterface::OP_RIGHT_CURLY)) {
            $this->nextToken($tokenizer);
        } else {
            while (true) {
                if ($token->isIn(static::$specialEntryKeywords)) {
                    yield $this->getSpecialObjectEntryRule()->run($node, $tokenizer);
                } else if ($token->isIn(static::$entryKeywords)) {
                    yield $this->getObjectEntryRule()->run($node, $tokenizer);
                } else {
                    throw new LexicalError(RuleInterface::MESSAGE_UNEXPECTED_TOKEN,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $token = $this->currentToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_COMMA)) {
                    break;
                }
                $token = $this->nextToken($tokenizer);
            }

            if (!$token->is(TokenizerInterface::OP_RIGHT_CURLY)) {
                throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
            }
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }

    /**
     * @return SpecialObjectEntry
     */
    public function getSpecialObjectEntryRule()
    {
        if ($this->specialObjectEntryRule === null) {
            $this->specialObjectEntryRule = $this->rule->get('SpecialObjectEntry');
        }

        return $this->specialObjectEntryRule;
    }

    /**
     * @return ObjectEntry
     */
    public function getObjectEntryRule()
    {
        if ($this->objectEntryRule === null) {
            $this->objectEntryRule = $this->rule->get('ObjectEntry');
        }

        return $this->objectEntryRule;
    }

    /**
     * @return ParameterList
     */
    public function getParameterListRule()
    {
        if ($this->parameterListRule === null) {
            $this->parameterListRule = $this->rule->get('ParameterList');
        }

        return $this->parameterListRule;
    }

    /**
     * @return StatementList
     */
    public function getStatementListRule()
    {
        if ($this->statementListRule === null) {
            $this->statementListRule = $this->rule->get('StatementList');
        }

        return $this->statementListRule;
    }

    /**
     * @return AssignmentExpression
     */
    public function getAssignmentExpressionRule()
    {
        if ($this->assignmentExpressionRule === null) {
            $this->assignmentExpressionRule = $this->rule->get('AssignmentExpression');
        }

        return $this->assignmentExpressionRule;
    }
}
