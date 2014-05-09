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
 * Class AssignmentExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * AssignmentExpression:
 *     ConditionalExpression
 *     ConditionalExpression AssignmentOperator AssignmentExpression
 */
class AssignmentExpression
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
        /** @var Grammar\AssignmentExpression $node */
        $node = $this->grammar->get('AssignmentExpression');
        $parent->addChild($node);

        /** @var AssignmentExpression $conditionalExpressionRule */
        $conditionalExpressionRule = $this->rule->get('ConditionalExpression');

        while (true) {
            yield $conditionalExpressionRule->run($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_ASSIGN) {
                break;
            }

            /** @var Grammar\AssignmentOperator $assignmentOperator */
            $assignmentOperator = $this->grammar
                ->get('AssignmentOperator', [$token->getAssignOperator()])
            ;
            $node->addChild($assignmentOperator);

            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }
}
