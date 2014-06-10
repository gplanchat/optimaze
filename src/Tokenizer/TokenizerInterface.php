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

namespace Gplanchat\Tokenizer;

use Gplanchat\Tokenizer\DataSource\DataSourceInterface;

/**
 * Interface TokenizerInterface.
 * Defines the way a tokenizer should react.
 *
 * @package Gplanchat\Tokenizer
 */
interface TokenizerInterface
    extends \Iterator
{
    /**
     * Initializes the tokenizer with a DataSourceInterface.
     *
     * @param DataSourceInterface $source
     * @return $this
     */
    public function open(DataSourceInterface $source);

    /**
     * Fetch the next token from the DataSourceInterface
     *
     * @return Token|null
     */
    public function get();

    /**
     * Seek into the source to a specific point
     *
     * @param Token $tokenOffset
     * @return $this
     */
    public function seek(Token $tokenOffset);

    /**
     * @param Token $token
     * @param array $typeList
     * @return bool|null
     */
    function isPreviousTokenType(Token $token, array $typeList);

    /**
     * @param Token $token
     * @param array $typeList
     * @return bool|null
     */
    function isNextTokenType(Token $token, array $typeList);
}
