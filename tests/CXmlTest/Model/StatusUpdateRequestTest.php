<?php

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Credential;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\StatusUpdateRequest;
use CXml\Model\Status;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StatusUpdateRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
	public function testMinimumExample(): void
	{
		$from = new Credential(
			'NetworkId',
			'AN00000123'
		);
		$to = new Credential(
			'NetworkId',
			'AN00000456'
		);
		$sender = new Credential(
			'NetworkId',
			'AN00000123',
			'abracadabra'
		);

		$statusUpdateRequest = new StatusUpdateRequest(
			new Status(200, 'OK', 'Forwarded to supplier', 'en-US'),
			'0c300508b7863dcclb_14999'
		);

		$cxml = Builder::create('en-US', 'Supplier’s Super Order Processor', $this)
			->from($from)
			->to($to)
			->sender($sender)
			->payload($statusUpdateRequest)
			->build()
		;

		$this->assertEquals('StatusUpdateRequest_0c30050@supplierorg.com', (string) $cxml);

		$xml = Serializer::create()->serialize($cxml);
		$this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/StatusUpdateRequest.xml', $xml);
	}

	public function newPayloadIdentity(): PayloadIdentity
	{
		return new PayloadIdentity(
			'0c30050@supplierorg.com',
			new \DateTime('2021-01-08T23:00:06-08:00')
		);
	}
}
