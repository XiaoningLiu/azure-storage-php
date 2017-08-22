<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\unit\Blob\Models;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions;

/**
 * Unit tests for class GetBlobPropertiesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetBlobPropertiesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new GetBlobPropertiesOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions::getAccessConditions
     */
    public function testGetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new GetBlobPropertiesOptions();
        $result->setAccessConditions($expected);
        
        // Test
        $actual = $result->getAccessConditions();
        
        // Assert
        $this->assertEquals($expected, $actual[0]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions::setAccessConditions
     */
    public function testSetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new GetBlobPropertiesOptions();
        
        // Test
        $result->setAccessConditions($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessConditions()[0]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions::setSnapshot
     */
    public function testSetSnapshot()
    {
        // Setup
        $blob = new GetBlobPropertiesOptions();
        $expected = TestResources::QUEUE1_NAME;
        
        // Test
        $blob->setSnapshot($expected);
        
        // Assert
        $this->assertEquals($expected, $blob->getSnapshot());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions::getSnapshot
     */
    public function testGetSnapshot()
    {
        // Setup
        $blob = new GetBlobPropertiesOptions();
        $expected = TestResources::QUEUE_URI;
        $blob->setSnapshot($expected);
        
        // Test
        $actual = $blob->getSnapshot();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}
