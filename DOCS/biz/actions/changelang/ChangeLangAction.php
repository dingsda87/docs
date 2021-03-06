<?php
namespace DOCS\biz\actions\changelang;

use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use DOCS\biz\UrlManager;
use APF\tools\request\RequestHandler;
use APF\tools\http\HeaderManager;

/**
 * @package DOCS\biz\actions\changelang
 * @class ChangeLangAction
 *
 * Represents the front controller action to change the site's language.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 15.12.2009<br />
 */
class ChangeLangAction extends AbstractFrontcontrollerAction {

   private static $LANG = 'lang';
   private static $PAGE_ID = 'page-id';
   private static $VERSION_ID = 'version-id';

   /**
    * @public
    *
    * Changes the language by redirecting to the appropriate url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.12.2009<br />
    */
   public function run() {

      // clean input parameters to avoid sql injection!
      $targetLang = preg_replace('/([^en|^de])/i', '', RequestHandler::getValue(self::$LANG));
      $targetPageId = preg_replace('/([^0-9]+)/', '', RequestHandler::getValue(self::$PAGE_ID));
      $targetVersion = RequestHandler::getValue(self::$VERSION_ID);
      if ($targetVersion !== null) {
         $targetVersion = preg_replace('/([^0-9A-Za-z\.]+)/', '', $targetVersion);
      }

      /* @var $urlMan UrlManager */
      $urlMan = & $this->getServiceObject('DOCS\biz\UrlManager');
      $forwardUrl = $urlMan->generateLink($targetPageId, $targetLang, $targetVersion);

      HeaderManager::forward($forwardUrl);
   }

}
