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

namespace Facebook;

use Facebook\Authentication\AccessToken;
use Facebook\Exception\SDKException;
use Facebook\FileUpload\File;
use Facebook\FileUpload\Video;
use Facebook\Http\RequestBodyMultipart;
use Facebook\Http\RequestBodyUrlEncoded;
use Facebook\Url\UrlManipulator;

/**
 * Class Request.
 */
class Request
{
    /**
     * @var Application the Facebook app entity
     */
    protected $app;

    /**
     * @var null|string the access token to use for this request
     */
    protected $accessToken;

    /**
     * @var string the HTTP method for this request
     */
    protected $method;

    /**
     * @var string the Graph endpoint for this request
     */
    protected $endpoint;

    /**
     * @var array the headers to send with this request
     */
    protected $headers = [];

    /**
     * @var array the parameters to send with this request
     */
    protected $params = [];

    /**
     * @var array the files to send with this request
     */
    protected $files = [];

    /**
     * @var string ETag to send with this request
     */
    protected $eTag;

    /**
     * Creates a new Request entity.
     *
     * @param null|AccessToken|string $accessToken
     * @param null|string             $method
     * @param null|string             $endpoint
     * @param null|array              $params
     * @param null|string             $eTag
     * @param null|string             $graphVersion
     */
    public function __construct(?Application $app = null, $accessToken = null, $method = null, $endpoint = null, array $params = [], $eTag = null, protected $graphVersion = null)
    {
        $this->setApp($app);
        $this->setAccessToken($accessToken);
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setParams($params);
        $this->setETag($eTag);
    }

    /**
     * Set the access token for this request.
     *
     * @param null|AccessToken|string
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): static
    {
        $this->accessToken = $accessToken;
        if ($accessToken instanceof AccessToken) {
            $this->accessToken = $accessToken->getValue();
        }

        return $this;
    }

    /**
     * Sets the access token with one harvested from a URL or POST params.
     *
     * @param string $accessToken the access token
     *
     * @throws SDKException
     */
    public function setAccessTokenFromParams($accessToken): static
    {
        $existingAccessToken = $this->getAccessToken();
        if (!$existingAccessToken) {
            $this->setAccessToken($accessToken);
        } elseif ($accessToken !== $existingAccessToken) {
            throw new SDKException('Access token mismatch. The access token provided in the Request and the one provided in the URL or POST params do not match.');
        }

        return $this;
    }

    /**
     * Return the access token for this request.
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Return the access token for this request as an AccessToken entity.
     */
    public function getAccessTokenEntity(): ?\Facebook\Authentication\AccessToken
    {
        return $this->accessToken ? new AccessToken($this->accessToken) : null;
    }

    /**
     * Set the Application entity used for this request.
     */
    public function setApp(?Application $app = null): void
    {
        $this->app = $app;
    }

    /**
     * Return the Application entity used for this request.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Generate an app secret proof to sign this request.
     *
     * @return null|string
     */
    public function getAppSecretProof()
    {
        if (!$accessTokenEntity = $this->getAccessTokenEntity()) {
            return null;
        }

        return $accessTokenEntity->getAppSecretProof($this->app->getSecret());
    }

