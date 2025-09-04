<?php
$f = 'app/Http/Controllers/VolunteerDashboardController.php';
$src = file_get_contents($f);

if (strpos($src, 'latestCert') === false) {
    // Ensure DB/Schema imports exist
    if (strpos($src, "use Illuminate\\Support\\Facades\\DB;") === false) {
        $src = preg_replace(
            '/^namespace App\\\\Http\\\\Controllers;\\s*/m',
            "namespace App\\Http\\Controllers;\nuse Illuminate\\Support\\Facades\\DB;\nuse Illuminate\\Support\\Facades\\Schema;\n",
            $src, 1
        );
    } elseif (strpos($src, "use Illuminate\\Support\\Facades\\Schema;") === false) {
        $src = preg_replace(
            '/^use Illuminate\\\\Support\\\\Facades\\\\DB;\\s*$/m',
            "use Illuminate\\Support\\Facades\\DB;\nuse Illuminate\\Support\\Facades\\Schema;",
            $src, 1
        );
    }

    // Inject latestCert before returning the view
    $src = preg_replace(
        "/\\n\\s*return\\s+view\\(\\s*'volunteer\\.dashboard'\\s*,\\s*\\[/m",
        "\n        \$latestCert = null;\n        if (\\Schema::hasTable('certificates')) {\n            \$q = \\DB::table('certificates')->where('user_id', \$user->id);\n            if (\\Schema::hasColumn('certificates','issued_at')) { \$q->orderBy('issued_at','desc'); } else { \$q->orderBy('id','desc'); }\n            \$latestCert = \$q->select('code','file_path','title','issued_at')->first();\n        }\n\n        return view('volunteer.dashboard', [\n            'latestCert' => \$latestCert,\n            ",
        $src, 1
    );

    file_put_contents($f, $src);
    echo "VolunteerDashboardController patched.\n";
} else {
    echo "Already patched.\n";
}
