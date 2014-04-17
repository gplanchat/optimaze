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

namespace Gplanchat\Tokenizer\DataSource;

/**
 * String data source
 *
 * @package Gplanchat\Tokenizer\DataSource
 */
class String
    implements DataSourceInterface
{
    /**
     * @var string
     */
    private $contents = null;

    /**
     * @param string $contents
     */
    public function __construct($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->contents;
    }

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    public function __invoke($length = null, $offset = null)
    {
        return $this->get($length, $offset);
    }

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    public function get($length = null, $offset = null)
    {
        if ($offset === null) {
            $offset = 0;
        }

        if ($length === null) {
            return substr($this->contents, $offset);
        } else {
            return substr($this->contents, $offset, $length);
        }
    }

    public function getPath()
    {
        return '[string]';
    }
}
