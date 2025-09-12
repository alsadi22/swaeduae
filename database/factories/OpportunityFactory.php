<?php

namespace Database\Factories;

use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        $cols = Schema::hasTable('opportunities')
            ? Schema::getColumnListing('opportunities')
            : [];

        $data = [];
        $set = function (string $col, $val) use (&$data, $cols) {
            if (in_array($col, $cols, true)) {
                $data[$col] = $val;
            }
        };

        // Common, safe defaults (only applied if the column exists)
        $set('title',        $this->faker->sentence(3));
        $set('name',         $this->faker->sentence(3));
        $set('description',  $this->faker->paragraph());
        $set('status',       'active');
        $set('city',         $this->faker->city());
        $set('category',     'General');
        $set('region',       $this->faker->state());
        $set('badge',        $this->faker->word());

        // Time window (if present)
        $start = now();
        $end   = now()->addDay();
        $set('starts_at',    $start);
        $set('ends_at',      $end);
        $set('start_at',     $start);
        $set('end_at',       $end);

        // Misc optional columns seen in your migrations
        $set('token',        (string) Str::uuid());
        $set('checkin_token',(string) Str::uuid());
        $set('checkout_token',(string) Str::uuid());
        $set('featured',     false);

        // Foreign keys (leave null unless your schema requires NOT NULL)
        $set('organization_id', null);
        $set('owner_id',        null);

        return $data;
    }
}
