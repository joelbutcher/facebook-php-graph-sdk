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

namespace Facebook\Http;

use Facebook\BatchRequest;
use Facebook\Exception\SDKException;
use Facebook\Request;
use Facebook\Response;

/**
 * Interface for HTTP client implementations.
 */
interface ClientInterface
{
    /**
     * Prepares the request for sending to the client handler.
     *
     * @return array{0: string, 1: string, 2: array, 3: string}
     */
    public function prepareRequestMessage(Request $request): array;

    /**
     * Makes the request to Graph and returns the result.
     *
     * @throws SDKException
     */
    public function sendRequest(Request $request): Response;

    /**
     * Makes a batched request to Graph and returns the result.
     *
     * @throws SDKException
     */
    public function sendBatchRequest(BatchRequest $request): \Facebook\BatchResponse;
}

