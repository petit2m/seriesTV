<?php
namespace AppBundle\Parser; 
    
use DOMElement;
use DateTime;
use FastFeed\Item;
use FastFeed\Exception\RuntimeException;
use FastFeed\Parser\AbstractParser;
use FastFeed\Parser\ParserInterface;
 
 /**
  * RSSParser
  */
 class TorrentRssParser extends AbstractParser implements ParserInterface
 {
 
     /**
      * Retrieve a Items's array
      *
      * @param $content
      *
      * @return array
      * @throws \FastFeed\Exception\RuntimeException
      */
     public function getNodes($content)
     {
         $items = array();
         $document = new \SimpleXmlElement($content);
        
         $nodes = $document->getElementsByTagName('item');
        
         if ($nodes->length) {
             foreach ($nodes as $node) {
                 try {
                     $item = $this->create($node);
                     $items[] = $item;
                 } catch (\Exception $e) {
                     throw new RuntimeException($e->getMessage());
                 }
             }
         }
 
         return $items;
     }
 
     /**
      * @param DOMElement $node
      *
      * @return Item
      */
     public function create(DOMElement $node)
     {
         $item = new Item();
         $this->setProperties($node, $item);
         $this->setDate($node, $item);
         $this->setTags($node, $item);
         $this->executeAggregators($node, $item);
 
         return $item;
     }
 
     /**
      * @return array
      */
     protected function getPropertiesMapping()
    {
        return array(
            'setId' => 'link',
            'setName' => 'title',
            'setIntro' => 'description',
            'setContent' => 'description',
            'setSource' => 'link',
            'setAuthor' => 'author'
        );
    }

    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function setDate(DOMElement $node, Item $item)
    {
        $value = $this->getNodeValueByTagName($node, 'pubDate');
        if ($value) {
            if (strtotime($value)) {
              $item->setDate(new DateTime($value));
             }
         }
    }
    
    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function setTags(DOMElement $node, Item $item)
    {
        $tags = $this->getNodeValuesByTagName($node, 'category');
        foreach ($tags as $tag) {
            $item->addTag($tag);
        }
    }
}