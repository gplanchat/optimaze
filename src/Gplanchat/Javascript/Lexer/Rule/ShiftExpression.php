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
 * ShiftExpression:
 *     AdditiveExpression
 *     AdditiveExpression ShiftOperator ShiftExpression
 */
class ShiftExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $shiftOperators = [
        TokenizerInterface::OP_RSH,
        TokenizerInterface::OP_LSH,
        TokenizerInterface::OP_URSH
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

        /** @var Grammar\ShiftExpression $node */
        $node = $this->getGrammarServiceManager()->get('ShiftExpression');
        $parent->addChild($node);

        /** @var AdditiveExpression $additiveExpressionRule */
        $additiveExpressionRule = $this->getRuleServiceManager()->get('AdditiveExpression');

        while (true) {
            $additiveExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), $this->shiftOperators)) {
                break;
            }

            /** @var Grammar\ShiftOperator $shiftOperator */
            $shiftOperator = $this->getGrammarServiceManager()
                ->get('ShiftOperator', [$token->getValue()])
            ;
            $node->addChild($shiftOperator);
            $this->nextToken($tokenizer);
        }
    }
}
