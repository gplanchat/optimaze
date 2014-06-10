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

use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class ConditionalExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
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
     * @var OrExpression
     */
    protected $orExpressionRule = null;

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
        /** @var Grammar\Expression $node */
        $node = $this->grammar->get('ConditionalExpression');
        $parent->addChild($node);

        yield $this->getOrExpressionRule()->run($node, $tokenizer, $level + 1);

        $token = $this->currentToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_HOOK)) {
            $node->optimize();
            return;
        }
        $this->nextToken($tokenizer);

        yield $this->getAssignmentExpressionRule()->run($node, $tokenizer, $level + 1);

        $token = $this->currentToken($tokenizer);
        if (!$token->is(TokenizerInterface::OP_COLON)) {
            throw new LexicalError(static::MESSAGE_MISSING_COLON,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $this->nextToken($tokenizer);

        yield $this->getAssignmentExpressionRule()->run($node, $tokenizer, $level + 1);

        $node->optimize();
    }

    /**
     * @return OrExpression
     */
    public function getOrExpressionRule()
    {
        if ($this->orExpressionRule === null) {
            $this->orExpressionRule = $this->rule->get('OrExpression');
        }

        return $this->orExpressionRule;
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
