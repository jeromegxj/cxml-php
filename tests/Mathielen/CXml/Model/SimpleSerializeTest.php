<?php

namespace Mathielen\CXml\Model;

use Mathielen\CXml\Endpoint;
use Mathielen\CXml\Model\Message\PunchOutOrderMessage;
use Mathielen\CXml\Model\Request\PunchOutSetupRequest;
use PHPUnit\Framework\TestCase;

class SimpleSerializeTest extends TestCase
{
	public function testSimpleRequest(): void
	{
		$from = new Party(
			new Credential('AribaNetworkUserId', 'admin@acme.com')
		);
		$to = new Party(
			new Credential('DUNS', '012345678')
		);
		$sender = new Party(
			new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
			'Network Hub 1.1'
		);
		$request = new Request(
			new PunchOutSetupRequest(
				'nomnom',
				'https://browserFormPost',
				'https://supplierSetup'
			)
		);

		$header = new Header(
			$from,
			$to,
			$sender
		);

		$msg = CXml::forRequest(
			new PayloadIdentity('payload-id', new \DateTime('2000-01-01')),
			$request,
			$header
		);

		$actualXml = Endpoint::buildSerializer()
				->serialize($msg, 'xml');

		//XML copied from cXML Reference Guide
		$expectedXml = <<<EOT
			<?xml version="1.0" encoding="UTF-8"?>
			<cXML payloadID="payload-id" timestamp="2000-01-01T00:00:00+00:00">
			<Header>
			 <From>
			 <Credential domain="AribaNetworkUserId">
			 <Identity>admin@acme.com</Identity>
			 </Credential>
			 </From>
			 <To>
			 <Credential domain="DUNS">
			 <Identity>012345678</Identity>
			 </Credential>
			 </To>
			 <Sender>
			 <Credential domain="AribaNetworkUserId">
			 <Identity>sysadmin@buyer.com</Identity>
			 <SharedSecret>abracadabra</SharedSecret>
			 </Credential>
			 <UserAgent>Network Hub 1.1</UserAgent>
			 </Sender>
			</Header>
			<Request>
			 <PunchOutSetupRequest operation="create">
			  <BuyerCookie>nomnom</BuyerCookie>
			  <BrowserFormPost>
			   <URL>https://browserFormPost</URL>
			  </BrowserFormPost>
			  <SupplierSetup>
			   <URL>https://supplierSetup</URL>
			  </SupplierSetup>
			 </PunchOutSetupRequest>
			</Request>
			</cXML>
			EOT;

		$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
	}

	public function testSimpleMessage(): void
	{
		$from = new Party(
			new Credential('AribaNetworkUserId', 'admin@acme.com')
		);
		$to = new Party(
			new Credential('DUNS', '012345678')
		);
		$sender = new Party(
			new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
			'Network Hub 1.1'
		);
		$message = new Message(
			new PunchOutOrderMessage()
		);

		$header = new Header(
			$from,
			$to,
			$sender
		);

		$msg = CXml::forMessage(
			new PayloadIdentity('payload-id', new \DateTime('2000-01-01')),
			$message,
			$header
		);

		$actualXml = Endpoint::buildSerializer()
			->serialize($msg, 'xml');

		//XML *NOT* copied from cXML Reference Guide
		$expectedXml = <<<EOT
			<?xml version="1.0" encoding="UTF-8"?>
			<cXML payloadID="payload-id" timestamp="2000-01-01T00:00:00+00:00">
			<Header>
			 <From>
			 <Credential domain="AribaNetworkUserId">
			 <Identity>admin@acme.com</Identity>
			 </Credential>
			 </From>
			 <To>
			 <Credential domain="DUNS">
			 <Identity>012345678</Identity>
			 </Credential>
			 </To>
			 <Sender>
			 <Credential domain="AribaNetworkUserId">
			 <Identity>sysadmin@buyer.com</Identity>
			 <SharedSecret>abracadabra</SharedSecret>
			 </Credential>
			 <UserAgent>Network Hub 1.1</UserAgent>
			 </Sender>
			</Header>
			<Message>
			  <PunchOutOrderMessage>
			    <BuyerCookie>34234234ADFSDF234234</BuyerCookie>
			  </PunchOutOrderMessage>
			</Message>
			</cXML>
			EOT;

		$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
	}

	public function testSimpleResponse(): void
	{
		$msg = CXml::forResponse(
			new PayloadIdentity('978979621537--4882920031100014936@206.251.25.169', new \DateTime('2001-01-08T10:47:01-08:00')),
			new Response(
				null,
				new Status(200, 'OK', 'Ping Response CXml')
			)
		);

		$actualXml = Endpoint::buildSerializer()
			->serialize($msg, 'xml');

		//XML copied from cXML Reference Guide
		$expectedXml = <<<EOT
			<?xml version="1.0" encoding="UTF-8"?>
			<cXML timestamp="2001-01-08T10:47:01-08:00"
			payloadID="978979621537--4882920031100014936@206.251.25.169">
			 <Response>
			  <Status code="200" text="OK">Ping Response CXml</Status>
			 </Response>
			</cXML>
			EOT;

		$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
	}
}
