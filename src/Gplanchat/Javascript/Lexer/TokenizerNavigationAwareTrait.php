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
 * @package Gplanchat\Tokenizer
 */

namespace Gplanchat\Javascript\Lexer;

use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Tokenizer\Token;

/**
 * Make aware a class of token navigation in the tokenizer
 *
 * @package Gplanchat\Javascript\Lexer
 */
trait TokenizerNavigationAwareTrait
{
    /**
     * @param TokenizerInterface $tokenizer
     * @return Token
     * @throws LexicalError
     */
    protected function nextToken(TokenizerInterface $tokenizer)
    {
        $tokenizer->next();
        $token = $this->currentToken($tokenizer);

        return $token;
    }

    /**
     * @param TokenizerInterface $tokenizer
     * @return Token
     * @throws LexicalError
     */
    protected function currentToken(TokenizerInterface $tokenizer)
    {
        if (!$valid = $tokenizer->valid()) {
            throw new LexicalError('Invalid $end reached');
        }
        $token = $tokenizer->current();

        return $token;
    }
}
