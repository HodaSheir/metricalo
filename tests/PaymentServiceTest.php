<?php

namespace App\Tests;

use App\Service\PaymentService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PaymentServiceTest extends TestCase
{
    private $httpClient;
    private $parameterBag;
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        // Mock HttpClient
        $this->httpClient = $this->createMock(HttpClientInterface::class);

        // Define fake API keys for testing
        $shift4Key = 'test_shift4_key';
        $aciKey = 'test_aci_key';
        $aciEntityId = 'test_aci_entity_id';

        // Initialize PaymentService with all required parameters
        $this->paymentService = new PaymentService(
            $this->httpClient,
            $shift4Key,
            $aciKey,
            $aciEntityId
        );
    }
    public function testProcessPaymentShift4()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn(['id' => '123', 'amount' => 100, 'currency' => 'USD']);

        $mockHttpClient->method('request')->willReturn($mockResponse);

        $service = new PaymentService($mockHttpClient, 'test_shift4_key', 'test_aci_key', 'test_aci_entity_id');
        $result = $service->processPayment('shift4', ['amount' => 1, 'currency' => 'USD']);

        $this->assertEquals('123', $result['transaction_id']);
    }

    public function testProcessAciPayment()
    {
        // Mock Response from ACI
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn([
            'id' => 'aci_txn_987654321',
            'amount' => 9200,
            'currency' => 'EUR'
        ]);

        $this->httpClient
            ->method('request')
            ->willReturn($mockResponse);

        // Sample payment data
        $paymentData = [
            'amount' => 92.00,
            'currency' => 'EUR',
            'card_number' => '4200000000000000',
            'card_exp_year' => '2034',
            'card_exp_month' => '05',
            'card_cvv' => '123'
        ];

        $result = $this->paymentService->processPayment('aci', $paymentData);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('aci_txn_987654321', $result['id']);
        $this->assertEquals(9200, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    public function testInvalidProviderThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->paymentService->processPayment('invalid_provider', []);
    }
  
}
