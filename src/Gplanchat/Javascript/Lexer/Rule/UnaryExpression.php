<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 22/03/14
 * Time: 19:11
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\Token;
use Gplanchat\Javascript\Lexer\Grammar;

/**
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * UnaryExpression:
 **     MemberExpression
 *     UnaryOperator UnaryExpression
 *     - UnaryExpression
 **     IncrementOperator MemberExpression
 **     MemberExpression IncrementOperator
 **     new Constructor
 **     delete MemberExpression
 */
class UnaryExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $unaryOperators = [
        TokenizerInterface::OP_BITWISE_NOT,
        TokenizerInterface::KEYWORD_DELETE,
        TokenizerInterface::KEYWORD_TYPEOF,
        TokenizerInterface::KEYWORD_VOID,
        TokenizerInterface::OP_MINUS
    ];

    protected $incrementOperators = [
        TokenizerInterface::OP_INCREMENT,
        TokenizerInterface::OP_DECREMENT
    ];

    /**
     * @param Token $token
     * @return bool
     */
    public function match(Token $token)
    {
        return true;
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);
        if (!$this->match($token)) {
            return;
        }

        /** @var Grammar\UnaryExpression $node */
        $node = $this->grammar->get('UnaryExpression');
        $parent->addChild($node);

        /** @var MemberExpression $memberExpressionRule */
        $memberExpressionRule = $this->rule->get('MemberExpression');

        while (true) {
            if (!in_array($token->getType(), $this->unaryOperators)) {
                /** @var Grammar\UnaryOperator $unaryOperator */
                $unaryOperator = $this->getGrammarServiceManager()
                    ->get('UnaryOperator', [$token->getValue()])
                ;
                $node->addChild($unaryOperator);
            }

            if ($memberExpressionRule->match($token)) {
                $memberExpressionRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if (in_array($token->getType(), $this->incrementOperators)) {
                    /** @var Grammar\IncrementOperator $incrementOperator */
                    $incrementOperator = $this->getGrammarServiceManager()
                        ->get('IncrementOperator', [$token->getValue()])
                    ;
                    $node->addChild($incrementOperator);
                }
                break;
            } else if (in_array($token->getType(), $this->incrementOperators)) {
                /** @var Grammar\IncrementOperator $incrementOperator */
                $incrementOperator = $this->getGrammarServiceManager()
                    ->get('IncrementOperator', [$token->getValue()])
                ;
                $node->addChild($incrementOperator);

                $this->nextToken($tokenizer);
                $memberExpressionRule->parse($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_DELETE) {
                /** @var Grammar\DeleteKeyword $deleteKeyword */
                $deleteKeyword = $this->getGrammarServiceManager()
                    ->get('DeleteKeyword', [$token->getValue()])
                ;
                $node->addChild($deleteKeyword);

                $this->nextToken($tokenizer);
                $memberExpressionRule->parse($node, $tokenizer);
                return;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_NEW) {
                /** @var Grammar\NewKeyword $newKeyword */
                $newKeyword = $this->getGrammarServiceManager()
                    ->get('NewKeyword', [$token->getValue()])
                ;
                $node->addChild($newKeyword);

                /** @var Constructor $constructorRule */
                $constructorRule = $this->rule->get('Constructor');

                $this->nextToken($tokenizer);
                $constructorRule->parse($node, $tokenizer);
                break;
            }
        }
    }
}
