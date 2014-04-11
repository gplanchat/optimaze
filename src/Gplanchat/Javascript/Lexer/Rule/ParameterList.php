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
 * ParameterList:
 *     empty
 *     Identifier
 *     Identifier , ParameterList
 */
class ParameterList
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
        /** @var Grammar\ParameterList $node */
        $node = $this->grammar->get('ParameterList');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        while (true) {
            if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                break;
            }

            /** @var Grammar\Identifier $identifier */
            $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
            $node->addChild($identifier);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_COMMA) {
                continue;
            }
        }
    }
}
