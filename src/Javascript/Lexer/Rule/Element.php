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
 * @package Gplanchat\Javascript\Lexer
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Debug;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class Element
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Element:
 *     FunctionExpression ;
 *     DocComment
 *     BlockComment
 *     LineComment
 *     Statement
 */
class Element
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Statement
     */
    protected $statementRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\Element $node */
        $node = $this->grammar->get('Element');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);

        if ($token->getType() === TokenizerInterface::TOKEN_BLOCK_COMMENT) {
            /** @var Grammar\BlockComment $blockComment */
            $blockComment = $this->grammar->get('BlockComment');
            $node->addChild($blockComment);
            $this->nextToken($tokenizer);
        } else if ($token->getType() === TokenizerInterface::TOKEN_LINE_COMMENT) {
            /** @var Grammar\LineComment $lineComment */
            $lineComment = $this->grammar->get('LineComment');
            $node->addChild($lineComment);
            $this->nextToken($tokenizer);
        } else if ($token->getType() === TokenizerInterface::TOKEN_DOC_COMMENT) {
            /** @var Grammar\DocComment $docComment */
            $docComment = $this->grammar->get('DocComment');
            $node->addChild($docComment);
            $this->nextToken($tokenizer);
        } else {
            yield $this->getStatementRule()->run($node, $tokenizer, $level + 1);
        }


        $node->optimize();
    }

    /**
     * @return Statement
     */
    public function getStatementRule()
    {
        if ($this->statementRule === null) {
            $this->statementRule = $this->rule->get('Statement');
        }

        return $this->statementRule;
    }
}
