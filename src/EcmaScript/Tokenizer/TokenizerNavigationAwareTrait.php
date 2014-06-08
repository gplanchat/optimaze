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
 * @package Gplanchat\EcmaScript\Lexer
 */

namespace Gplanchat\EcmaScript\Tokenizer;

use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;
use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Tokenizer\Token;

/**
 * Make aware a class of token navigation in the tokenizer
 *
 * @package Gplanchat\EcmaScript\Lexer
 */
trait TokenizerNavigationAwareTrait
{
    /**
     * @param BaseTokenizerInterface $tokenizer
     * @param bool $ignoreNewLine
     * @return Token
     * @throws LexicalError
     */
    protected function nextToken(BaseTokenizerInterface $tokenizer, $ignoreNewLine = true)
    {
        $tokenizer->next();
        $token = $this->currentToken($tokenizer, $ignoreNewLine);

        return $token;
    }

    /**
     * @param BaseTokenizerInterface $tokenizer
     * @param bool $ignoreNewLine
     * @return Token
     * @throws LexicalError
     */
    protected function currentToken(BaseTokenizerInterface $tokenizer, $ignoreNewLine = true)
    {
        if (!$tokenizer->valid()) {
            throw new LexicalError('Invalid $end reached');
        }
        $token = $tokenizer->current();

        while (true) {
            if ($token->getType() !== TokenizerInterface::TOKEN_BLOCK_COMMENT &&
                $token->getType() !== TokenizerInterface::TOKEN_LINE_COMMENT &&
                $token->getType() !== TokenizerInterface::TOKEN_DOC_COMMENT &&
                ($ignoreNewLine === false || $token->getType() !== TokenizerInterface::TOKEN_NEWLINE)
            ) {
                break;
            }
            $tokenizer->next();

            if (!$tokenizer->valid()) {
                throw new LexicalError('Invalid $end reached');
            }
            $token = $tokenizer->current();
        }

        return $token;
    }
}
