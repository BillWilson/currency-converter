<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ExampleSeeder extends Seeder
{

    protected Faker $faker;

    protected Carbon $orderCreatedAt;

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run(Faker $faker)
    {
        $this->faker = $faker;
        $this->orderCreatedAt = Carbon::create(2021, 02, 01);

        for ($x = 0; $x < 100; $x++) {
            DB::transaction(fn() => $this->createProperty(100));
        }
    }

    /**
     * @throws \Exception
     */
    protected function createProperty(int $count): void
    {
        for ($x = 0; $x < $count; $x++){
            dump($x);
            $id = DB::table('properties')
                ->insertGetId([
                    'name' => $this->faker->company()
                ]);

            $this->createRoom($id, random_int(10, 50));
        }
    }

    protected function createRoom(int $propertyId, int $count): void
    {
        for ($x = 0; $x < $count; $x++){
//            dump($x);
            $id = DB::table('rooms')
                ->insertGetId([
                    'property_id' => $propertyId,
                    'name' => $this->faker->company(),
                ]);

            $this->createOrder($id, random_int(10, 200));
        }
    }

    protected function createOrder(int $roomId, int $count): void
    {
        for ($x = 0; $x < $count; $x++){
//            dump($x);
           DB::table('orders')
                ->insert([
                    'room_id' => $roomId,
                    'price' => $this->faker->randomFloat(2, 1000, 5000),
                    'created_at' => $this->orderCreatedAt
                ]);

            $this->orderCreatedAt->addSecond();
        }
    }
}
