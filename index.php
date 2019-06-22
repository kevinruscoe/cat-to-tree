<?php

include "vendor/autoload.php";

use drupol\phptree\Node\ValueNode;
use drupol\phptree\Importer\ImporterInterface;
use drupol\phptree\Node\NodeInterface;

class CategoryImporter implements ImporterInterface
{
    /**
     * The index of the root node.
     * 
     * @var int
     */
    private $rootNodeIndex;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->setRootNodeIndex(1);
    }

    /**
     * Set the root node index.
     * 
     * @param int $rootNodeIndex The root node index.
     * 
     * @return CategoryImporter
     */
    public function setRootNodeIndex(int $rootNodeIndex = 1): CategoryImporter
    {
        $this->rootNodeIndex = $rootNodeIndex;

        return $this;
    }

    /**
     * Import the data.
     * 
     * @param array $data The data.
     * 
     * @return NodeInterface
     */
    public function import($data): NodeInterface
    {
        $nodes = [];

        foreach ($data as $item) {
            if (! isset($item['id'])) {
                continue;
            }

            $nodes[$item['id']] = new ValueNode($item['id']);
        }

        foreach ($data as $item) {
            if (! isset($item['parent'])) {
                continue;
            }
            
            $nodes[$item['parent']]->add($nodes[$item['id']]);
        }

        return $nodes[$this->rootNodeIndex];
    }
}

$flat = [
    ['id' => 1, 'parent' => null],
    ['id' => 2, 'parent' => 1],
    ['id' => 8, 'parent' => 5],
    ['id' => 3, 'parent' => null],
    ['id' => 4],
    ['id' => 5, 'parent' => 2],
    ['id' => 6, 'parent' => 5],
    ['id' => 7],
    [],
];

$importer = new CategoryImporter;
$tree = $importer->setRootNodeIndex(1)->import($flat);

$exporter = new \drupol\phptree\Exporter\Ascii;
var_dump($exporter->export($tree));