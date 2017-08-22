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
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Samples
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Samples;

require_once "../vendor/autoload.php";

use MicrosoftAzure\Storage\Table\Models\BatchOperations;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;
use MicrosoftAzure\Storage\Table\Models\QueryTablesOptions;
use MicrosoftAzure\Storage\Table\Models\Filters\Filter;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

$connectionString = 'DefaultEndpointsProtocol=https;AccountName=<yourAccount>;AccountKey=<yourKey>';
$tableClient = ServicesBuilder::getInstance()->createTableService($connectionString);

// Get and Set Table Service Properties
tableServicePropertiesSample($tableClient);

// To create a table call createTable.
createTableSample($tableClient);

// To add an entity to a table, create a new Entity object and pass it to TableRestProxy->insertEntity.
// Note that when you create an entity you must specify a PartitionKey and RowKey. These are the unique
// identifiers for an entity and are values that can be queried much faster than other entity properties.
// The system uses PartitionKey to automatically distribute the table's entities over many storage nodes.
insertEntitySample($tableClient);

// The TableRestProxy->getEntity method allows you to retrieve a single
// entity by querying for its PartitionKey and RowKey. In the example below,
// the partition key 'pk' and row key '1' are passed to the getEntity method.
getSingleEntitySample($tableClient);

// To add mutiple entities with one call, create a BatchOperations and pass it to TableRestProxy->batch.
// Note that all these entities must have the same PartitionKey value. BatchOperations supports to update,
// merge, delete entities as well. You can find more details in:
//   https://msdn.microsoft.com/library/azure/dd894038.aspx
batchInsertEntitiesSample($tableClient);

// Entity queries are constructed using filters (for more information,
// see Querying Tables and Entities). To retrieve all entities in partition,
// use the filter "PartitionKey eq partition_name". The following example shows
// how to retrieve all entities in the tasksSeattle partition by passing a
// filter to the queryEntities method.
queryAllEntitiesInPartition($tableClient);

// The same pattern used in the previous example can be used to retrieve any
// subset of entities in a partition. The subset of entities you retrieve are
// determined by the filter you use (for more information, see Querying Tables
// and Entities).The following example shows how to use a filter to retrieve
// all entities with a specific Location and a DueDate less than a specified
// date.
querySubsetEntitiesSample($tableClient);

// An existing entity can be updated by using the Entity->setProperty and
// Entity->addProperty methods on the entity, and then calling
// TableRestProxy->updateEntity. The following example retrieves an entity,
// modifies one property, removes another property, and adds a new property.
// Note that you can remove a property by setting its value to null.
updateEntitySample($tableClient);

// To delete an entity, pass the table name, and the entity's PartitionKey
// and RowKey to the TableRestProxy->deleteEntity method.
deleteEntitySample($tableClient);

// Finally, to delete a table, pass the table name to the
// TableRestProxy->deleteTable method.
deleteTableSample($tableClient);

function listTables($tableService)
{
    $tablePrefix = "table".generateRandomString();
      
    echo "Create multiple tables with prefix {$tablePrefix}".PHP_EOL;
    for ($i = 1; $i <= 5; $i++) {
        $tableService->createTable($tablePrefix.(string)$i);
    }
    echo "List tables with prefix {$tablePrefix}".PHP_EOL;
    $queryTablesOptions = new QueryTablesOptions();
    $queryTablesOptions->setPrefix($tablePrefix);
    $tablesListResult = $tableService->queryTables($queryTablesOptions);
    foreach ($tablesListResult->getTables() as $table) {
        echo "  table ".$table.PHP_EOL;
    }
    echo "Delete tables with prefix {$tablePrefix}".PHP_EOL;
    for ($i = 1; $i <= 5; $i++) {
        $tableService->deleteTable($tablePrefix.(string)$i);
    }
}

