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


  namespace ClicShopping\Apps\Marketing\RecoverCart\Sites\ClicShoppingAdmin\Pages\Home\Actions\RecoverCart;

  use ClicShopping\OM\Registry;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_RecoverCart = Registry::get('RecoverCart');

      if (isset($_GET['customer_id'])) {

        $Qdelete = $CLICSHOPPING_RecoverCart->db->prepare('delete
                                                            from :table_customers_basket
                                                            where customers_id = :customers_id
                                                          ');
        $Qdelete->bindInt(':customers_id', (int)$_GET['customer_id']);
        $Qdelete->execute();

        $Qdelete = $CLICSHOPPING_RecoverCart->db->prepare('delete
                                                            from :table_customers_basket_attributes
                                                            where customers_id = :customers_id
                                                          ');
        $Qdelete->bindInt(':customers_id', (int)$_GET['customer_id']);
        $Qdelete->execute();

      } else {
        $Qdelete = $CLICSHOPPING_RecoverCart->db->prepare('delete from :table_customers_basket');
        $Qdelete->execute();

        $Qdelete = $CLICSHOPPING_RecoverCart->db->prepare('delete from :table_customers_basket_attributes');
        $Qdelete->execute();
      }

      $CLICSHOPPING_RecoverCart->redirect('RecoverCart');
    }
  }