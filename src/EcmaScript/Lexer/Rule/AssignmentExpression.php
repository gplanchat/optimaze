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
 * Class AssignmentExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
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
     * @var ConditionalExpression
     */
    protected $conditionalExpressionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\AssignmentExpression $node */
        $node = $this->grammar->get('AssignmentExpression');
        $parent->addChild($node);

        while (true) {
            yield $this->getConditionalExpressionRule()->run($node, $tokenizer, $level + 1);

            $token = $this->currentToken($tokenizer);
            if (!$token->is(TokenizerInterface::OP_ASSIGN)) {
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

    /**
     * @return ConditionalExpression
     */
    public function getConditionalExpressionRule()
    {
        if ($this->conditionalExpressionRule === null) {
            $this->conditionalExpressionRule = $this->rule->get('ConditionalExpression');
        }

        return $this->conditionalExpressionRule;
    }
}
