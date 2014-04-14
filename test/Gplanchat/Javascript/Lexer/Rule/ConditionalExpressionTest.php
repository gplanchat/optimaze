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
 * ConditionalExpression:
 *     OrExpression
 *     OrExpression ? AssignmentExpression : AssignmentExpression
 */
class ConditionalExpressionTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testCondition()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,      'a', null],
            [TokenizerInterface::OP_HOOK,               '?', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,  '1', null],
            [TokenizerInterface::OP_COLON,              ':', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,  '2', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['OrExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
            ['AssignmentExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '2', true)
                ])
            ]
        ];

        $grammarServices = [
            ['ConditionalExpression', Grammar\ConditionalExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionalExpression::class))
        ;

        $rule = new ConditionalExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['OrExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
        ];

        $grammarServices = [
            ['ConditionalExpression', Grammar\ConditionalExpression::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionalExpression::class))
        ;

        $rule = new ConditionalExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }
}
