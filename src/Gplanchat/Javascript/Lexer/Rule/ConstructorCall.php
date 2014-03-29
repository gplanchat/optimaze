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
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\ConstructorCall $node */
        $node = $this->grammar->get('ConstructorCall');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        while (true) {
            if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                throw new LexicalError('Invalid expression : missing identifier',
                    null, $token->getLine(), $token->getStart());
            }
            /** @var Grammar\Identifier $identifier */
            $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
            $node->addChild($identifier);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var ConstructorCall $rule */
                $rule = $this->rule->get('ConstructorCall', [$this->rule, $this->grammar]);
                $rule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $token = $this->nextToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->grammar->get('DotOperator');
                $node->addChild($dotOperator);
                $token = $this->nextToken($tokenizer);
                continue;
            }
            break;
        }
    }
}
