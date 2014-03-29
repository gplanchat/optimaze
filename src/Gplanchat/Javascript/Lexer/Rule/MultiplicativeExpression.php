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
 * Class MultiplicativeExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * MultiplicativeExpression:
 *     UnaryExpression
 *     UnaryExpression MultiplicativeOperator MultiplicativeExpression
 */
class MultiplicativeExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $multiplicativeOperators = [
        TokenizerInterface::OP_MUL,
        TokenizerInterface::OP_DIV,
        TokenizerInterface::OP_MOD
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\MultiplicativeExpression $node */
        $node = $this->grammar->get('MultiplicativeExpression');
        $parent->addChild($node);

        /** @var UnaryExpression $unaryExpressionRule */
        $unaryExpressionRule = $this->rule->get('UnaryExpression');

        while (true) {
            $unaryExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), $this->multiplicativeOperators)) {
                break;
            }

            /** @var Grammar\MultiplicativeOperator $multiplicativeOperator */
            $multiplicativeOperator = $this->grammar
                ->get('MultiplicativeOperator', [$token->getValue()])
            ;
            $node->addChild($multiplicativeOperator);
            $this->nextToken($tokenizer);
        }
    }
}
