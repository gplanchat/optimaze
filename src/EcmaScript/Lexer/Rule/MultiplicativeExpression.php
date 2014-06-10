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
 * Class MultiplicativeExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * MultiplicativeExpression:
 *     UnaryExpression
 *     UnaryExpression MultiplicativeOperator MultiplicativeExpression
 */
class MultiplicativeExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var UnaryExpression
     */
    protected $unaryExpressionRule = null;

    /**
     * @var array
     */
    protected static $multiplicativeOperators = [
        TokenizerInterface::OP_MUL,
        TokenizerInterface::OP_DIV,
        TokenizerInterface::OP_MOD
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
        /** @var Grammar\MultiplicativeExpression $node */
        $node = $this->grammar->get('MultiplicativeExpression');
        $parent->addChild($node);

        while (true) {
            yield $this->getUnaryExpressionRule()->run($node, $tokenizer, $level + 1);

            $token = $this->currentToken($tokenizer);
            if (!$token->isIn(static::$multiplicativeOperators)) {
                break;
            }

            /** @var Grammar\MultiplicativeOperator $multiplicativeOperator */
            $multiplicativeOperator = $this->grammar
                ->get('MultiplicativeOperator', [$token->getValue()])
            ;
            $node->addChild($multiplicativeOperator);
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }

    /**
     * @return UnaryExpression
     */
    public function getUnaryExpressionRule()
    {
        if ($this->unaryExpressionRule === null) {
            $this->unaryExpressionRule = $this->rule->get('UnaryExpression');
        }

        return $this->unaryExpressionRule;
    }
}
