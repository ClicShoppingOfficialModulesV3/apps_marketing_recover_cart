<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\RecoverCart\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class RecoverCart extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_RecoverCart = Registry::get('RecoverCart');

      $this->page->setFile('recover_cart.php');
      $this->page->data['action'] = 'RecoverCart';

      $CLICSHOPPING_RecoverCart->loadDefinitions('Sites/ClicShoppingAdmin/RecoverCart');
    }
  }