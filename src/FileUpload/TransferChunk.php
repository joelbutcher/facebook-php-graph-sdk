<?php
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

class TransferChunk
{
    /**
     * @param int  $uploadSessionId
     * @param int  $videoId
     * @param int  $startOffset
     * @param int  $endOffset
     */
    public function __construct(private readonly File $file, private $uploadSessionId, private $videoId, private $startOffset, private $endOffset)
    {
    }

    /**
     * Return the file entity.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Return a File entity with partial content.
     */
    public function getPartialFile(): \Facebook\FileUpload\File
    {
        $maxLength = $this->endOffset - $this->startOffset;

        return new File($this->file->getFilePath(), $maxLength, $this->startOffset);
    }

    /**
     * Return upload session Id.
     *
     * @return int
     */
    public function getUploadSessionId()
    {
        return $this->uploadSessionId;
    }

    /**
     * Check whether is the last chunk.
     */
    public function isLastChunk(): bool
    {
        return $this->startOffset === $this->endOffset;
    }

    /**
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Get uploaded video Id.
     *
     * @return int
     */
    public function getVideoId()
    {
        return $this->videoId;
    }
}
