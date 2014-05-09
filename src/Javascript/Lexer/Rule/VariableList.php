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
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * VariableList:
 *     Variable
 *     Variable , VariableList
 *
 * Variable:
 *     Identifier
 *     Identifier = AssignmentExpression
 */
class VariableList
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\VariableList $node */
        $node = $this->grammar->get('VariableList');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        while (true) {
            /** @var Grammar\Variable $variable */
            $variable = $this->grammar->get('Variable');
            $node->addChild($variable);

            if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                throw new LexicalError(static::MESSAGE_MISSING_IDENTIFIER,
                    null, $token->getLine(), $token->getLineOffset(), $token->getStart());
            }

            /** @var Grammar\Identifier $identifier */
            $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
            $variable->addChild($identifier);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_ASSIGN) {
                $this->nextToken($tokenizer);

                /** @var AssignmentExpression $assignmentExpressionRule */
                $assignmentExpressionRule = $this->rule->get('AssignmentExpression');
                yield $assignmentExpressionRule->run($variable, $tokenizer);
                $token = $this->currentToken($tokenizer);
            }

            if ($token->getType() !== TokenizerInterface::OP_COMMA) {
                break;
            }
            $token = $this->nextToken($tokenizer);
        }

        $node->optimize();
    }
}
