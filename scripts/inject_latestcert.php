<?php
$f = 'app/Http/Controllers/VolunteerDashboardController.php';
$c = file_get_contents($f);
$pattern = "/(return\\s+view\\(\\s*'volunteer\\.dashboard'[\\s\\S]*?;)/";
$inject =
"        // [latestCert injection]\n".
"        \$latestCert = null;\n".
"        if (Schema::hasTable('certificates')) {\n".
"            \$q = DB::table('certificates')->where('user_id', \$user->id);\n".
"            if (Schema::hasColumn('certificates','issued_at')) {\n".
"                \$q->orderBy('issued_at','desc');\n".
"            } else {\n".
"                \$q->orderBy('id','desc');\n".
"            }\n".
"            \$latestCert = \$q->select('code','file_path','title','issued_at')->first();\n".
"        }\n".
"        view()->share('latestCert', \$latestCert);\n\n".
"\$1";
$c2 = preg_replace($pattern, $inject, $c, 1, $count);
if ($count < 1) { fwrite(STDERR, \"Could not find insertion point.\\n\"); exit(1); }
file_put_contents($f, $c2);
echo \"OK\\n\";
