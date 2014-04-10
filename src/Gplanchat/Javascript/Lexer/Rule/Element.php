<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 29/03/14
 * Time: 17:53
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Debug;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;

/**
 * Class Element
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Element:
 *     function Identifier ( empty ) { StatementList }
 *     function Identifier ( ParameterList ) { StatementList }
 *     Statement
 */
class Element
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
        /** @var Grammar\Element $node */
        $node = $this->grammar->get('Element');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);

        if ($token->getType() === TokenizerInterface::KEYWORD_FUNCTION) {
            /** @var Grammar\FunctionKeyword $functionKeyword */
            $functionKeyword = $this->grammar->get('FunctionKeyword');
            $node->addChild($functionKeyword);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::TOKEN_IDENTIFIER) {
                /** @var Grammar\Identifier $identifier */
                $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
                $functionKeyword->addChild($identifier);

                $token = $this->nextToken($tokenizer);
            }

            if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
                throw new LexicalError('Invalid expression : missing left bracket',
                    null, $token->getLine(), $token->getStart());
            }
            $this->nextToken($tokenizer);

            /** @var Rule\ParameterList $parameterListRule */
            $parameterListRule = $this->rule->get('ParameterList');
            $parameterListRule->parse($functionKeyword, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                throw new LexicalError('Invalid expression : missing right bracket',
                    null, $token->getLine(), $token->getStart());
            }
            $token = $this->nextToken($tokenizer);

            if ($token->getType() !== TokenizerInterface::OP_LEFT_CURLY) {
                throw new LexicalError('Invalid expression : missing left curly brace',
                    null, $token->getLine(), $token->getStart());
            }
            $this->nextToken($tokenizer);


            /** @var Rule\StatementList $statementListRule */
            $statementListRule = $this->rule->get('StatementList');
            $statementListRule->parse($functionKeyword, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
                throw new LexicalError('Invalid expression : missing right curly brace',
                    null, $token->getLine(), $token->getStart());
            }
            $this->nextToken($tokenizer);
        } else {
            /** @var Rule\Statement $statementRule */
            $statementRule = $this->rule->get('Statement');;
            $statementRule->parse($node, $tokenizer);
        }
    }
}
