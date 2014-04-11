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

use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;

/**
 * Class ConditionalExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ConditionalExpression:
 *     OrExpression
 *     OrExpression ? AssignmentExpression : AssignmentExpression
 */
class ConditionalExpression
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
        /** @var Grammar\Expression $node */
        $node = $this->grammar->get('ConditionalExpression');
        $parent->addChild($node);
//        echo $parent->dump();

        /** @var OrExpression $orExpressionRule */
        $orExpressionRule = $this->rule->get('OrExpression');;
        $orExpressionRule->parse($node, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_HOOK) {
            $node->flatten();
            return;
        }

        /** @var AssignmentExpression $assignmentExpressionRule */
        $assignmentExpressionRule = $this->rule->get('AssignmentExpression');;
        $assignmentExpressionRule->parse($node, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid conditional expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $assignmentExpressionRule->parse($node, $tokenizer);

        $this->nextToken($tokenizer);
    }
}
