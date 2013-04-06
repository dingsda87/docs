<?php
namespace APF\sites\apf\biz;

use APF\core\pagecontroller\APFObject;

/**
 * @package APF\sites\apf\biz
 * @class SearchResult
 *
 * This class represents the domain object of the fulltextsearch functionality.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.03.2008<br />
 * Version 0.2, 02.10.2008<br />
 */
class SearchResult extends APFObject {

   private $title;
   private $pageID;
   private $lastMod;
   private $wordCount;
   private $indexedWord;

   public function SearchResult() {
   }

   public function setPageId($pageId) {
      $this->pageID = $pageId;
   }

   public function getPageId() {
      return $this->pageID;
   }

   public function setTitle($title) {
      $this->title = $title;
   }

   public function getTitle() {
      return $this->title;
   }

   public function setLastModified($lastMod) {
      $this->lastMod = $lastMod;
   }

   public function getLastModified() {
      return $this->lastMod;
   }

   public function setWordCount($count) {
      $this->wordCount = $count;
   }

   public function getWordCount() {
      return $this->wordCount;
   }

   public function setIndexedWord($word) {
      $this->indexedWord = $word;
   }

   public function getIndexedWord() {
      return $this->indexedWord;
   }

}
