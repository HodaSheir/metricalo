<?php


namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentService
{

  public function __construct(private HttpClientInterface $httpClient, private string $shift4Key, private string $aciKey, private string $aciEntityId) {

  }

  public function processPayment(string $provider, array $data): array
  {
    return $provider === 'shift4' ? $this->processShift4($data) : $this->processAci($data);
  }

  private function processShift4(array $data): array
  {
    $response = $this->httpClient->request('POST', 'https://dev.shift4.com/docs/api#charge-create', [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->shift4Key,  // Ensure the API key is prefixed with "Bearer"
        'Content-Type' => 'application/json'
      ],
      'json' => [
        'amount' => $data['amount'] * 100, // Convert to cents
        'currency' => $data['currency'],
        'card' => [
          'number' => $data['card_number'],
          'expMonth' => $data['exp_month'],
          'expYear' => $data['exp_year'],
          'cvc' => $data['cvv']
        ]
      ]
    ]);

    return $this->formatResponse($response->toArray());
  }

  private function processAci(array $data): array
  {
    $response = $this->httpClient->request('POST', 'https://docs.oppwa.com/integrations/server-to-server#syncPayment', [
      'query' => [
        'entityId' => $this->aciEntityId,
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'paymentBrand' => 'VISA',
        'card.number' => $data['card_number'],
        'card.expiryMonth' => $data['exp_month'],
        'card.expiryYear' => $data['exp_year'],
        'card.cvv' => $data['cvv']
      ],
      'headers' => [
        'Authorization' => 'Bearer ' . $this->aciKey
      ]
    ]);

    return $this->formatResponse($response->toArray());
  }

  private function formatResponse(array $response): array
  {
    return [
      'transaction_id' => $response['id'] ?? 'N/A',
      'date' => $response['timestamp'] ?? date('Y-m-d H:i:s'),
      'amount' => $response['amount'] ?? 0,
      'currency' => $response['currency'] ?? 'N/A',
      'card_bin' => substr($response['card']['number'] ?? '000000', 0, 6)
    ];
  }
}























?>