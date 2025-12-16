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

namespace Facebook\Tests\GraphNode;

use Facebook\GraphNode\GraphCoverPhoto;
use Facebook\GraphNode\GraphGroup;
use Facebook\GraphNode\GraphNodeFactory;
use Facebook\GraphNode\GraphPage;
use Facebook\GraphNode\GraphPicture;
use Facebook\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class GraphEventTest extends TestCase
{
    /**
     * @var ObjectProphecy|Response
     */
    protected $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = \Mockery::mock(Response::class);
    }

    public function testCoverGetsCastAsGraphCoverPhoto(): void
    {
        $dataFromGraph = [
            'cover' => ['id' => '1337'],
        ];

        $this->responseMock->shouldReceive('getDecodedBody')->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        $graphNode = $factory->makeGraphEvent();

        $cover = $graphNode->getCover();
        $this->assertInstanceOf(GraphCoverPhoto::class, $cover);
    }

    public function testPlaceGetsCastAsGraphPage(): void
    {
        $dataFromGraph = [
            'place' => ['id' => '1337'],
        ];

        $this->responseMock->shouldReceive('getDecodedBody')->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        $graphNode = $factory->makeGraphEvent();

        $place = $graphNode->getPlace();
        $this->assertInstanceOf(GraphPage::class, $place);
    }

    public function testPictureGetsCastAsGraphPicture(): void
    {
        $dataFromGraph = [
            'picture' => ['id' => '1337'],
        ];

        $this->responseMock->shouldReceive('getDecodedBody')->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        $graphNode = $factory->makeGraphEvent();

        $picture = $graphNode->getPicture();
        $this->assertInstanceOf(GraphPicture::class, $picture);
    }

    public function testParentGroupGetsCastAsGraphGroup(): void
    {
        $dataFromGraph = [
            'parent_group' => ['id' => '1337'],
        ];

        $this->responseMock->shouldReceive('getDecodedBody')->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        $graphNode = $factory->makeGraphEvent();

        $parentGroup = $graphNode->getParentGroup();
        $this->assertInstanceOf(GraphGroup::class, $parentGroup);
    }
}
