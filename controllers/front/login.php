<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author     Wherd <ola@wherd.dev>
 * @copyright  2019-2020 Wherd
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class WhyloginascustomerLoginModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    public $display_column_left = false;

    public function initContent()
    {
        parent::initContent();

        $id_customer = (int) Tools::getValue('id_customer');
        $token = $this->module->makeToken($id_customer);

        if ($id_customer && (Tools::getValue('xtoken') == $token)) {
            $customer = new Customer((int) $id_customer);

            if (Validate::isLoadedObject($customer)) {
                $customer->logged = 1;
                $this->context->updateCustomer($customer);

                $cookie = new Cookie('psAdmin', '', (int) Configuration::get('PS_COOKIE_LIFETIME_BO'));
                $employee = new Employee((int) $cookie->id_employee);

                PrestaShopLogger::addLog(
                    'Logged in as customer ' . $customer->firstname . ' ' . $customer->lastname,
                    1,
                    null,
                    'Customer',
                    $customer->id,
                    true,
                    $employee->id
                );

                Tools::redirect('/');
            }
        }

        $this->setTemplate('failed.tpl');
    }
}
