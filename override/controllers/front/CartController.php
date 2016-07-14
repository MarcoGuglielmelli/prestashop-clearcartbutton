<?php
 
if (!defined('_CAN_LOAD_FILES_'))
    exit;
 
class CartController extends CartControllerCore
{
 
 public function postProcess()
	{
	    // Update the cart ONLY if $this->cookies are available, in order to avoid ghost carts created by bots
	    if ($this->context->cookie->exists() && !$this->errors && !($this->context->customer->isLogged() && !$this->isTokenValid())) {
	        if (Tools::getIsset('add') || Tools::getIsset('update')) {
	            $this->processChangeProductInCart();
	        } elseif (Tools::getIsset('delete')) {
	            $this->processDeleteProductInCart();
	        } elseif (Tools::getIsset('changeAddressDelivery')) {
	            $this->processChangeProductAddressDelivery();
	        } elseif (Tools::getIsset('allowSeperatedPackage')) {
	            $this->processAllowSeperatedPackage();
	        } elseif (Tools::getIsset('duplicate')) {
	            $this->processDuplicateProduct();
	        } elseif (Tools::getIsset('deleteAll')) {
 				$this->context->cart->delete();
			    $this->context->cookie->id_cart = 0;
			    die(1);
			}
	        // Make redirection
	        if (!$this->errors && !$this->ajax) {
	            $queryString = Tools::safeOutput(Tools::getValue('query', null));
	            if ($queryString && !Configuration::get('PS_CART_REDIRECT')) {
	                Tools::redirect('index.php?controller=search&search='.$queryString);
	            }
	 
	            // Redirect to previous page
	            if (isset($_SERVER['HTTP_REFERER'])) {
	                preg_match('!http(s?)://(.*)/(.*)!', $_SERVER['HTTP_REFERER'], $regs);
	                if (isset($regs[3]) && !Configuration::get('PS_CART_REDIRECT')) {
	                    $url = preg_replace('/(\?)+content_only=1/', '', $_SERVER['HTTP_REFERER']);
	                    Tools::redirect($url);
	                }
	            }
	 
	            Tools::redirect('index.php?controller=order&'.(isset($this->id_product) ? 'ipa='.$this->id_product : ''));
	        }
	    } elseif (!$this->isTokenValid()) {
	        Tools::redirect('index.php');
	    }
	}
 
}
