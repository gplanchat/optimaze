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

use Gplanchat\Javascript\Lexer\Accumulator;
use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * MultiplicativeExpression:
 *     UnaryExpression
 *     UnaryExpression MultiplicativeOperator MultiplicativeExpression
 */
class MultiplicativeExpressionTest
    extends AbstractRuleTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [TokenizerInterface::OP_MUL, '*'],
            [TokenizerInterface::OP_MUL, '/'],
            [TokenizerInterface::OP_MOD, '%']
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     */
    public function testOneCondition($operatorType, $operatorValue)
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,            'a', null],
            [$operatorType,                        $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,            'b', null],
            [TokenizerInterface::TOKEN_END,                  null, null]
        ];

        $ruleServices = [
            ['UnaryExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true)
                ])
            ]
        ];

        $grammarServices = [
            ['MultiplicativeExpression', Grammar\MultiplicativeExpression::class],
            ['MultiplicativeOperator',   Grammar\MultiplicativeOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MultiplicativeExpression::class))
        ;

        $rule = new MultiplicativeExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     * @dataProvider dataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     */
    public function testMultipleConditions($operatorType, $operatorValue)
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,            'a', null],
            [$operatorType,                        $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,            'b', null],
            [$operatorType,                        $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,            'c', null],
            [TokenizerInterface::TOKEN_END,                  null, null]
        ];

        $ruleServices = [
            ['UnaryExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'c', true)
                ])
            ]
        ];

        $grammarServices = [
            ['MultiplicativeExpression', Grammar\MultiplicativeExpression::class],
            ['MultiplicativeOperator',   Grammar\MultiplicativeOperator::class],
            ['MultiplicativeOperator',   Grammar\MultiplicativeOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MultiplicativeExpression::class))
        ;

        $rule = new MultiplicativeExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testPassThrough()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['UnaryExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
        ];

        $grammarServices = [
            ['MultiplicativeExpression', Grammar\MultiplicativeExpression::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MultiplicativeExpression::class))
        ;

        $rule = new MultiplicativeExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }
}
