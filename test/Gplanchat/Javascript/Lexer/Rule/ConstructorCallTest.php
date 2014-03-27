<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 22/03/14
 * Time: 20:02
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Javascript\Lexer\Grammar;
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
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier',  0, 10, 1],
            [TokenizerInterface::TOKEN_END,                null, 11, 11, 1]
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
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleDottedIdentifiers()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier',   0, 10, 1],
            [TokenizerInterface::OP_DOT,                    '.',  10, 11, 1],
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier2', 11, 22, 1],
            [TokenizerInterface::TOKEN_END,                null,  22, 22, 1]
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
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }


    /**
     *
     */
    public function testIdentifierWithOptions()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier',   0, 10, 1],
            [TokenizerInterface::OP_LEFT_BRACKET,           '(',  10, 11, 1],
            [TokenizerInterface::OP_RIGHT_BRACKET,          ')',  11, 12, 1],
            [TokenizerInterface::TOKEN_END,                null,  12, 12, 1]
        ];

        $ruleServices = [
            ['ConstructorCall', Rule\ConstructorCall::class]
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
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }


    /**
     *
     */
    public function testInvalidToken()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing identifier');

        $tokens = [
            [TokenizerInterface::OP_LEFT_BRACKET,           '(',  0, 1, 1]
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
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