function tableServicePropertiesSample($tableClient)
{
    // Get table service properties
    echo "Get Table Service properties" . PHP_EOL;
    $originalProperties = $tableClient->getServiceProperties();
    // Set table service properties
    echo "Set Table Service properties" . PHP_EOL;
    $retentionPolicy = new RetentionPolicy();
    $retentionPolicy->setEnabled(true);
    $retentionPolicy->setDays(10);
    
    $logging = new Logging();
    $logging->setRetentionPolicy($retentionPolicy);
    $logging->setVersion('1.0');
    $logging->setDelete(true);
    $logging->setRead(true);
    $logging->setWrite(true);
    
    $metrics = new Metrics();
    $metrics->setRetentionPolicy($retentionPolicy);
    $metrics->setVersion('1.0');
    $metrics->setEnabled(true);
    $metrics->setIncludeAPIs(true);
    $serviceProperties = new ServiceProperties();
    $serviceProperties->setLogging($logging);
    $serviceProperties->setHourMetrics($metrics);
    $tableClient->setServiceProperties($serviceProperties);
    
    // revert back to original properties
    echo "Revert back to original service properties" . PHP_EOL;
    $tableClient->setServiceProperties($originalProperties->getValue());
    echo "Service properties sample completed" . PHP_EOL;
}

function createTableSample($tableClient)
{
    try {
        // Create table.
        $tableClient->createTable("mytable");
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function insertEntitySample($tableClient)
{
    $entity = new Entity();
    $entity->setPartitionKey("pk");
    $entity->setRowKey("1");
    $entity->addProperty("PropertyName", EdmType::STRING, "Sample1");
    
    try {
        $tableClient->insertEntity("mytable", $entity);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function getSingleEntitySample($tableClient)
{
    try {
        $result = $tableClient->getEntity("mytable", "pk", 1);
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179438.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    
    $entity = $result->getEntity();
    
    echo $entity->getPartitionKey().":".$entity->getRowKey().":".$entity->getPropertyValue("PropertyName")."\n";
}

function batchInsertEntitiesSample($tableClient)
{
    $batchOp = new BatchOperations();
    for ($i = 2; $i < 10; ++$i) {
        $entity = new Entity();
        $entity->setPartitionKey("pk");
        $entity->setRowKey(''.$i);
        $entity->addProperty("PropertyName", EdmType::STRING, "Sample".$i);
        $entity->addProperty("Description", null, "Sample description.");

        $batchOp->addInsertEntity("mytable", $entity);
    }
    
    try {
        $tableClient->batch($batchOp);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function queryAllEntitiesInPartition($tableClient)
{
    $filter = "PartitionKey eq 'pk'";
    
    try {
        $result = $tableClient->queryEntities("mytable", $filter);
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179438.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    
    $entities = $result->getEntities();
    
    foreach ($entities as $entity) {
        echo $entity->getPartitionKey().":".$entity->getRowKey()."<br />"."\n";
    }
}

function querySubsetEntitiesSample($tableClient)
{
    $filter = Filter::applyQueryString("RowKey ne '1'");
    $options = new QueryEntitiesOptions();
    $options->addSelectField("Description");
    $options->addSelectField("PartitionKey");
    $options->addSelectField("RowKey");
    $options->setFilter($filter);
    try {
        $result = $tableClient->queryEntities("mytable", $options);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
    
    $entities = $result->getEntities();
    
    foreach ($entities as $entity) {
        echo $entity->getPartitionKey().":".$entity->getRowKey().PHP_EOL;
        $description = $entity->getProperty("Description")->getValue();
        echo $description."<br />"."\n";
    }
}

function updateEntitySample($tableClient)
{
    $result = $tableClient->getEntity("mytable", "pk", 1);

    $entity = $result->getEntity();
    
    $entity->setPropertyValue("DueDate", new \DateTime()); //Modified DueDate.
    
    $entity->setPropertyValue("Location", null); //Removed Location.
    
    $entity->addProperty("Status", EdmType::STRING, "In progress"); //Added Status.
    
    try {
        $tableClient->updateEntity("mytable", $entity);
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179438.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

function deleteEntitySample($tableClient)
{
    try {
    // Delete entity.
        $tableClient->deleteEntity("mytable", "pk", "2");
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179438.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

function deleteTableSample($tableClient)
{
    try {
        // Delete table.
        $tableClient->deleteTable("mytable");
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179438.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

function generateRandomString($length = 6)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
