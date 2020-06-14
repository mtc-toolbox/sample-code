<?php

namespace models;

/**
 * Class Rate
 * @package models
 */
class Rate extends BaseModel
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Rate';
    }

    /**
     * @return array|null
     */
    public function getSessionDataAttribute()
    {
        $session = new Session($this->getConnection());

        $result = $session
            ->select('*')
            ->where('Id', $this['Session'])
            ->fetch();

        return $result;
    }

    /**
     * @return array|null
     */
    public function getProductDataAttribute()
    {
        $product = new Product($this->getConnection());

        $result = $product
            ->select('*')
            ->where('Id', $this['Product'])
            ->fetch();

        return $result;
    }

    /**
     * @param array $record
     *
     * @return int
     */
    public function insert(array $record = []): int
    {
        try {
            $this
                ->getConnection()
                ->beginTransaction();

            $record['Rate'] = $record['Rate'] ?? 0;

            $result = parent::insert($record);

            if (!$result) {

                $this
                    ->getConnection()
                    ->rollBack();

                return $result;
            }

            /**
             * @var Product $productModel
             */
            $productModel = new Product($this->getConnection());
            $product      = $productModel
                ->select('*')
                ->where('Id', $record['Product'])
                ->fetch();

            if (isset($product)) {
                $newRate      = ($product['AverageRate'] * $product['RateCount'] + ($record['Rate'])) / ($product['RateCount'] + 1);
                $newRateCount = $product['RateCount'] + 1;
                $records      = $productModel
                    ->where('Id', $record['Product'])
                    ->update(['AverageRate' => $newRate, 'RateCount' => $newRateCount]);

                if (!$records) {

                    $this
                        ->getConnection()
                        ->rollBack();

                    return $records;
                }

            }

            if (!$this->connection()->commit()) {
                $result = 0;
            }

        } catch (\Exception $e) {
            $this->setError((int)$e->getCode(), $e->getMessage());
            $result = 0;
        }

        return $result;
    }
}
