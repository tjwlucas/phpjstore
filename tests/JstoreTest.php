<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers jstore
 */
final class jstoreMainTest extends TestCase
{
	public function testInitialiseDataStore() {
		require('vendor/autoload.php');
		$store = new jstore\jstore('data');
		$this->assertFileExists('data');
		return $store;
	}
	/**
     * @depends testInitialiseDataStore
     */
	public function testCanInstantiateEmptyStructure($store)
    {
        $this->assertInstanceOf(
			jstore\jstoreObject::class,
            $store->get('test')
        );
    }
	/**
     * @depends testInitialiseDataStore
     */
	public function testStoringGlobalInStore($store) {
			$store->setGlobal(['test' =>'value']);
			$this->assertEquals('value', $store->getGlobal('test'));
	}
}
