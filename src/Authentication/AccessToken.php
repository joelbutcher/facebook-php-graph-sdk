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

namespace Facebook\Authentication;

class AccessToken implements \Stringable
{
    /**
     * Date when token expires.
     *
     * @var null|\DateTime
     */
    protected $expiresAt;

    /**
     * Create a new access token entity.
     *
     * @param string $value
     * @param int    $expiresAt
     */
    public function __construct(/**
     * The access token value.
     */
    protected $value, $expiresAt = 0)
    {
        if ($expiresAt) {
            $this->setExpiresAtFromTimeStamp($expiresAt);
        }
    }

    /**
     * Generate an app secret proof to sign a request to Graph.
     *
     * @param string $appSecret the app secret
     */
    public function getAppSecretProof($appSecret): string
    {
        return hash_hmac('sha256', $this->value, $appSecret);
    }

    /**
     * Getter for expiresAt.
     *
     * @return null|\DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Determines whether or not this is an app access token.
     */
    public function isAppAccessToken(): bool
    {
        return str_contains($this->value, '|');
    }

    /**
     * Determines whether or not this is a long-lived token.
     *
     * @return bool
     */
    public function isLongLived()
    {
        if ($this->expiresAt) {
            return $this->expiresAt->getTimestamp() > time() + (60 * 60 * 2);
        }

        return $this->isAppAccessToken();
    }

    /**
     * Checks the expiration of the access token.
     */
    public function isExpired(): ?bool
    {
        if ($this->getExpiresAt() instanceof \DateTime) {
            return $this->getExpiresAt()->getTimestamp() < time();
        }

        if ($this->isAppAccessToken()) {
            return false;
        }

        return null;
    }

    /**
     * Returns the access token as a string.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the access token as a string.
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Setter for expires_at.
     *
     * @param int $timeStamp
     */
    protected function setExpiresAtFromTimeStamp($timeStamp)
    {
        $dt = new \DateTime();
        $dt->setTimestamp((int) $timeStamp);
        $this->expiresAt = $dt;
    }
}
