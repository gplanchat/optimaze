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
 * ConstructorCall:
 *     Identifier
 *     Identifier ( ArgumentListOpt )
 *     Identifier . ConstructorCall
 */
class ConstructorCallTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testLoneIdentifier()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleDottedIdentifiers()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_DOT,            '.', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'b', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class],
            ['DotOperator',     Grammar\DotOperator::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }


    /**
     *
     */
    public function testIdentifierWithOptions()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_LEFT_BRACKET,   '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,  ')', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['ArgumentList', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));


        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testInvalidTokenMissingIdentifier()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_IDENTIFIER);

        $tokens = [
            [TokenizerInterface::OP_LEFT_BRACKET,  '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET, ')', null]
        ];

        $ruleServices = [];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));


        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testInvalidTokenMissingRightBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET);

        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier', null],
            [TokenizerInterface::OP_LEFT_BRACKET,           '(', null],
            [TokenizerInterface::TOKEN_END,                null, null]
        ];

        $ruleServices = [
            ['ArgumentList', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));


        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
