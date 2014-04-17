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
 * FunctionExpression:
 *     function Identifier ( empty ) { StatementList }
 *     function Identifier ( ParameterList ) { StatementList }
 *     function ( empty ) { StatementList }
 *     function ( ParameterList ) { StatementList }
 */
class FunctionExpressionTest
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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingParameterLeftBracketLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_LEFT_BRACKET);

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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingParameterRightBracketLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET);

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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingBodyLeftCurlyLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_LEFT_CURLY_BRACE);

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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testFunctionWithMissingBodyRightCurlyLexerError()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE);

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
            ['FunctionExpression', Grammar\FunctionExpression::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\FunctionExpression::class))
        ;

        $rule = new FunctionExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
