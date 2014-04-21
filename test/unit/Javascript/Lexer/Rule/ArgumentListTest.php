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
 * ArgumentList:
 *     empty
 *     AssignmentExpression
 *     AssignmentExpression , ArgumentList
 */
class ArgumentListTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testEmptyList()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['AssignmentExpression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['ArgumentList', Grammar\ArgumentList::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ArgumentList::class))
        ;

        $rule = new ArgumentList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testOneArgumentList()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['AssignmentExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)
                ])
            ]
        ];

        $grammarServices = [
            ['ArgumentList',  Grammar\ArgumentList::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ArgumentList::class))
        ;

        $rule = new ArgumentList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleArgumentList()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_COMMA,          ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'b', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['AssignmentExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true)
                ])
            ]
        ];

        $grammarServices = [
            ['ArgumentList',  Grammar\ArgumentList::class],
            ['CommaOperator', Grammar\CommaOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ArgumentList::class))
        ;

        $rule = new ArgumentList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
