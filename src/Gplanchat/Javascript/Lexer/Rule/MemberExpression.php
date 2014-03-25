<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 24/03/14
 * Time: 00:20
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\Token;

/**
 * Class MemberExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * MemberExpression:
 *     PrimaryExpression
 *     PrimaryExpression . MemberExpression
 *     PrimaryExpression [ Expression ]
 *     PrimaryExpression ( ArgumentListOpt )
 */
class MemberExpression
    implements RuleInterface
{
    use RuleTrait;

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

        /** @var Grammar\MemberExpression $node */
        $node = $this->grammar->get('MemberExpression');
        $parent->addChild($node);

        /** @var PrimaryExpression $rule */
        $rule = $this->rule->get('PrimaryExpression');
        while (true) {
            $rule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_LEFT_SQUARE_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var Expression $expressionRule */
                $expressionRule = $this->rule->get('Expression');
                $expressionRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_SQUARE_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right square bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $this->nextToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var ArgumentList $argumentListRule */
                $argumentListRule = $this->rule->get('ArgumentList');
                $argumentListRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $this->nextToken($tokenizer);
            } else if ($token->getType() !== TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->grammar
                    ->get('DotOperator')
                ;
                $node->addChild($dotOperator);
                $this->nextToken($tokenizer);
                continue;
            }
        }
    }
}
