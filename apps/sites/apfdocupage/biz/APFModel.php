<?php
   /**
   *  @package sites::apfdocupage::biz
   *  @class APFModel
   *
   *  Implements the model of the APF docu page.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.08.2008<br />
   */
   class APFModel extends coreObject
   {

      /**
      *  @public
      *
      *  Initializes the model's attributes.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.08.2008<br />
      *  Version 0.2, 17.09.2008 (Added content and quicknavi file name)<br />
      */
      function APFModel(){

         // indicates the path to the content and quicknavi files
         $this->__Attributes['content.filepath'] = './apps/sites/apfdocupage/pres';

         // indicates the current perspective
         $this->__Attributes['perspective.name'] = 'start';

         // indicates the current language
         $this->__Attributes['page.language'] = null;

         // indivates the current page id
         $this->__Attributes['page.id'] = null;

         // indicates the current page title
         $this->__Attributes['page.title'] = null;

         // indicates the current content file name
         $this->__Attributes['page.contentfilename'] = null;

         // indicates the current quicknavi file name
         $this->__Attributes['page.quicknavifilename'] = null;

         // defines the page indicator per language
         $this->__Attributes['page.indicator'] = array(
                                                       'de' => 'Seite',
                                                       'en' => 'Page'
         );

       // end function
      }

    // end class
   }
?>