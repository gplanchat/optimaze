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
 * Stream data source
 *
 * @package Gplanchat\Tokenizer\DataSource
 */
class Stream
    implements DataSourceInterface
{
    /**
     * @var resource
     */
    private $stream = null;

    /**
     * @var int
     */
    private $defaultLength = 20;

    /**
     * @var int
     */
    private $readLength = 1000;

    /**
     * @var int
     */
    private $bufferLength = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var string
     */
    private $buffer = null;

    /**
     * @var string
     */
    private $path = null;

    /**
     * @var bool
     */
    private $emptiedSource = false;

    /**
     * @param resource|string $stream
     * @param int $defaultLength
     * @param int $offset
     * @param int $readLength
     * @param resource $context
     */
    public function __construct($stream = null, $defaultLength = 20, $offset = 0, $readLength = 1000, $context = null)
    {
        $this->defaultLength = $defaultLength;
        $this->offset = $offset;
        $this->readLength = $readLength;

        if (is_string($stream)) {
            $this->open($stream, $context);
        } else if ($stream !== null) {
            $this->path = strval($stream);
            $this->stream = $stream;
        }
    }

    /**
     * @param string $path
     * @param resource $context
     * @return $this
     */
    public function open($path, $context)
    {
        $this->path = $path;
        $this->stream = fopen($path, 'r', $context);

        return $this;
    }

    /**
     * @param resource $stream
     * @return $this
     * @throws \RuntimeException
     */
    public function using($stream)
    {
        if (!is_resource($stream)) {
            throw new \RuntimeException('Stream expected.');
        }

        $this->path = (string) $stream;
        $this->stream = $stream;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
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
        if (feof($this->stream)) {
            return '';
        }

        if ($length === null) {
            $length = $this->defaultLength;
        }

        if ($length > $this->bufferLength) {
            $read = stream_get_contents($this->stream, max($length - $this->bufferLength, $this->readLength));
            $this->buffer .= $read;
            $this->bufferLength += strlen($read);
        }

        if ($length === null) {
            return substr($this->buffer, $offset);
        } else {
            return substr($this->buffer, $offset, $length);
        }






        if (!$this->emptiedSource && ($this->offset + $length) > $this->bufferLength) {
            $data = stream_get_contents($this->stream, $this->readLength);
            $readLength = strlen($data);
            if ($readLength > 0) {
                $this->buffer .= $data;
                $this->bufferLength += strlen($this->buffer);
            } else {
                $this->emptiedSource = true;
            }
        }

        $data = substr($this->buffer, $this->offset, $length);
        $this->offset += strlen($data);

        return $data;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
