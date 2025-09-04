<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class OrgCheck extends Command
{
    protected $signature = 'org:check';
    protected $description = 'Show organization seeding status, counts, and recent records';

    public function handle(): int
    {
        $this->info('Organizations by license_status');
        $rows = Organization::selectRaw('COALESCE(license_status,"(null)") as status, COUNT(*) as count')
            ->groupBy('status')->orderBy('status')->get()
            ->map(fn($r) => ['status' => $r->status, 'count' => $r->count])
            ->toArray();
        $this->table(['status','count'], $rows);

        $this->info('Latest organizations');
        $latest = Organization::orderByDesc('id')->take(5)->get(['id','name_en','email','license_status'])->toArray();
        $this->table(['id','name_en','email','license_status'], $latest);

        $owner = User::where('email','support@swaeduae.ae')->first(['id','name','email']);
        $this->line('Seeded owner user: '.($owner ? json_encode($owner->toArray()) : 'NOT FOUND'));

        $this->line('organizations columns: '.implode(', ', Schema::getColumnListing('organizations')));

        return Command::SUCCESS;
    }
}
