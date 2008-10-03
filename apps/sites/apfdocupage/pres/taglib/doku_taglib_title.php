<?php
   import('sites::apfdocupage::biz','APFModel');


   /**
   *  @package sites::apfdocupage::pres::taglib
   *  @class doku_taglib_title
   *
   *  Implements the title taglib. The tag informs the model about the page's title and
   *  displays a <h2> heading.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 18.09.2008<br />
   */
   class doku_taglib_title extends Document
   {


      /**
      *  @private
      *  Indicates the page's title.
      */
      var $__Title = null;


      function doku_taglib_title(){
      }


      /**
      *  @public
      *
      *  Analyzes the attributes and content and informs the model.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 18.09.2008<br />
      *  Version 0.2, 19.09.2008 (Added meta tag and urlname handling; changed description output format)<br />
      *  Version 0.3, 30.09.2008 (Removed double blanks in meta description)<br />
      */
      function onParseTime(){

         // get page title
         if(!isset($this->__Attributes['title']) || empty($this->__Attributes['title'])){
            trigger_error('[doku_taglib_title::onParseTime()] The attribute "title" is missing. Please provide the page title!',E_USER_ERROR);
            exit(1);
          // end if
         }
         $this->__Title = $this->__Attributes['title'];

         // get page tags
         if(!isset($this->__Attributes['tags']) || empty($this->__Attributes['tags'])){
            trigger_error('[doku_taglib_title::onParseTime()] The attribute "tags" is missing. Please provide the page meta tags!',E_USER_ERROR);
            exit(1);
          // end if
         }
         $Tags = $this->__Attributes['tags'];

         // get urlname
         if(!isset($this->__Attributes['urlname']) || empty($this->__Attributes['urlname'])){
            trigger_error('[doku_taglib_title::onParseTime()] The attribute "urlname" is missing. Please provide url name of the page!',E_USER_ERROR);
            exit(1);
          // end if
         }
         $URLName = $this->__Attributes['urlname'];

         // get page description
         if(empty($this->__Content)){
            trigger_error('[doku_taglib_title::onParseTime()] No page description given in the tag\'s content area. Please provide the page description!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // inform model
         $Model = &Singleton::getInstance('APFModel');
         $Model->setAttribute('page.title',$this->__Title);
         $Model->setAttribute('page.description',str_replace('  ',' ',str_replace("\r",'',str_replace("\n",'',trim($this->__Content)))));
         $Model->setAttribute('page.tags',$Tags);
         $Model->setAttribute('page.urlname',$URLName);

       // end function
      }


      /**
      *  @public
      *
      *  Displays the <h2> heading.
      *
      *  @return string $Heading the <h2> heading
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 18.09.2008<br />
      *  Version 0.2, 03.10.2008 (Introduced the "display" attribute. If present and set to false, the title will not be displayed)<br />
      */
      function transform(){
         if(isset($this->__Attributes['display']) && $this->__Attributes['display'] == 'false'){
            return (string)'';
          // end if
         }
         else{
           return '<h2>'.$this->__Title.'</h2>';
          // end else
         }

       // end function
      }

    // end class
   }
?>