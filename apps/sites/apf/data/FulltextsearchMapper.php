<?php
   /**
    * @package sites::apf::data
    * @class FulltextsearchMapper
    *
    * Implements the data layer of the APF docu page's fulltext search.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.03.2008<br />
    */
   class FulltextsearchMapper extends APFObject {

      public function fulltextsearchMapper() {
      }

      /**
       * @public
       *
       * Loads a list of search result objects according to the given search string.
       *
       * @param string $searchString one or more search strings.
       * @return searchResult[] List of search result objects.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.03.2008<br />
       * Version 0.2, 19.10.2008 (Introduced synonym mapping)<br />
       * Version 0.3, 05.11.2008 (Added value escaping for the search string to avoid sql injections)<br />
       */
      public function loadSearchResult($searchString) {

         // start timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('fulltextsearchMapper::loadSearchResult()');

         // get configuration
         $Config = &$this->__getConfiguration('sites::apf::biz','fulltextsearch');

         // get database connection
         $cM = &$this->__getServiceObject('core::database','ConnectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // make search string save (sql injection)
         $searchString = $SQL->escapeValue($searchString);

         // do synonym mapping
         $synonyms = &$this->__getConfiguration('sites::apf::biz','fulltextsearch_synonyms');
         $section = $synonyms->getSection($this->__Language);
         
         foreach($section as $key => $value) {
            $searchString = str_replace($key,$value,$searchString);
          // end foreach
         }

         // split search strings
         $SearchStringArray = explode(' ',$searchString);

         // create where statement
         $count = count($SearchStringArray);
         $WHERE = array();
         if($count > 1) {

            for($i = 0; $i < $count; $i++) {
               $WHERE[] = 'search_word.Word LIKE \'%'.strtolower($SearchStringArray[$i]).'%\'';
               // end for
            }

            // end if
         }
         else {
            $WHERE[] = 'search_word.Word LIKE \'%'.strtolower($searchString).'%\'';
            // end else
         }

         // create search statement
         $select = 'SELECT search_articles.*, search_index.*, search_word.* FROM search_articles
                       INNER JOIN search_index ON search_articles.ArticleID = search_index.ArticleID
                       INNER JOIN search_word ON search_index.WordID = search_word.WordID
                       WHERE '.implode('OR ',$WHERE).'
                       GROUP BY search_articles.ArticleID
                       ORDER BY search_index.WordCount DESC, search_articles.ModificationTimestamp DESC
                       LIMIT 20';

         // execute search statement
         $result = $SQL->executeTextStatement($select);

         // map results to domain objects
         $Results = array();

         while($data = $SQL->fetchData($result)) {
            $Results[] = $this->__mapSearchResult2DomainObject($data);
            // end while
         }

         // stop timer
         $T->stop('fulltextsearchMapper::loadSearchResult()');

         // return results
         return $Results;

         // end function
      }

      /**
       * @private
       *
       * Mapps a database result set to a search result object.
       *
       * @param string[] $resultSet The database result set.
       * @return searchResult The search result object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.03.2008<br />
       * Version 0.2, 02.10.2008 (Added some new domain object attributes)<br />
       */
      private function __mapSearchResult2DomainObject($resultSet) {

         $searchResult = new searchResult();

         if(isset($resultSet['PageID'])) {
            $searchResult->setPageId($resultSet['PageID']);
         }
         if(isset($resultSet['Title'])) {
            $searchResult->setTitle($resultSet['Title']);
         }
         if(isset($resultSet['Language'])) {
            $searchResult->setLanguage($resultSet['Language']);
         }
         if(isset($resultSet['ModificationTimestamp'])) {
            $searchResult->setLastModified($resultSet['ModificationTimestamp']);
         }
         if(isset($resultSet['WordCount'])) {
            $searchResult->setWordCount($resultSet['WordCount']);
         }
         if(isset($resultSet['Word'])) {
            $searchResult->setIndexedWord($resultSet['Word']);
         }

         return $searchResult;
      }

    // end class
   }
?>