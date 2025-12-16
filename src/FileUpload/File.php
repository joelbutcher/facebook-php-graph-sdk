<?php
declare(strict_types=1);

/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

namespace Facebook\FileUpload;

use Facebook\Exception\SDKException;

class File
{
    /**
     * @var resource the stream pointing to the file
     */
    protected $stream;

    /**
     * Creates a new File entity.
     *
     * @param string $path
     * @param int    $maxLength
     * @param int    $offset
     *
     * @throws SDKException
     */
    public function __construct(protected $path, private $maxLength = -1, private $offset = -1)
    {
        $this->open();
    }

    /**
     * Closes the stream when destructed.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Opens a stream for the file.
     *
     * @throws SDKException
     */
    public function open(): void
    {
        if (!$this->isRemoteFile($this->path) && !is_readable($this->path)) {
            throw new SDKException('Failed to create File entity. Unable to read resource: '.$this->path.'.');
        }

        $this->stream = fopen($this->path, 'r');

        if (!$this->stream) {
            throw new SDKException('Failed to create File entity. Unable to open resource: '.$this->path.'.');
        }
    }

    /**
     * Stops the file stream.
     */
    public function close(): void
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    /**
     * Return the contents of the file.
     *
     * @return string
     */
    public function getContents(): string|false
    {
        return stream_get_contents($this->stream, $this->maxLength, (int) $this->offset);
    }

    /**
     * Return the name of the file.
     */
    public function getFileName(): string
    {
        return basename($this->path);
    }

    /**
     * Return the path of the file.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->path;
    }

    /**
     * Return the size of the file.
     *
     * @return int
     */
    public function getSize(): int|false
    {
        return filesize($this->path);
    }

    /**
     * Return the mimetype of the file.
     *
     * @return string
     */
    public function getMimetype()
    {
        return Mimetypes::getInstance()->fromFilename($this->path) ?: 'text/plain';
    }

    /**
     * Returns true if the path to the file is remote.
     *
     * @param string $pathToFile
     */
    protected function isRemoteFile($pathToFile): bool
    {
        return preg_match('/^(https?|ftp):\/\/.*/', $pathToFile) === 1;
    }
}
