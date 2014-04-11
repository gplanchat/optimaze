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
 * @package Gplanchat\Tokenizer
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * Element:
 *     function Identifier ( empty ) { StatementList }
 *     function Identifier ( ParameterList ) { StatementList }
 *     Statement
 */
class ElementTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testAnonymousFunctionWithEmptyParameterListWithEmptyBody()
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
            ['ParameterList', Rule\ParameterList::class],
            ['StatementList', Rule\StatementList::class],
        ];

        $grammarServices = [
            ['Element', Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testNamedFunctionWithEmptyParameterListWithEmptyBody()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,    'hello', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['ParameterList', Rule\ParameterList::class],
            ['StatementList', Rule\StatementList::class],
        ];

        $grammarServices = [
            ['Element', Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class],
            ['Identifier', Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testAnonymousFunctionWithParameterListWithEmptyBody()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['ParameterList', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')],
            ['StatementList', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_CURLY, '}')],
        ];

        $grammarServices = [
            ['Element', Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testNamedFunctionWithParameterListWithEmptyBody()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,    'hello', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['ParameterList', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')],
            ['StatementList', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_CURLY, '}')],
        ];

        $grammarServices = [
            ['Element',         Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingParameterLeftBracketLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing left bracket');

        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['Element',         Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingParameterRightBracketLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing right bracket');

        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['ParameterList', new Rule\TokenSeeker(TokenizerInterface::OP_LEFT_CURLY, '{')],
        ];

        $grammarServices = [
            ['Element',         Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingBodyLeftCurlyLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing left curly brace');

        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['ParameterList', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')],
        ];

        $grammarServices = [
            ['Element',         Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingBodyRightCurlyLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing right curly brace');

        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['ParameterList', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')],
            ['StatementList', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['Element',         Grammar\Element::class],
            ['FunctionKeyword', Grammar\FunctionKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testStatement()
    {
        $tokens = [
            [TokenizerInterface::OP_SEMICOLON,  ';', null],
            [TokenizerInterface::TOKEN_END,    null, null]
        ];

        $ruleServices = [
            ['Statement', Rule\Statement::class]
        ];

        $grammarServices = [
            ['Element', Grammar\Element::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Element::class))
        ;

        $rule = new Element($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
