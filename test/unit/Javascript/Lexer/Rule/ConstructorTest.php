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
 * Constructor:
 *     this . ConstructorCall
 *     ConstructorCall
 */
class ConstructorTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testCall()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_THIS,     'this', null],
            [TokenizerInterface::OP_DOT,             '.', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,    'a', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,    ')', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['ConstructorCall', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Constructor', Grammar\Constructor::class],
            ['ThisKeyword', Grammar\ThisKeyword::class],
            ['DotOperator', Grammar\DotOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Constructor::class))
        ;

        $rule = new Constructor($this->getRuleServiceManagerMock($ruleServices),
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
            [TokenizerInterface::OP_LEFT_BRACKET,   '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,  ')', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['ConstructorCall', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Constructor', Grammar\Constructor::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Constructor::class))
        ;

        $rule = new Constructor($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));

        $this->assertCount(0, $root);
    }
}