    /**
     * Validate that an access token exists for this request.
     *
     * @throws SDKException
     */
    public function validateAccessToken(): void
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            throw new SDKException('You must provide an access token.');
        }
    }

    /**
     * Set the HTTP method for this request.
     *
     * @param string
     * @param mixed $method
     */
    public function setMethod($method): void
    {
        $this->method = strtoupper((string) $method);
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Validate that the HTTP method is set.
     *
     * @throws SDKException
     */
    public function validateMethod(): void
    {
        if (!$this->method) {
            throw new SDKException('HTTP method not specified.');
        }

        if (!in_array($this->method, ['GET', 'POST', 'DELETE'])) {
            throw new SDKException('Invalid HTTP method specified.');
        }
    }

    /**
     * Set the endpoint for this request.
     *
     * @param string
     * @param mixed $endpoint
     *
     * @throws SDKException
     */
    public function setEndpoint($endpoint): static
    {
        // Harvest the access token from the endpoint to keep things in sync
        $params = UrlManipulator::getParamsAsArray($endpoint);
        if (isset($params['access_token'])) {
            $this->setAccessTokenFromParams($params['access_token']);
        }

        // Clean the token & app secret proof from the endpoint.
        $filterParams = ['access_token', 'appsecret_proof'];
        $this->endpoint = UrlManipulator::removeParamsFromUrl($endpoint, $filterParams);

        return $this;
    }

    /**
     * Return the endpoint for this request.
     *
     * @return string
     */
    public function getEndpoint()
    {
        // For batch requests, this will be empty
        return $this->endpoint;
    }

    /**
     * Generate and return the headers for this request.
     */
    public function getHeaders(): array
    {
        $headers = static::getDefaultHeaders();

        if ($this->eTag) {
            $headers['If-None-Match'] = $this->eTag;
        }

        return array_merge($this->headers, $headers);
    }

    /**
     * Set the headers for this request.
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Sets the eTag value.
     *
     * @param string $eTag
     */
    public function setETag($eTag): void
    {
        $this->eTag = $eTag;
    }

    /**
     * Set the params for this request.
     *
     *
     * @throws SDKException
     *
     */
    public function setParams(array $params = []): static
    {
        if (isset($params['access_token'])) {
            $this->setAccessTokenFromParams($params['access_token']);
        }

        // Don't let these buggers slip in.
        unset($params['access_token'], $params['appsecret_proof']);

        // @TODO Refactor code above with this
        //$params = $this->sanitizeAuthenticationParams($params);
        $params = $this->sanitizeFileParams($params);
        $this->dangerouslySetParams($params);

        return $this;
    }

    /**
     * Set the params for this request without filtering them first.
     *
     *
     */
    public function dangerouslySetParams(array $params = []): static
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Iterate over the params and pull out the file uploads.
     *
     *
     */
    public function sanitizeFileParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if ($value instanceof File) {
                $this->addFile($key, $value);
                unset($params[$key]);
            }
        }

        return $params;
    }

    /**
     * Add a file to be uploaded.
     *
     * @param string $key
     */
    public function addFile($key, File $file): void
    {
        $this->files[$key] = $file;
    }

    /**
     * Removes all the files from the upload queue.
     */
    public function resetFiles(): void
    {
        $this->files = [];
    }

    /**
     * Get the list of files to be uploaded.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Let's us know if there is a file upload with this request.
     */
    public function containsFileUploads(): bool
    {
        return $this->files !== [];
    }

    /**
     * Let's us know if there is a video upload with this request.
     */
    public function containsVideoUploads(): bool
    {
        foreach ($this->files as $file) {
            if ($file instanceof Video) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the body of the request as multipart/form-data.
     */
    public function getMultipartBody(): \Facebook\Http\RequestBodyMultipart
    {
        $params = $this->getPostParams();

        return new RequestBodyMultipart($params, $this->files);
    }

    /**
     * Returns the body of the request as URL-encoded.
     */
    public function getUrlEncodedBody(): \Facebook\Http\RequestBodyUrlEncoded
    {
        $params = $this->getPostParams();

        return new RequestBodyUrlEncoded($params);
    }

    /**
     * Generate and return the params for this request.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->params;

        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $params['access_token'] = $accessToken;
            $params['appsecret_proof'] = $this->getAppSecretProof();
        }

        return $params;
    }

    /**
     * Only return params on POST requests.
     *
     * @return array
     */
    public function getPostParams()
    {
        if ($this->getMethod() === 'POST') {
            return $this->getParams();
        }

        return [];
    }

    /**
     * The graph version used for this request.
     *
     * @return null|string
     */
    public function getGraphVersion()
    {
        return $this->graphVersion;
    }

    /**
     * Generate and return the URL for this request.
     *
     * @return string
     */
    public function getUrl()
    {
        $this->validateMethod();

        $graphVersion = UrlManipulator::forceSlashPrefix($this->graphVersion);
        $endpoint = UrlManipulator::forceSlashPrefix($this->getEndpoint());

        $url = $graphVersion.$endpoint;

        if ($this->getMethod() !== 'POST') {
            $params = $this->getParams();
            $url = UrlManipulator::appendParamsToUrl($url, $params);
        }

        return $url;
    }

    /**
     * Return the default headers that every request should use.
     */
    public static function getDefaultHeaders(): array
    {
        return [
            'User-Agent'      => 'fb-php-'.Facebook::VERSION,
            'Accept-Encoding' => '*',
        ];
    }
}
