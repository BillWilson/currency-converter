<?php

namespace App\Actions;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CurrencyConverter
{
    use AsAction;

    public const SUPPORTED_CURRENCY = ['TWD', 'JPY', 'USD'];

    protected Collection $currencyData;

    protected array $headers = [
        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
        'Expires' => '0',
    ];

    public function rules(): array
    {
        return [
            'from' => [
                'string',
                Rule::in(self::SUPPORTED_CURRENCY),
                'required',
            ],
            'to' => [
                'string',
                Rule::in(self::SUPPORTED_CURRENCY),
                'required',
            ],
            'amount' => [
                'numeric',
                'min:0',
                'required',
            ],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function handle(ActionRequest $request): array
    {
        $this->initConfig();
        $data = collect($request->validated());

        $rate = $this->getCurrencyRate($data->get('from'), $data->get('to'));
        $result = $this->calculate($data['amount'], $rate);

        return [
            'result' => $this->format($result),
        ];
    }

    /**
     * @throws \JsonException
     */
    protected function initConfig(): void
    {
        $configJson = config('currency.setting');
        $currency = json_decode($configJson, true, 512, JSON_THROW_ON_ERROR);

        $this->currencyData = collect($currency['currencies']);
    }

    protected function getCurrencyRate(string $from, string $to): float
    {
        return (float) data_get(
            $this->currencyData,
            sprintf('%s.%s',$from,$to),
            0
        );
    }

    protected function calculate(float $amount, float $rate): float
    {
        return (float) bcmul($amount, $rate, 3);
    }

    protected function format(float $number): string
    {
        return number_format(round($number, 2), 2, '.', ',');
    }

    public function response($result): Response
    {
        $statusCode = empty($result) ? ResponseAlias::HTTP_NO_CONTENT : ResponseAlias::HTTP_OK;

        return response($result, $this->statusCode ?? $statusCode, $this->headers);
    }
}
