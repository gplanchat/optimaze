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
 * Class Constructor
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Constructor:
 *     this . ConstructorCall
 *     ConstructorCall
 */
class Constructor
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
        $token = $this->currentToken($tokenizer);

        /** @var Grammar\Constructor $node */
        $node = $this->grammar->get('Constructor');
        $parent->addChild($node);

        if ($token->getType() === TokenizerInterface::KEYWORD_THIS) {
            /** @var Grammar\ThisKeyword $thisKeyword */
            $thisKeyword = $this->grammar
                ->get('ThisKeyword')
            ;
            $node->addChild($thisKeyword);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_DOT) {
                throw new LexicalError('Invalid expression : missing constructor call',
                    null, $token->getLine(), $token->getStart());
            }

            /** @var Grammar\DotOperator $dotOperator */
            $dotOperator = $this->grammar
                ->get('DotOperator')
            ;
            $node->addChild($dotOperator);

            $this->nextToken($tokenizer);
        }

        /** @var ConstructorCall $rule */
        $rule = $this->rule->get('ConstructorCall', [$this->rule, $this->grammar]);
        $rule->parse($node, $tokenizer);
    }
}
