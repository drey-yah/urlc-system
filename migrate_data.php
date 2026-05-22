<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ResearchProposal;

$proposals = ResearchProposal::all();
foreach ($proposals as $proposal) {
    if (!$proposal->proposal_code) {
        $department = $proposal->user->department ? strtoupper($proposal->user->department) : 'UNI';
        $year = $proposal->created_at->format('Y');
        $sequence = str_pad($proposal->id, 3, '0', STR_PAD_LEFT);
        $proposal->update(['proposal_code' => "{$department}-UA-RP-{$year}-{$sequence}"]);
    }
    
    if ($proposal->document_path && $proposal->documents()->count() == 0) {
        $proposal->documents()->create([
            'document_tag' => "{$proposal->proposal_code}-PH1-MANUSCRIPT-V1",
            'document_type' => 'manuscript',
            'phase' => 1,
            'version' => 1,
            'file_path' => $proposal->document_path,
            'created_at' => $proposal->created_at,
            'updated_at' => $proposal->updated_at,
        ]);
    }
}
echo "Data migration complete.\n";
