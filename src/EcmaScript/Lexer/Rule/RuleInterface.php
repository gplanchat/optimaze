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

namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Tokenizer\TokenizerInterface;

interface RuleInterface
{
    const MESSAGE_UNEXPECTED_DOT                     = 'Unexpected dot';
    const MESSAGE_UNEXPECTED_TOKEN                   = 'Unexpected token';
    const MESSAGE_MISSING_IDENTIFIER                 = 'Missing identifier';
    const MESSAGE_MISSING_SEMICOLON                  = 'Missing semicolon';
    const MESSAGE_MISSING_SEMICOLON_OR_IN_KEYWORD    = 'Missing semicolon or "in" keyword';
    const MESSAGE_MISSING_SEMICOLON_OR_RIGHT_BRACKET = 'Missing semicolon or right bracket';
    const MESSAGE_MISSING_COLON                      = 'Missing colon';
    const MESSAGE_MISSING_LEFT_BRACKET               = 'Missing left bracket';
    const MESSAGE_MISSING_RIGHT_BRACKET              = 'Missing right bracket';
    const MESSAGE_MISSING_LEFT_CURLY_BRACE           = 'Missing left curly brace';
    const MESSAGE_MISSING_RIGHT_CURLY_BRACE          = 'Missing right curly brace';
    const MESSAGE_MISSING_LEFT_SQUARE_BRACKET        = 'Missing left square bracket';
    const MESSAGE_MISSING_RIGHT_SQUARE_BRACKET       = 'Missing right square bracket';
    const MESSAGE_MISSING_FUNCTION_KEYWORD           = 'Missing function keyword';
    const MESSAGE_MISSING_DOT                        = 'Missing dot';

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return \Generator|null
     */
    public function __invoke(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer);

    /**
     * @see self::run
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return \Generator|null
     */
    public function run(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer);
}
