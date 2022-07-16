<?php
#uses(Tests\TestCase::class);

function formatPath(string $path = 'api/currency/convert', array $queryString = []): string
{
    return sprintf('/%s?%s', $path, http_build_query($queryString));
}

test('Convert USD to USD', function () {
    $queryString = [
        'from' => 'USD',
        'to' => 'USD',
        'amount' => '123.554',
    ];

    $response = $this->get(formatPath(queryString: $queryString));

    $this->assertSame($response->json('result'), '123.55');
});

test('Convert USD to TWD', function () {
    $queryString = [
        'from' => 'USD',
        'to' => 'TWD',
        'amount' => '1000',
    ];

    $response = $this->get(formatPath(queryString: $queryString));

    $this->assertSame($response->json('result'), '30,444.00');
    $this->assertNotSame($response->json('result'), '30444.00');
});

test('Convert to Same currency', function () {
    foreach (['TWD', 'JPY', 'USD'] as $currency) {
        $queryString = [
            'from' => $currency,
            'to' => $currency,
            'amount' => '1',
        ];

        $response = $this->get(formatPath(queryString: $queryString));

        $this->assertSame($response->json('result'), '1.00', $currency);
        $this->assertNotSame($response->json('result'), '1', $currency);
    }
});

test('Unsupported currency', function () {
    $queryString = [
        'from' => 'EUR',
        'to' => 'EUR',
        'amount' => '1',
    ];

    $response = $this->get(formatPath(queryString: $queryString));

    $response->assertInvalid(['from', 'to']);
});
