<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    private function streamCsv(string $filename, array $header, \Closure $rowGen): StreamedResponse
    {
        return response()->streamDownload(function() use ($header, $rowGen) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            foreach ($rowGen() as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function users()
    {
        abort_unless(Schema::hasTable('users'), 404);
        return $this->streamCsv('users.csv',
            ['id','name','email','created_at'],
            function(){
                foreach (DB::table('users')->orderBy('id')->cursor() as $u) {
                    yield [$u->id, $u->name, $u->email, $u->created_at];
                }
            }
        );
    }

    public function hours()
    {
        abort_unless(Schema::hasTable('volunteer_hours'), 404);
        return $this->streamCsv('volunteer_hours.csv',
            ['user_id','opportunity_id','minutes','notes','updated_at'],
            function(){
                $q = DB::table('volunteer_hours')->orderBy('updated_at','desc')->cursor();
                foreach ($q as $h) yield [$h->user_id, $h->opportunity_id, (int)($h->minutes ?? 0), $h->notes, $h->updated_at];
            }
        );
    }

    public function certificates()
    {
        abort_unless(Schema::hasTable('certificates'), 404);
        return $this->streamCsv('certificates.csv',
            ['id','user_id','opportunity_id','code','title','issued_at','file_path'],
            function(){
                foreach (DB::table('certificates')->orderBy('id')->cursor() as $c) {
                    yield [$c->id, $c->user_id, $c->opportunity_id, $c->code, $c->title, $c->issued_at, $c->file_path];
                }
            }
        );
    }

    public function applications()
    {
        abort_unless(Schema::hasTable('applications'), 404);
        return $this->streamCsv('applications.csv',
            ['id','user_id','opportunity_id','status','created_at'],
            function(){
                foreach (DB::table('applications')->orderBy('id')->cursor() as $a) {
                    $status = $a->status ?? '';
                    yield [$a->id, $a->user_id, $a->opportunity_id, $status, $a->created_at];
                }
            }
        );
    }
}
