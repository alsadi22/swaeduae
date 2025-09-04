<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserExportController extends Controller
{
    public function csv()
    {
        $filename = 'users_'.date('Ymd_His').'.csv';
        $users = User::all();
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $columns = ['id', 'name', 'email', 'role', 'created_at'];
        $callback = function() use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->created_at
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
