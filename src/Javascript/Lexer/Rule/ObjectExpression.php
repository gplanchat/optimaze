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

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class ObjectExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ObjectExpression:
 *     { empty }
 *     { ObjectEntryList }
 *
 * ObjectEntry:
 *     Identifier : AssignmentExpression
 *     StringLiteral : AssignmentExpression
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
        /** @var Grammar\ObjectExpression $node */
        $node = $this->grammar->get('ObjectExpression');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_CURLY) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_CURLY_BRACE,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $token = $this->nextToken($tokenizer);

        if ($token->getType() === TokenizerInterface::OP_RIGHT_CURLY) {
            $this->nextToken($tokenizer);
        } else {
            while (true) {
                if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER &&
                    $token->getType() !== TokenizerInterface::TOKEN_STRING) {
                    throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                /** @var Grammar\ObjectEntry $objectEntry */
                $objectEntry = $this->grammar->get('ObjectEntry', [$token->getValue()]);
                $node->addChild($objectEntry);

                $token = $this->nextToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_COLON) {
                    throw new LexicalError(RuleInterface::MESSAGE_MISSING_COLON,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                yield $this->getAssignmentExpressionRule()->run($objectEntry, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_COMMA) {
                    break;
                }
                $token = $this->nextToken($tokenizer);
            }

            if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
                throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
            }
            $this->nextToken($tokenizer);
        }

        $node->optimize();
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
