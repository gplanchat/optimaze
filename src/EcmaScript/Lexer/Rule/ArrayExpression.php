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
 * Class Expression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * ArrayExpression:
 *     [ Expression ]
 */
class ArrayExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Expression
     */
    protected $expressionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\ArrayExpression $node */
        $node = $this->grammar->get('ArrayExpression');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_SQUARE_BRACKET) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_SQUARE_BRACKET,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $token = $this->nextToken($tokenizer);

        while (true) {
            yield $this->getExpressionRule()->run($node, $tokenizer, $level + 1);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_COMMA) {
                break;
            }
            $token = $this->nextToken($tokenizer);
        }

        if ($token->getType() !== TokenizerInterface::OP_RIGHT_SQUARE_BRACKET) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_SQUARE_BRACKET,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $this->nextToken($tokenizer);

        $node->optimize();
    }

    /**
     * @return Expression
     */
    public function getExpressionRule()
    {
        if ($this->expressionRule === null) {
            $this->expressionRule = $this->rule->get('Expression');
        }

        return $this->expressionRule;
    }
}
