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
 * @package Gplanchat\Javascript\Lexer
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class MemberExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * SwitchCase:
 *     case StringLiteral
 *     case IntegerLiteral
 *     case FloatingPointLiteral
 *     case Identifier
 *     default
 */
class SwitchCase
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var StatementList
     */
    protected $statementListRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\SwitchStatement $node */
        $node = $this->grammar->get('SwitchStatement');
        $parent->addChild($node);

        while (true) {
            $token = $this->currentToken($tokenizer);
            if ($token->getType() === TokenizerInterface::KEYWORD_CASE) {
                $token = $this->nextToken($tokenizer);

                /** @var Grammar\CaseKeyword $case */
                $case = $this->grammar->get('CaseKeyword');
                $node->addChild($case);

                if ($token->getType() !== TokenizerInterface::TOKEN_STRING) {
                    /** @var Grammar\StringLiteral $condition */
                    $condition = $this->grammar->get('StringLiteral', [$token->getValue()]);
                    $case->addChild($condition);
                } else if ($token->getType() !== TokenizerInterface::TOKEN_NUMBER_INTEGER) {
                    /** @var Grammar\IntegerLiteral $condition */
                    $condition = $this->grammar->get('IntegerLiteral', [$token->getValue()]);
                    $case->addChild($condition);
                } else if ($token->getType() !== TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT) {
                    /** @var Grammar\FloatingPointLiteral $condition */
                    $condition = $this->grammar->get('FloatingPointLiteral', [$token->getValue()]);
                    $case->addChild($condition);
                } else if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                    /** @var Grammar\Identifier $condition */
                    $condition = $this->grammar->get('Identifier', [$token->getValue()]);
                    $case->addChild($condition);
                } else {
                    throw new LexicalError(static::MESSAGE_UNEXPECTED_TOKEN,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
            } else if ($token->getType() === TokenizerInterface::KEYWORD_DEFAULT) {
                /** @var Grammar\DefaultKeyword $case */
                $case = $this->grammar->get('DefaultKeyword');
                $node->addChild($case);
            } else {
                break;
            }
            $token = $this->nextToken($tokenizer);

            if ($token->getType() !== TokenizerInterface::OP_COLON) {
                throw new LexicalError(static::MESSAGE_MISSING_COLON,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
            }

            $this->nextToken($tokenizer);
        }

        $node->optimize();
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
}
