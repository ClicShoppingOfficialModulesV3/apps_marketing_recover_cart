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

  namespace ClicShopping\Apps\Marketing\RecoverCart\Module\Hooks\ClicShoppingAdmin\StatsDashboard;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\RecoverCart\RecoverCart as RecoverCartApp;

  class PageTabContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('RecoverCart')) {
        Registry::set('RecoverCart', new RecoverCartApp());
      }

      $this->app = Registry::get('RecoverCart');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/StatsDashboard/page_tab_content');
    }

    /**
     * @return int
     */
    private function statsCountRecoverShoppingCart(): int
    {
      $QcustomersRecoverShoppingCart = $this->app->db->prepare('select count(customers_basket_id) as count
                                                                from :table_customers_basket
                                                                limit 1
                                                                ');
      $QcustomersRecoverShoppingCart->execute();

      $customers_recover_shopping_cart = $QcustomersRecoverShoppingCart->valueint('count');

      return $customers_recover_shopping_cart;
    }


    public function display()
    {

      if (!defined('CLICSHOPPING_APP_RECOVER_CART_RC_STATUS') || CLICSHOPPING_APP_RECOVER_CART_RC_STATUS == 'False') {
        return false;
      }

      if ($this->statsCountRecoverShoppingCart() != 0) {
        $content = '
        <div class="row">
          <div class="col-md-11 mainTable">
            <div class="form-group row">
              <label for="' . $this->app->getDef('box_entry_recover_shopping_cart') . '" class="col-9 col-form-label"><a href="' . $this->app->link('RecoverCart') . '">' . $this->app->getDef('box_entry_recover_shopping_cart') . '</a></label>
              <div class="col-md-3">
                ' . $this->statsCountRecoverShoppingCart() . '
              </div>
            </div>
            
      
            
          </div>
        </div>
       ';

        $output = <<<EOD
  <!-- ######################## -->
  <!--  Start Recover      -->
  <!-- ######################## -->
             {$content}
  <!-- ######################## -->
  <!--  Start Recover      -->
  <!-- ######################## -->
EOD;
        return $output;
      }
    }
  }