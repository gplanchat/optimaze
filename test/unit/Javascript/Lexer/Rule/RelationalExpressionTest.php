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

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * RelationalExpression:
 *     ShiftExpression
 *     RelationalExpression RelationalOperator ShiftExpression
 */
class RelationalExpressionTest
    extends AbstractRuleTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [TokenizerInterface::OP_GT,                      '>'],
            [TokenizerInterface::OP_GE,                      '>='],
            [TokenizerInterface::OP_LE,                      '<='],
            [TokenizerInterface::OP_LT,                       '<'],
            [TokenizerInterface::KEYWORD_INSTANCEOF, 'instanceof']
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
            ['ShiftExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true)
                ])
            ]
        ];

        $grammarServices = [
            ['RelationalExpression', Grammar\RelationalExpression::class],
            ['RelationalOperator',   Grammar\RelationalOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\RelationalExpression::class))
        ;

        $rule = new RelationalExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));
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
            ['ShiftExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'c', true)
            ])
            ]
        ];

        $grammarServices = [
            ['RelationalExpression', Grammar\RelationalExpression::class],
            ['RelationalOperator',   Grammar\RelationalOperator::class],
            ['RelationalOperator',   Grammar\RelationalOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\RelationalExpression::class))
        ;

        $rule = new RelationalExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));
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
            ['ShiftExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
        ];

        $grammarServices = [
            ['RelationalExpression', Grammar\RelationalExpression::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\RelationalExpression::class))
        ;

        $rule = new RelationalExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }
}
