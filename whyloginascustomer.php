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

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Whyloginascustomer extends Module
{
    public function __construct()
    {
        $this->name = 'whyloginascustomer';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
        $this->author = 'Wherd';
        $this->controllers = ['login'];
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Login as customer');
        $this->description = $this->l('Allows you to login as a customer from the back-office.');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayAdminCustomers');
    }

    public function uninstall()
    {
        return $this->unregisterHook('displayAdminCustomers') && parent::uninstall();
    }

    public function hookDisplayAdminCustomers($request)
    {
        $customer = new Customer($request['request']->get('customerId'));

        if (!Validate::isLoadedObject($customer)) {
            return;
        }

        $link = $this->context->link->getModuleLink(
            $this->name,
            'login',
            ['id_customer' => $customer->id, 'xtoken' => $this->makeToken($customer->id)]
        );

        ob_start(); ?>
        <div class="col-md-3">
            <div class="card">
                <h3 class="card-header">
                    <i class="material-icons">lock_outline</i>
                    <?php echo $this->l('Login As Customer'); ?>
                </h3>
                <div class="card-body">
                    <p class="text-muted text-center">
                        <a href="<?php echo $link; ?>" target="_blank" style="text-decoration:none;">
                            <i class="material-icons d-block">lock_outline</i>
                            <?php echo $this->l('Login As Customer'); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    public function makeToken($id_customer)
    {
        return md5(_COOKIE_KEY_ . $id_customer . date('Ymd'));
    }
}
