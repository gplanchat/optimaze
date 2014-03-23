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
 * ConstructorCall:
 *     Identifier
 *     Identifier ( ArgumentListOpt )
 *     Identifier . ConstructorCall
 */
class ConstructorCall
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

        /** @var Grammar\ConstructorCall $node */
        $node = $this->getGrammarServiceManager()->get('ConstructorCall');
        $parent->addChild($node);

        while (true) {
            /** @var Grammar\Identifier $identifier */
            $identifier = $this->getGrammarServiceManager()
                ->get('Identifier', [$token->getValue()])
            ;
            $node->addChild($identifier);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var ConstructorCall $rule */
                $rule = $this->getRuleServiceManager()->get('ConstructorCall');
                $rule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $this->nextToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->getGrammarServiceManager()
                    ->get('DotOperator')
                ;
                $node->addChild($dotOperator);
                $this->nextToken($tokenizer);
                continue;
            }
            break;
        }
    }
}
