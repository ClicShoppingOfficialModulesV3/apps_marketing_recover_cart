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

  namespace ClicShopping\Apps\Marketing\RecoverCart\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\RecoverCart\RecoverCart;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_RecoverCart = new RecoverCart();
      Registry::set('RecoverCart', $CLICSHOPPING_RecoverCart);

      $this->app = $CLICSHOPPING_RecoverCart;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
