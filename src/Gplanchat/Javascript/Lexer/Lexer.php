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

namespace Gplanchat\Javascript\Lexer;

use Gplanchat\Javascript\Lexer\TokenizerNavigationAwaretrait;
use Gplanchat\Javascript\Lexer\Grammar\GrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\LexerInterface;
use Gplanchat\ServiceManager\ServiceManagerInterface;

/**
 * Javascript lexer
 *
 * @package Gplanchat\Javascript\Lexer
 */
class Lexer
    implements LexerInterface
{
    use TokenizerNavigationAwaretrait;

    /**
     * @var ServiceManagerInterface
     */
    protected $rule = null;

    /**
     * @var ServiceManagerInterface
     */
    protected $grammar = null;

    /**
     * @var array
     */
    protected $defaultRuleServiceList = [
        'AdditiveExpression'       => Rule\AdditiveExpression::class,
        'AndExpression'            => Rule\AndExpression::class,
        'ArgumentList'             => Rule\ArgumentList::class,
        'AssignmentExpression'     => Rule\AssignmentExpression::class,
        'BitwiseAndExpression'     => Rule\BitwiseAndExpression::class,
        'BitwiseOrExpression'      => Rule\BitwiseOrExpression::class,
        'BitwiseXorExpression'     => Rule\BitwiseXorExpression::class,
        'ConditionalExpression'    => Rule\ConditionalExpression::class,
        'Constructor'              => Rule\Constructor::class,
        'ConstructorCall'          => Rule\ConstructorCall::class,
        'EqualityExpression'       => Rule\EqualityExpression::class,
        'Expression'               => Rule\Expression::class,
        'MemberExpression'         => Rule\MemberExpression::class,
        'MultiplicativeExpression' => Rule\MultiplicativeExpression::class,
        'OrExpression'             => Rule\OrExpression::class,
        'PrimaryExpression'        => Rule\PrimaryExpression::class,
        'RelationalExpression'     => Rule\RelationalExpression::class,
        'ShiftExpression'          => Rule\ShiftExpression::class,
        'UnaryExpression'          => Rule\UnaryExpression::class,
    ];

    /**
     * @var array
     */
    protected $defaultGrammarServiceList = [
        'AdditiveExpression'       => Grammar\AdditiveExpression::class,
        'AdditiveOperator'         => Grammar\AdditiveOperator::class,
        'AndExpression'            => Grammar\AndExpression::class,
        'ArgumentList'             => Grammar\ArgumentList::class,
        'AssignmentOperator'       => Grammar\AssignmentOperator::class,
        'BitwiseAndExpression'     => Grammar\BitwiseAndExpression::class,
        'BitwiseOrExpression'      => Grammar\BitwiseOrExpression::class,
        'BitwiseXorExpression'     => Grammar\BitwiseXorExpression::class,
        'BooleanLiteral'           => Grammar\BooleanLiteral::class,
        'CommaOperator'            => Grammar\CommaOperator::class,
        'ConditionalExpression'    => Grammar\ConditionalExpression::class,
        'Constructor'              => Grammar\Constructor::class,
        'ConstructorCall'          => Grammar\ConstructorCall::class,
        'DeleteKeyword'            => Grammar\DeleteKeyword::class,
        'DotOperator'              => Grammar\DotOperator::class,
        'EqualityExpression'       => Grammar\EqualityExpression::class,
        'EqualityOperator'         => Grammar\EqualityOperator::class,
        'Expression'               => Grammar\Expression::class,
        'FloatingPointLiteral'     => Grammar\FloatingPointLiteral::class,
        'Identifier'               => Grammar\Identifier::class,
        'IncrementOperator'        => Grammar\IncrementOperator::class,
        'IntegerLiteral'           => Grammar\IntegerLiteral::class,
        'MemberExpression'         => Grammar\MemberExpression::class,
        'MultiplicativeExpression' => Grammar\MultiplicativeExpression::class,
        'MultiplicativeOperator'   => Grammar\MultiplicativeOperator::class,
        'NewKeyword'               => Grammar\NewKeyword::class,
        'NullKeyword'              => Grammar\NullKeyword::class,
        'OrExpression'             => Grammar\OrExpression::class,
        'PrimaryExpression'        => Grammar\PrimaryExpression::class,
        'RelationalExpression'     => Grammar\RelationalExpression::class,
        'RelationalOperator'       => Grammar\RelationalOperator::class,
        'ShiftExpression'          => Grammar\ShiftExpression::class,
        'ShiftOperator'            => Grammar\ShiftOperator::class,
        'StringLiteral'            => Grammar\StringLiteral::class,
        'ThisKeyword'              => Grammar\ThisKeyword::class,
        'UnaryExpression'          => Grammar\UnaryExpression::class,
        'UnaryOperator'            => Grammar\UnaryOperator::class,
    ];

    /**
     * @var array
     */
    protected $operatorPrecedence = [
        TokenizerInterface::OP_SEMICOLON => 0,
        TokenizerInterface::OP_COMMA => 1,
        TokenizerInterface::OP_EQ => 2,
        TokenizerInterface::OP_HOOK => 2,
        TokenizerInterface::OP_COLON => 2,
        TokenizerInterface::OP_OR => 4,
        TokenizerInterface::OP_AND => 5,
        TokenizerInterface::OP_BITWISE_OR => 6,
        TokenizerInterface::OP_BITWISE_XOR => 7,
        TokenizerInterface::OP_BITWISE_AND => 8,
        TokenizerInterface::OP_EQ => 9,
        TokenizerInterface::OP_NE => 9,
        TokenizerInterface::OP_STRICT_EQ => 9,
        TokenizerInterface::OP_STRICT_NE => 9,
        TokenizerInterface::OP_LT => 10,
        TokenizerInterface::OP_LE => 10,
        TokenizerInterface::OP_GE => 10,
        TokenizerInterface::OP_GT => 10,
        TokenizerInterface::KEYWORD_IN => 10,
        TokenizerInterface::KEYWORD_INSTANCEOF => 10,
        TokenizerInterface::OP_LSH => 11,
        TokenizerInterface::OP_RSH => 11,
        TokenizerInterface::OP_URSH => 11,
        TokenizerInterface::OP_PLUS => 12,
        TokenizerInterface::OP_MINUS => 12,
        TokenizerInterface::OP_MUL => 13,
        TokenizerInterface::OP_DIV => 13,
        TokenizerInterface::OP_MOD => 13,
        TokenizerInterface::KEYWORD_DELETE => 14,
        TokenizerInterface::KEYWORD_VOID => 14,
        TokenizerInterface::KEYWORD_TYPEOF => 14,
        TokenizerInterface::OP_NOT => 14,
        TokenizerInterface::OP_BITWISE_NOT => 14,
        TokenizerInterface::OP_UNARY_PLUS => 14,
        TokenizerInterface::OP_UNARY_MINUS => 14,
        TokenizerInterface::OP_INCREMENT => 15,
        TokenizerInterface::OP_DECREMENT => 15,
        TokenizerInterface::KEYWORD_NEW => 16,
        TokenizerInterface::OP_DOT => 17,
        TokenizerInterface::JS_NEW_WITH_ARGS => 0,
        TokenizerInterface::JS_INDEX => 0,
        TokenizerInterface::JS_CALL => 0,
        TokenizerInterface::JS_ARRAY_INIT => 0,
        TokenizerInterface::JS_OBJECT_INIT => 0,
        TokenizerInterface::JS_GROUP => 0
    ];

    /**
     * @var array
     */
    protected $operatorArity = [
        TokenizerInterface::OP_COMMA => -2,
        TokenizerInterface::OP_ASSIGN => 2,
        TokenizerInterface::OP_HOOK => 3,
        TokenizerInterface::OP_OR => 2,
        TokenizerInterface::OP_AND => 2,
        TokenizerInterface::OP_BITWISE_OR => 2,
        TokenizerInterface::OP_BITWISE_XOR => 2,
        TokenizerInterface::OP_BITWISE_AND => 2,
        TokenizerInterface::OP_EQ => 2,
        TokenizerInterface::OP_NE => 2,
        TokenizerInterface::OP_STRICT_EQ => 2,
        TokenizerInterface::OP_STRICT_NE => 2,
        TokenizerInterface::OP_LT => 2,
        TokenizerInterface::OP_LE => 2,
        TokenizerInterface::OP_GE => 2,
        TokenizerInterface::OP_GT => 2,
        TokenizerInterface::KEYWORD_IN => 2,
        TokenizerInterface::KEYWORD_INSTANCEOF => 2,
        TokenizerInterface::OP_LSH => 2,
        TokenizerInterface::OP_RSH => 2,
        TokenizerInterface::OP_URSH => 2,
        TokenizerInterface::OP_PLUS => 2,
        TokenizerInterface::OP_MINUS => 2,
        TokenizerInterface::OP_MUL => 2,
        TokenizerInterface::OP_DIV => 2,
        TokenizerInterface::OP_MOD => 2,
        TokenizerInterface::KEYWORD_DELETE => 1,
        TokenizerInterface::KEYWORD_VOID => 1,
        TokenizerInterface::KEYWORD_TYPEOF => 1,
        TokenizerInterface::OP_NOT => 1,
        TokenizerInterface::OP_BITWISE_NOT => 1,
        TokenizerInterface::OP_UNARY_PLUS => 1,
        TokenizerInterface::OP_UNARY_MINUS => 1,
        TokenizerInterface::OP_INCREMENT => 1,
        TokenizerInterface::OP_DECREMENT => 1,
        TokenizerInterface::KEYWORD_NEW => 1,
        TokenizerInterface::OP_DOT => 2,
        TokenizerInterface::JS_NEW_WITH_ARGS => 2,
        TokenizerInterface::JS_INDEX => 2,
        TokenizerInterface::JS_CALL => 2,
        TokenizerInterface::JS_ARRAY_INIT => 1,
        TokenizerInterface::JS_OBJECT_INIT => 1,
        TokenizerInterface::JS_GROUP => 1,
        TokenizerInterface::TOKEN_BLOCK_COMMENT => 1,
        TokenizerInterface::TOKEN_LINE_COMMENT => 1
    ];

    /**
     * @param ServiceManagerInterface $ruleServiceManager
     * @param ServiceManagerInterface $grammarServiceManager
     */
    public function __construct(
        ServiceManagerInterface $ruleServiceManager = null,
        ServiceManagerInterface $grammarServiceManager = null)
    {
        if ($grammarServiceManager === null) {
            $this->grammar = new Grammar\ServiceManager();
        } else {
            $this->grammar = $grammarServiceManager;
        }

        if ($ruleServiceManager === null) {
            $this->rule = new Rule\ServiceManager($this->grammar);
        } else {
            $this->rule = $ruleServiceManager;
        }
    }

    /**
     * @return array
     */
    public function getOperatorArity()
    {
        return $this->operatorArity;
    }

    /**
     * @return array
     */
    public function getOperatorPrecedence()
    {
        return $this->operatorPrecedence;
    }

    /**
     * @param TokenizerInterface $tokenizer
     * @return GrammarInterface
     * @throws
     *
     * Program:
     *     empty
     *     Element Program
     */
    public function parse(TokenizerInterface $tokenizer)
    {
        /** @var Grammar\Program $program */
        $program = $this->grammar->get('Program');

        /** @var Rule\Element $elementRule */
        $elementRule = $this->rule->get('Element', [$this->rule, $this->grammar]);

        $token = $this->currentToken($tokenizer);
        while ($tokenizer->valid()) {
            if ($token->getType() === TokenizerInterface::TOKEN_BLOCK_COMMENT ||
                $token->getType() === TokenizerInterface::TOKEN_LINE_COMMENT) {
                $token = $this->nextToken($tokenizer);
                continue;
            }
            $elementRule->parse($program, $tokenizer);
            break;
        }

        return $program;
    }
}
