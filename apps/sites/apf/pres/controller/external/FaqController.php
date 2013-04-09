<?php
namespace APF\sites\apf\pres\controller\external;

use APF\core\database\ConnectionManager;
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\registry\Registry;

/**
 * @package APF\sites\apf\pres\controller\external
 * @class FaqController
 *
 *  Implements the document controller for the FAQ list.
 *
 * @author Christian Achatz
 * @version
 *  Version 0.1, 10.01.2009<br />
 */
class FaqController extends BaseDocumentController {

   /**
    * @public
    *
    *  Displays the FAQ list on the FAQ content page.
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 10.01.2009<br />
    */
   public function transformContent() {

      // get forum database connection
      /* @var $cM ConnectionManager */
      $cM = &$this->getServiceObject('APF\core\database\ConnectionManager');
      $SQLForum = &$cM->getConnection('Forum');

      // get configuration from the registry
      $forumBaseURL = Registry::retrieve('APF\sites\apf', 'ForumBaseURL');

      $select = 'SELECT
                          `topic_id`,
                          `topic_title`,
                          `topic_time`,
                          `topic_first_poster_name`,
                          `topic_last_post_time`
                       FROM `de_phpbb3_topics`
                       WHERE `forum_id` = \'6\'
                       ORDER BY topic_last_post_time DESC;';
      $result = $SQLForum->executeTextStatement($select);

      // get template and prefill it
      $templatePostsForum = &$this->getTemplate('PostsForum');
      $templateAuthorLabel = &$this->getTemplate('Author_' . $this->language);
      $templateCreationDateLabel = &$this->getTemplate('CreationDate_' . $this->language);
      $templatePostsForum->setPlaceHolder('AuthorLabel', $templateAuthorLabel->transformTemplate());
      $templatePostsForum->setPlaceHolder('CreationDateLabel', $templateCreationDateLabel->transformTemplate());

      // create post list
      $buffer = (string)'';

      while ($data = $SQLForum->fetchData($result)) {

         // fill template
         $templatePostsForum->setPlaceHolder('Link', $forumBaseURL . '/' . $this->language . '/viewtopic.php?f=6&t=' . $data['topic_id']);
         $templatePostsForum->setPlaceHolder('LinkText', utf8_encode($data['topic_title']));
         $templatePostsForum->setPlaceHolder('Title', utf8_encode($data['topic_title']));

         $templatePostsForum->setPlaceHolder('CreationDate', utf8_encode(date('Y-m-d, H:i:s', $data['topic_time'])));
         $templatePostsForum->setPlaceHolder('Author', utf8_encode($data['topic_first_poster_name']));

         // add current post to list
         $buffer .= $templatePostsForum->transformTemplate();

      }

      // add post list to content
      $this->setPlaceHolder('Entries', $buffer);

   }

}