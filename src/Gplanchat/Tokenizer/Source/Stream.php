<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 12:06
 */

namespace Gplanchat\Tokenizer\Source;

class Stream
    implements SourceInterface
{
    /**
     * @var resource
     */
    private $stream = null;

    /**
     * @var int
     */
    private $defaultLength = 500;

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
    public function __construct($stream = null, $defaultLength = 500, $offset = 0, $readLength = 1000, $context = null)
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
    public function __call($length = null, $offset = null)
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
        if ($length === null) {
            $length = $this->defaultLength;
        }
        if ($offset === null) {
            $offset = $this->offset;
        }

        if (!$this->emptiedSource && $this->offset + $length > $this->bufferLength) {
            $data = stream_get_contents($this->stream, $this->readLength, $offset);
            $readLength = strlen($this->buffer);
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
