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
 * OrExpression:
 *     AndExpression
 *     AndExpression || OrExpression
 * AndExpression:
 *     BitwiseOrExpression
 *     BitwiseOrExpression && AndExpression
 * BitwiseOrExpression:
 *     BitwiseXorExpression
 *     BitwiseXorExpression | BitwiseOrExpression
 * BitwiseXorExpression:
 *     BitwiseAndExpression
 *     BitwiseAndExpression ^ BitwiseXorExpression
 * BitwiseAndExpression:
 *     EqualityExpression
 *     EqualityExpression & BitwiseAndExpression
 */
class ConditionalOperatorsTest
    extends AbstractRuleTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [TokenizerInterface::OP_OR,          '||', Rule\OrExpression::class,        'OrExpression',         Grammar\OrExpression::class,         'AndExpression'],
            [TokenizerInterface::OP_AND,         '&&', Rule\AndExpression::class,       'AndExpression',        Grammar\AndExpression::class,        'BitwiseOrExpression'],
            [TokenizerInterface::OP_BITWISE_OR,   '|', Rule\BitwiseOrExpression::class,  'BitwiseOrExpression',  Grammar\BitwiseOrExpression::class,  'BitwiseXorExpression'],
            [TokenizerInterface::OP_BITWISE_AND,  '&', Rule\BitwiseXorExpression::class, 'BitwiseXorExpression', Grammar\BitwiseXorExpression::class, 'BitwiseAndExpression'],
            [TokenizerInterface::OP_BITWISE_XOR,  '^', Rule\BitwiseAndExpression::class, 'BitwiseAndExpression', Grammar\BitwiseAndExpression::class, 'EqualityExpression']
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     * @param string $ruleClass
     * @param string $grammarService
     * @param string $grammarClass
     * @param string $childRuleService
     */
    public function testOneCondition($operatorType, $operatorValue, $ruleClass, $grammarService, $grammarClass, $childRuleService)
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,            'a', null],
            [$operatorType,                        $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,            'b', null],
            [TokenizerInterface::TOKEN_END,                  null, null]
        ];

        $ruleServices = [
            [$childRuleService, new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true)
                ])
            ]
        ];

        $grammarServices = [
            [$grammarService, $grammarClass]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf($grammarClass))
        ;

        $rule = new $ruleClass($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     * @dataProvider dataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     * @param string $ruleClass
     * @param string $grammarService
     * @param string $grammarClass
     * @param string $childRuleService
     */
    public function testMultipleConditions($operatorType, $operatorValue, $ruleClass, $grammarService, $grammarClass, $childRuleService)
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
            [$childRuleService, new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'c', true)
            ])
            ]
        ];

        $grammarServices = [
            [$grammarService, $grammarClass]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf($grammarClass))
        ;

        $rule = new $ruleClass($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     * @dataProvider dataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     * @param string $ruleClass
     * @param string $grammarService
     * @param string $grammarClass
     * @param string $childRuleService
     */
    public function testPassThrough($operatorType, $operatorValue, $ruleClass, $grammarService, $grammarClass, $childRuleService)
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            [$childRuleService, new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
        ];

        $grammarServices = [
            [$grammarService, $grammarClass]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf($grammarClass))
        ;

        $rule = new $ruleClass($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }
}
