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
 * UnaryExpression:
 *     MemberExpression
 *     UnaryOperator UnaryExpression
 *     - UnaryExpression
 *     IncrementOperator MemberExpression
 *     MemberExpression IncrementOperator
 *     new Constructor
 *     delete MemberExpression
 */
class UnaryExpressionTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testPassThrough()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_TRUE, 'true', null],
            [TokenizerInterface::TOKEN_END,      null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenSeeker(TokenizerInterface::KEYWORD_TRUE, 'true', true)],
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     * @return array
     */
    public function unaryOperatorsDataProvider()
    {
        return [
            [TokenizerInterface::OP_BITWISE_NOT, '~'],
            [TokenizerInterface::KEYWORD_TYPEOF, 'typeof'],
            [TokenizerInterface::KEYWORD_VOID,   'void'],
            [TokenizerInterface::OP_MINUS,       '-']
        ];
    }

    /**
     * @dataProvider unaryOperatorsDataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     */
    public function testUnaryOperators($operatorType, $operatorValue)
    {
        $tokens = [
            [$operatorType, $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenNullSeeker()
                ])
            ]
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
            ['UnaryOperator', Grammar\UnaryOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     * @dataProvider unaryOperatorsDataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     */
    public function testChainedUnaryOperators($operatorType, $operatorValue)
    {
        $tokens = [
            [$operatorType, $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [$operatorType, $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'b', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true),
                new Rule\TokenNullSeeker()
                ])
            ]
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
            ['UnaryOperator', Grammar\UnaryOperator::class],
            ['UnaryOperator', Grammar\UnaryOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     * @return array
     */
    public function incrementOperatorsDataProvider()
    {
        return [
            [TokenizerInterface::OP_INCREMENT, '++'],
            [TokenizerInterface::OP_DECREMENT, '--']
        ];
    }

    /**
     * @dataProvider incrementOperatorsDataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     */
    public function testPrefixedIncrementOperators($operatorType, $operatorValue)
    {
        $tokens = [
            [$operatorType, $operatorValue, null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
            ['IncrementOperator', Grammar\UnaryOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     * @dataProvider incrementOperatorsDataProvider
     * @param string|int $operatorType
     * @param string $operatorValue
     */
    public function testSuffixedIncrementOperators($operatorType, $operatorValue)
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [$operatorType, $operatorValue, null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
            ['IncrementOperator', Grammar\UnaryOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testDeleteOperator()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_DELETE,   'delete', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,      'a', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
            ['DeleteKeyword', Grammar\DeleteKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testNewOperator()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_NEW,      'new', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,   'a', null],
            [TokenizerInterface::OP_LEFT_BRACKET,    '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,   ')', null],
            [TokenizerInterface::TOKEN_END,         null, null]
        ];

        $ruleServices = [
            ['MemberExpression', new Rule\TokenNullSeeker()],
            ['Constructor', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['UnaryExpression', Grammar\UnaryExpression::class],
            ['NewKeyword', Grammar\NewKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\UnaryExpression::class))
        ;

        $rule = new UnaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
