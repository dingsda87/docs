<?php
   import('core::database','connectionManager');


   /**
   *  @package sites::apfdocupage::pres::documentcontroller::perspectives::start
   *  @class i_box_latest_controller
   *
   *  Implements the documentcontroller for the "i_box_latest.html" template.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 05.10.2008<br />
   */
   class i_box_latest_controller extends baseController
   {

      function i_box_latest_controller(){
      }


      /**
      *  @public
      *
      *  Reads the latest posts from the forum and displays them. Ths implementation is quick&dirty,
      *  because there is not business component for the web page.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.10.2008<br />
      */
      function transformContent(){

         // get forum database connection
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQLForum = &$cM->getConnection('Forum');

         // read last three forum topics
         $select_posts = 'SELECT forum_id,topic_id,topic_title
                          FROM '.$this->__Language.'_phpbb3_topics
                          WHERE topic_type = 0 AND topic_moved_id = 0
                          ORDER BY topic_last_post_time DESC
                          LIMIT 2;';
         $result_posts = $SQLForum->executeTextStatement($select_posts);

         // get template
         $Template__PostsForum = &$this->__getTemplate('PostsForum');

         // get configuration from the registry
         $Reg = &Singleton::getInstance('Registry');
         $ForumBaseURL = $Reg->retrieve('sites::apfdocupage','ForumBaseURL');

         // create post list
         $Buffer = (string)'';
         $isFirstPost = true;

         while($data = $SQLForum->fetchData($result_posts)){

            // fill template
            $Template__PostsForum->setPlaceHolder('Link',$ForumBaseURL.'/'.$this->__Language.'/viewtopic.php?f='.$data['forum_id'].'&t='.$data['topic_id']);
            $Template__PostsForum->setPlaceHolder('LinkText',utf8_encode(substr($data['topic_title'],0,35).'...'));
            $Template__PostsForum->setPlaceHolder('Title',utf8_encode($data['topic_title']));

            // add line break if not first post
            if($isFirstPost === true){
               $isFirstPost = false;
             // end if
            }
            else{
               $Buffer .= '<br />';
             // end else
            }

            // add current post to list
            $Buffer .= $Template__PostsForum->transformTemplate();

          // end while
         }

         // add post list to content
         $this->setPlaceHolder('Posts',$Buffer);


         // get comment database connection
         $SQLComments = &$cM->getConnection('Comments'); // <-- hier evtl. noch ein Bug, da der
                                                         // MySQLxHandler die Comments-Datenbank
                                                         // nicht sieht!

         // get comment posts template
         $Template__PostsComments = &$this->__getTemplate('PostsComments');

         // select the last two comments
         $select_comments = 'SELECT ArticleCommentID,Comment,Date,Time,CategoryKey
                             FROM article_comments
                             ORDER BY Date DESC, Time DESC
                             LIMIT 2;';
         $result_comments = $SQLComments->executeTextStatement($select_comments);

         // create comment list
         $Buffer = (string)'';
         $isFirstPost = true;

         while($data = $SQLComments->fetchData($result_comments)){

            // fill template
            //$Template__PostsComments->setPlaceHolder('Link',$ForumBaseURL.'/'.$this->__Language.'/viewtopic.php?f='.$data['forum_id'].'&t='.$data['topic_id']);
            $Template__PostsComments->setPlaceHolder('LinkText',utf8_encode(substr($data['Comment'],0,35).'...'));
            //$Template__PostsComments->setPlaceHolder('Title',utf8_encode($data['topic_title']));

            // add line break if not first post
            if($isFirstPost === true){
               $isFirstPost = false;
             // end if
            }
            else{
               $Buffer .= '<br />';
             // end else
            }

            // add current post to list
            $Buffer .= $Template__PostsComments->transformTemplate();

          // end while
         }

         // add post list to content
         $this->setPlaceHolder('Comments',$Buffer);

       // end function
      }

    // end class
   }
?>