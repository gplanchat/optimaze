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
 * PrimaryExpression:
 *     ( Expression )
 *     Identifier
 *     IntegerLiteral
 *     FloatingPointLiteral
 *     StringLiteral
 *     false
 *     true
 *     null
 *     this
 */
class PrimaryExpressionTest
    extends AbstractRuleTest
{
    /**
     * @return array
     */
    public function keywordsDataProvider()
    {
        return [
            [TokenizerInterface::KEYWORD_FALSE, 'false', 'BooleanLiteral', Grammar\BooleanLiteral::class],
            [TokenizerInterface::KEYWORD_TRUE,  'true',  'BooleanLiteral', Grammar\BooleanLiteral::class],
            [TokenizerInterface::KEYWORD_NULL,  'null',  'NullKeyword',    Grammar\NullKeyword::class],
            [TokenizerInterface::KEYWORD_THIS,  'this',  'ThisKeyword',    Grammar\ThisKeyword::class],
        ];
    }

    /**
     * @dataProvider keywordsDataProvider
     * @param string|int $keywordType
     * @param string $keywordValue
     * @param string $grammarService
     * @param string $grammarClass
     */
    public function testKeywords($keywordType, $keywordValue, $grammarService, $grammarClass)
    {
        $tokens = [
            [$keywordType, $keywordValue, null],
            [TokenizerInterface::TOKEN_END,      null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['PrimaryExpression', Grammar\PrimaryExpression::class],
            [$grammarService, $grammarClass]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\PrimaryExpression::class))
        ;

        $rule = new PrimaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     * @return array
     */
    public function literalsDataProvider()
    {
        return [
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,        '1',        'IntegerLiteral',       Grammar\IntegerLiteral::class],
            [TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT, '1.5',      'FloatingPointLiteral', Grammar\FloatingPointLiteral::class],
            [TokenizerInterface::TOKEN_STRING,                '"Hello!"', 'StringLiteral',        Grammar\StringLiteral::class]
        ];
    }

    /**
     * @dataProvider literalsDataProvider
     * @param string|int $keywordType
     * @param string $keywordValue
     * @param string $grammarService
     * @param string $grammarClass
     */
    public function testLiterals($keywordType, $keywordValue, $grammarService, $grammarClass)
    {
        $tokens = [
            [$keywordType, $keywordValue, null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['PrimaryExpression', Grammar\PrimaryExpression::class],
            [$grammarService, $grammarClass]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\PrimaryExpression::class))
        ;

        $rule = new PrimaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }

    /**
     *
     */
    public function testIdentifier()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['PrimaryExpression', Grammar\PrimaryExpression::class],
            ['Identifier', Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\PrimaryExpression::class))
        ;

        $rule = new PrimaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testBracketWrappedExpression()
    {
        $tokens = [
            [TokenizerInterface::OP_LEFT_BRACKET,   '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,  ')', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['PrimaryExpression', Grammar\PrimaryExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\PrimaryExpression::class))
        ;

        $rule = new PrimaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionExpressionPassThrough()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['FunctionExpression', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_CURLY, '}', true)]
        ];

        $grammarServices = [
            ['PrimaryExpression', Grammar\PrimaryExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\PrimaryExpression::class))
        ;

        $rule = new PrimaryExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
