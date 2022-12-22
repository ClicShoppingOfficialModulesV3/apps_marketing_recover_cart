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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;

  $CLICSHOPPING_RecoverCart = Registry::get('RecoverCart');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

  $unsold_day = 30;

  if (isset($_POST['tdate'])) {
    $tdate = HTML::sanitize($_POST['tdate']);
  } else {
    $tdate = $unsold_day;
  }
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/recover_cart.png', $CLICSHOPPING_RecoverCart->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-4 pageHeading"><?php echo $CLICSHOPPING_RecoverCart->getDef('heading_title'); ?></span>
          <span class="col-md-2 text-end">
            <div class="row">
<?php
  echo HTML::form('recover', $CLICSHOPPING_RecoverCart->link('RecoverCart'));
  echo $CLICSHOPPING_RecoverCart->getDef('text_last_days');
?>
            </div>
          </span>
          <span class="col-md-1"><?php echo HTML::inputField('tdate', $tdate); ?></span>
          <span
            class="col-md-2"><?php echo HTML::button($CLICSHOPPING_RecoverCart->getDef('button_update'), null, null, 'success'); ?></span>
          <span
            class="col-md-2 text-end"><?php echo HTML::button($CLICSHOPPING_RecoverCart->getDef('button_reset'), null, $CLICSHOPPING_RecoverCart->link('RecoverCart'), 'warning'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <table class="table table-sm table-hover table-striped">
    <thead>
    <tr class="dataTableHeadingRow">
      <td><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_date'); ?></td>
      <td><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_customer'); ?></td>
      <td><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_email'); ?></td>
      <td><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_phone'); ?></td>
      <td></td>
      <td></td>
    </tr>
    <tr class="dataTableHeadingRow">
      <td><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_model') ?></td>
      <td><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_description'); ?></td>
      <td class="text-end"><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_quantity'); ?></td>
      <td class="text-end"><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_price'); ?></td>
      <td class="text-end"><?php echo $CLICSHOPPING_RecoverCart->getDef('table_heading_total'); ?></td>
      <td class="text-end"><?php echo $CLICSHOPPING_RecoverCart->getDef('text_action'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php
      $rawtime = strtotime('-' . $tdate . ' days');
      $ndate = date('Ymd', $rawtime);

      $Qbasket = $CLICSHOPPING_RecoverCart->db->prepare('select cb.customers_id as cid,
                                                                 cb.products_id as pid,
                                                                 cb.customers_basket_quantity qty,
                                                                 cb.customers_basket_date_added bdate,
                                                                 cus.customers_firstname asfname,
                                                                 cus.customers_lastname aslname,
                                                                 cus.customers_telephone as phone,
                                                                 cus.customers_email_address as email
                                                        from :table_customers_basket cb,
                                                             :table_customers cus
                                                        where cb.customers_id in (
                                                                                 select distinct cb.customers_id ascid
                                                                                 from :table_customers_basket cb
                                                                                 where cb.customers_basket_date_added >= :customers_basket_date_added
                                                                                 )
                                                         and cb.customers_id = cus.customers_id
                                                         order by cb.customers_id,
                                                         cb.customers_basket_date_added desc
                                                       ');
      $Qbasket->bindValue(':customers_basket_date_added', $ndate);
      $Qbasket->execute();

      $results = 0;
      $curcus = '';
      $tprice = 0;
      $totalAll = 0;

      $first_line = true;

      while ($Qbasket->fetch()) {
        if ($curcus != $Qbasket->valueInt('cid')) {
// output line
          $totalAll += $tprice;
          $tcart_formated = $CLICSHOPPING_Currencies->format($tprice);
          ?>

          <tr>
            <td colspan="5">
              <strong><?php echo $CLICSHOPPING_RecoverCart->getDef('text_total_basket_cart'); ?></strong><?php echo $tcart_formated; ?>
            </td>
            <td class="text-end">
              <?php
                echo HTML::form('DeleteAll', $CLICSHOPPING_RecoverCart->link('RecoverCart&RecoverCart&Delete'));
                echo HTML::button($CLICSHOPPING_RecoverCart->getDef('button_delete_all'), null, null, 'danger', null, 'sm');
                echo '</form>';
              ?>
          </tr>
          <?php
          // set new cline and curcus
          $curcus = $Qbasket->valueInt('cid');
          $tprice = 0;

          if ($first_line === true) {
            $first_line = false;
          }


          $message_array = [
            'name' => $Qbasket->value('asfname') . ' ' . $Qbasket->value('aslname'),
            'store_name' => STORE_NAME,
            'url' => HTTP::getShopUrlDomain()
          ];
          ?>
          <tr>
            <td><?php echo DateTime::toShort($Qbasket->value('bdate')); ?></td>
            <td><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Customers&search=' . $Qbasket->value('asfname')), $Qbasket->value('aslname') . ' ' . $Qbasket->value('asfname')); ?></td>
            <td><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Communication\EMail&EMail&customer=' . $Qbasket->value('email') .'&message=' . $CLICSHOPPING_RecoverCart->getDef('text_message', $message_array)), $Qbasket->value('email')); ?></td>
            <td><?php echo $Qbasket->value('phone'); ?></td>
          </tr>
          <?php
        }

        $QProducts = $CLICSHOPPING_RecoverCart->db->prepare('select p.products_price price,
                                                                         p.products_model model,
                                                                         p.products_image image,
                                                                         pd.products_name
                                                                from :table_products p,
                                                                     :table_products_description pd,
                                                                     :table_languages l
                                                                where p.products_id = :products_id
                                                                and pd.products_id = p.products_id
                                                                and l.languages_id = pd.language_id
                                                               ');
        $QProducts->bindValue(':products_id', $Qbasket->valueInt('pid'));
        $QProducts->execute();

        $tprice = $tprice + ($Qbasket->valueInt('qty') * $QProducts->valueDecimal('price'));

        if ($Qbasket->valueInt('qty') != 0) {
          $pprice_formated = $CLICSHOPPING_Currencies->format($QProducts->valueDecimal('price'));
          $tpprice_formated = $CLICSHOPPING_Currencies->format(($Qbasket->valueInt('qty') * $QProducts->valueDecimal('price')));
          ?>
          <tr class="dataTableRow">
            <td><?php echo $QProducts->value('model'); ?></td>
            <td><?php echo $QProducts->value('products_name'); ?></a>
            </td>
            <td class="text-end"><?php echo $Qbasket->valueInt('qty'); ?></td>
            <td class="text-end"><?php echo $pprice_formated; ?></td>
            <td class="text-end"><?php echo $tpprice_formated; ?></td>
            <td></td>
          </tr>
          <?php
        }
      }
      // if we have had some customers then we need a final customer total line
      if (!empty($curcus)) {
        $totalAll += $tprice;
        $tcart_formated = $CLICSHOPPING_Currencies->format($tprice);
        ?>
        <tr>
          <td class="text-end" colspan="5">
            <strong><?php echo $CLICSHOPPING_RecoverCart->getDef('text_total_basket_cart') . '</strong>' . $tcart_formated; ?>
          </td>
          <td class="text-end">
            <?php
              echo HTML::form('Delete', $CLICSHOPPING_RecoverCart->link('RecoverCart&RecoverCart&Delete&customer_id=' . $curcus . '&tdate=' . $tdate));
              echo HTML::button($CLICSHOPPING_RecoverCart->getDef('button_delete'), null, null, 'danger', null, 'sm');
              echo '</form>';
            ?>
          </td>
        </tr>
        <?php
      }

      $totalAll_formated = $CLICSHOPPING_Currencies->format($totalAll);
    ?>
    <tr>
      <td class="text-end" colspan="5">
        <strong><?php echo $CLICSHOPPING_RecoverCart->getDef('total_grand_total'); ?></strong><?php echo $totalAll_formated; ?>
      </td>
    </tr>
    </tbody>
  </table>
</div>