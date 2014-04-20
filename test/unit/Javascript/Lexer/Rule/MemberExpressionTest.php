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
 * MemberExpression:
 *     PrimaryExpression
 *     PrimaryExpression . MemberExpression
 *     PrimaryExpression [ Expression ]
 *     PrimaryExpression ( ArgumentListOpt )
 */
class MemberExpressionTest
    extends AbstractRuleTest
{
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
            ['PrimaryExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['MemberExpression', Grammar\MemberExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MemberExpression::class))
        ;

        $rule = new MemberExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     *
     */
    public function testDottedChainExpressions()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_DOT,            '.', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'b', null],
            [TokenizerInterface::OP_DOT,            '.', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'c', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['PrimaryExpression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'b', true),
                new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'c', true)
                ])
            ]
        ];

        $grammarServices = [
            ['MemberExpression', Grammar\MemberExpression::class],
            ['DotOperator', Grammar\DotOperator::class],
            ['DotOperator', Grammar\DotOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MemberExpression::class))
        ;

        $rule = new MemberExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     *
     */
    public function testSquareBracketExpressions()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,         'a', null],
            [TokenizerInterface::OP_LEFT_SQUARE_BRACKET,   '[', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,     '1', null],
            [TokenizerInterface::OP_RIGHT_SQUARE_BRACKET,  ']', null],
            [TokenizerInterface::TOKEN_END,               null, null]
        ];

        $ruleServices = [
            ['PrimaryExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true)]
        ];

        $grammarServices = [
            ['MemberExpression', Grammar\MemberExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MemberExpression::class))
        ;

        $rule = new MemberExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     *
     */
    public function testSquareBracketExpressionsWithMissingRightSquareBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_SQUARE_BRACKET);

        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,         'a', null],
            [TokenizerInterface::OP_LEFT_SQUARE_BRACKET,   '[', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,     '1', null],
            [TokenizerInterface::TOKEN_END,               null, null]
        ];

        $ruleServices = [
            ['PrimaryExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true)]
        ];

        $grammarServices = [
            ['MemberExpression', Grammar\MemberExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MemberExpression::class))
        ;

        $rule = new MemberExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     *
     */
    public function testBracketExpressions()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,      'a', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,  '1', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['PrimaryExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
            ['ArgumentList', new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true)]
        ];

        $grammarServices = [
            ['MemberExpression', Grammar\MemberExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MemberExpression::class))
        ;

        $rule = new MemberExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     *
     */
    public function testBracketExpressionsWithMissingRightBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET);

        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,      'a', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,  '1', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['PrimaryExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)],
            ['ArgumentList', new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true)]
        ];

        $grammarServices = [
            ['MemberExpression', Grammar\MemberExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\MemberExpression::class))
        ;

        $rule = new MemberExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }
}
