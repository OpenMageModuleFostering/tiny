<?php

/*
 * Tiny Magento: Integration Module
 * Copyright (C) 2013  Tiny Software
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define("URL_TINY", "https://api.tiny.com.br/api2/recepcao.magento.api.php");

class Tiny_Pedidos_Model_Observer {

    /**
     * O Magento irá passar a Varien_Event_Observer object como
     * o primeiro parâmetro de eventos despachados.
     */

	public function inserePedido(Varien_Event_Observer $observer) {
		
		$exportarQuando = Mage::getStoreConfig('tiny/general/exportar_Tiny');
		/*
		 array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Não exportar')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Quando o pedido for incluído')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Quando o pedido for faturado'))
		 */		
		
		if ($exportarQuando == 1) {
			try {		
				$orderId = $observer->getEvent()->getOrder()->getIncrementId();
				$this->exportarPedido($orderId);
			} catch(Exception $e) {
			}
		}
	}

	public function exportarPedido($num) {
		
		$apiKey = Mage::getStoreConfig('tiny/general/apiKey_tiny');
		$exportarQuando = Mage::getStoreConfig('tiny/general/exportar_Tiny');
		$destino = Mage::getStoreConfig('tiny/general/destino_Tiny');
	
		if ($destino == 1) {
			$pedidoNota = "N";	
		} else {
			$pedidoNota = "P";
		}
		
		if ($exportarQuando != 0) {
		
			try {		
	
				$url = URL_TINY;
				$data = 'apiKey=' . $apiKey .'&tipo=' . $pedidoNota . '&numeroPedido=' . $num;
	
				$optional_headers = null;
				$params = array('http' => array('method' => 'POST', 'content' => $data));
				if ($optional_headers !== null) {
					$params['http']['header'] = $optional_headers;
				}
				$ctx = stream_context_create($params);
				$fp = @fopen($url, 'rb', false, $ctx);
				if (!$fp) {
					throw new Exception("Problema com $url, $php_errormsg");
				}
				$response = @stream_get_contents($fp);
				if ($response === false) {
					throw new Exception("Problema obtendo retorno de $url, $php_errormsg");
				}
			} catch(Exception $e) {
			}
		}
	}	

	public function inserePedidoPeloPagamento(Varien_Event_Observer $observer) {
		$exportarQuando = Mage::getStoreConfig('tiny/general/exportar_Tiny');
		if ($exportarQuando == 2) {
			try {		
				$orderId = $observer->getPayment()->getOrder()->getIncrementId();
				$this->exportarPedido($orderId);
			} catch(Exception $e) {
			}
		}
	}

	public function inserePedidoPeloInvoice(Varien_Event_Observer $observer) {
		try {		
			$order = $observer->getEvent()->getOrder();
		        $orderId = $order->getRealOrderId();
			//$orderId = $observer->getPayment()->getOrder()->getIncrementId();
			//$this->exportarPedido($orderId);
		} catch(Exception $e) {
		}
	}
}
?>
