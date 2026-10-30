<?php

namespace App\Http\Controllers;

use App\Models\BudgetAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordingStaffBudgetAllocationController extends Controller
{
    public function index(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', 2026);
        $campus = $request->input('campus');
        $search = $request->input('search');

        $query = BudgetAllocation::where('fiscal_year', $fiscalYear);

        if ($campus && $campus !== 'all') {
            $query->where('campus', $campus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('program_name', 'ilike', "%{$search}%")
                  ->orWhere('college', 'ilike', "%{$search}%");
            });
        }

        $allAllocations = $query->orderBy('campus')
            ->orderBy('college')
            ->orderBy('sequence_no')
            ->get();

        // Calculate KPI Metrics using calculateMetrics helper
        $kpiQuery = BudgetAllocation::where('fiscal_year', $fiscalYear);
        if ($campus && $campus !== 'all') {
            $kpiQuery->where('campus', $campus);
        }
        $kpiRecords = $kpiQuery->get();
        $metrics = $this->calculateMetrics($kpiRecords);

        // TF Pool Subtotal for Main Campus + Libertad + Caluya (106,000 GAA, 70,000 TF Local, 70,000 TF Foreign, 416,000 Impl TF)
        $tfPoolRecords = $kpiRecords->whereIn('campus', ['Main Campus', 'Libertad Campus', 'Caluya Campus']);
        $tfPoolMetrics = $this->calculateMetrics($tfPoolRecords);

        // Group allocations by Campus, then by College, ordered by institutional campus sequence
        $campusOrder = ['Main Campus', 'Libertad Campus', 'Caluya Campus', 'Hamtic Campus', 'Tibiao Campus'];
        $grouped = $allAllocations->groupBy('campus')->map(function ($campusGroup) {
            return $campusGroup->groupBy('college');
        })->sortBy(function ($colleges, $campusName) use ($campusOrder) {
            $idx = array_search($campusName, $campusOrder);
            return $idx !== false ? $idx : 99;
        });

        // Available campuses and years for filters
        $campuses = BudgetAllocation::distinct()->pluck('campus')->filter()->values();
        $years = BudgetAllocation::distinct()->orderBy('fiscal_year', 'desc')->pluck('fiscal_year')->filter()->values();
        if ($years->isEmpty()) {
            $years = collect([2026, 2025]);
        }

        return view('recording_staff.budget_allocations.index', compact(
            'grouped',
            'metrics',
            'tfPoolMetrics',
            'fiscalYear',
            'campus',
            'search',
            'campuses',
            'years'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|integer|min:2020|max:2035',
            'campus' => 'required|string|max:255',
            'college' => 'nullable|string|max:255',
            'program_name' => 'required|string|max:255',
            'sequence_no' => 'nullable|integer',
            'implementation_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_local_gaa' => 'nullable|numeric|min:0',
            'traveling_local_tf' => 'nullable|numeric|min:0',
            'traveling_foreign_tf' => 'nullable|numeric|min:0',
            'implementation_tf' => 'nullable|numeric|min:0',
            'total_allocation' => 'nullable|numeric|min:0',
            'source_of_fund' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = Auth::id();
        
        // Default null numeric fields to 0
        foreach (['implementation_pct', 'traveling_pct', 'traveling_local_gaa', 'traveling_local_tf', 'traveling_foreign_tf', 'implementation_tf'] as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        BudgetAllocation::create($validated);

        return redirect()->route('recording_staff.budget_allocations.index', [
            'fiscal_year' => $validated['fiscal_year'],
            'campus' => $validated['campus'],
        ])->with('success', 'Budget allocation for "' . $validated['program_name'] . '" saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $allocation = BudgetAllocation::findOrFail($id);

        $validated = $request->validate([
            'fiscal_year' => 'required|integer|min:2020|max:2035',
            'campus' => 'required|string|max:255',
            'college' => 'nullable|string|max:255',
            'program_name' => 'required|string|max:255',
            'sequence_no' => 'nullable|integer',
            'implementation_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_local_gaa' => 'nullable|numeric|min:0',
            'traveling_local_tf' => 'nullable|numeric|min:0',
            'traveling_foreign_tf' => 'nullable|numeric|min:0',
            'implementation_tf' => 'nullable|numeric|min:0',
            'total_allocation' => 'nullable|numeric|min:0',
            'source_of_fund' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        foreach (['implementation_pct', 'traveling_pct', 'traveling_local_gaa', 'traveling_local_tf', 'traveling_foreign_tf', 'implementation_tf'] as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        $allocation->update($validated);

        return redirect()->back()->with('success', 'Budget allocation updated successfully.');
    }

    public function destroy($id)
    {
        $allocation = BudgetAllocation::findOrFail($id);
        $name = $allocation->program_name;
        $allocation->delete();

        return redirect()->back()->with('success', 'Budget allocation for "' . $name . '" removed.');
    }

    public function updateCollege(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|integer',
            'campus' => 'required|string',
            'college' => 'required|string',
            'implementation_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_local_gaa' => 'nullable|numeric|min:0',
            'traveling_local_tf' => 'nullable|numeric|min:0',
            'traveling_foreign_tf' => 'nullable|numeric|min:0',
            'implementation_tf' => 'nullable|numeric|min:0',
            'total_allocation' => 'nullable|numeric|min:0',
            'source_of_fund' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'distribution_mode' => 'required|in:per_program,divide_equally',
        ]);

        $query = BudgetAllocation::where('fiscal_year', $validated['fiscal_year'])
            ->where('campus', $validated['campus'])
            ->where('college', $validated['college']);

        $count = $query->count();
        if ($count === 0) {
            return redirect()->back()->with('error', 'No programs found for ' . $validated['college']);
        }

        if ($validated['distribution_mode'] === 'divide_equally') {
            $updateData = [
                'implementation_pct' => ($validated['implementation_pct'] ?? 0) / $count,
                'traveling_pct' => ($validated['traveling_pct'] ?? 0) / $count,
                'traveling_local_gaa' => ($validated['traveling_local_gaa'] ?? 0) / $count,
                'traveling_local_tf' => ($validated['traveling_local_tf'] ?? 0) / $count,
                'traveling_foreign_tf' => ($validated['traveling_foreign_tf'] ?? 0) / $count,
                'implementation_tf' => ($validated['implementation_tf'] ?? 0) / $count,
                'total_allocation' => ($validated['total_allocation'] ?? 0) / $count,
            ];
        } else {
            // Apply standard figures to each program in this college
            $updateData = [
                'implementation_pct' => $validated['implementation_pct'] ?? 0,
                'traveling_pct' => $validated['traveling_pct'] ?? 0,
                'traveling_local_gaa' => $validated['traveling_local_gaa'] ?? 0,
                'traveling_local_tf' => $validated['traveling_local_tf'] ?? 0,
                'traveling_foreign_tf' => $validated['traveling_foreign_tf'] ?? 0,
                'implementation_tf' => $validated['implementation_tf'] ?? 0,
                'total_allocation' => $validated['total_allocation'] ?? 0,
            ];
        }

        if (array_key_exists('source_of_fund', $validated)) {
            $updateData['source_of_fund'] = $validated['source_of_fund'];
        }
        if (array_key_exists('remarks', $validated)) {
            $updateData['remarks'] = $validated['remarks'];
        }

        $query->update($updateData);

        return redirect()->back()->with('success', 'Budget allocations for ' . $validated['college'] . ' (' . $count . ' programs) updated successfully.');
    }

    public function saveCollegeAllotment(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|integer',
            'campus' => 'required|string',
            'college' => 'nullable|string',
            'program_name' => 'nullable|string|max:255',
            'sequence_no' => 'nullable|integer',
            'implementation_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_pct' => 'nullable|numeric|min:0|max:100',
            'traveling_local_gaa' => 'nullable|numeric|min:0',
            'traveling_local_tf' => 'nullable|numeric|min:0',
            'traveling_foreign_tf' => 'nullable|numeric|min:0',
            'implementation_tf' => 'nullable|numeric|min:0',
            'total_allocation' => 'nullable|numeric|min:0',
            'source_of_fund' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        foreach (['implementation_pct', 'traveling_pct', 'traveling_local_gaa', 'traveling_local_tf', 'traveling_foreign_tf', 'implementation_tf', 'total_allocation'] as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        $defaultName = 'TOTAL (Allotted Budget)';
        if ($validated['campus'] === 'Hamtic Campus') {
            $defaultName = 'Subtotal';
        } elseif ($validated['campus'] === 'Tibiao Campus') {
            $defaultName = 'Subtotal (TLMC)';
        }
        $programName = !empty($validated['program_name']) ? $validated['program_name'] : $defaultName;
        $collegeValue = !empty($validated['college']) ? $validated['college'] : null;

        BudgetAllocation::updateOrCreate(
            [
                'fiscal_year' => $validated['fiscal_year'],
                'campus' => $validated['campus'],
                'college' => $collegeValue,
                'is_allotment_row' => true,
            ],
            [
                'program_name' => $programName,
                'sequence_no' => $validated['sequence_no'] ?? 1,
                'implementation_pct' => $validated['implementation_pct'],
                'traveling_pct' => $validated['traveling_pct'],
                'traveling_local_gaa' => $validated['traveling_local_gaa'],
                'traveling_local_tf' => $validated['traveling_local_tf'],
                'traveling_foreign_tf' => $validated['traveling_foreign_tf'],
                'implementation_tf' => $validated['implementation_tf'],
                'total_allocation' => $validated['total_allocation'],
                'source_of_fund' => $validated['source_of_fund'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]
        );

        $targetLabel = $collegeValue ?: $validated['campus'];
        return redirect()->back()->with('success', 'Allotted Budget / Subtotal row for ' . $targetLabel . ' saved successfully.');
    }

    public function deleteCollegeAllotment(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|integer',
            'campus' => 'required|string',
            'college' => 'nullable|string',
        ]);

        $query = BudgetAllocation::where('fiscal_year', $validated['fiscal_year'])
            ->where('campus', $validated['campus'])
            ->where('is_allotment_row', true);

        if (!empty($validated['college'])) {
            $query->where('college', $validated['college']);
        } else {
            $query->whereNull('college');
        }

        $query->delete();

        return redirect()->back()->with('success', 'Allotted budget row removed. Total row reverted to sum of individual programs.');
    }

    public function print(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', 2026);
        $campus = $request->input('campus');

        $query = BudgetAllocation::where('fiscal_year', $fiscalYear);

        if ($campus && $campus !== 'all') {
            $query->where('campus', $campus);
        }

        $allAllocations = $query->orderBy('campus')
            ->orderBy('college')
            ->orderBy('sequence_no')
            ->get();

        $metrics = $this->calculateMetrics($allAllocations);

        // TF Pool Subtotal for Main Campus + Libertad + Caluya
        $tfPoolRecords = $allAllocations->whereIn('campus', ['Main Campus', 'Libertad Campus', 'Caluya Campus']);
        $tfPoolMetrics = $this->calculateMetrics($tfPoolRecords);

        $campusOrder = ['Main Campus', 'Libertad Campus', 'Caluya Campus', 'Hamtic Campus', 'Tibiao Campus'];
        $grouped = $allAllocations->groupBy('campus')->map(function ($campusGroup) {
            return $campusGroup->groupBy('college');
        })->sortBy(function ($colleges, $campusName) use ($campusOrder) {
            $idx = array_search($campusName, $campusOrder);
            return $idx !== false ? $idx : 99;
        });

        return view('recording_staff.budget_allocations.print', compact(
            'grouped',
            'metrics',
            'tfPoolMetrics',
            'fiscalYear',
            'campus'
        ));
    }

    protected function calculateMetrics($records)
    {
        $collegeGroups = $records->groupBy(function ($item) {
            return $item->campus . '|||' . $item->college;
        });

        $totalGaa = 0;
        $totalLocalTf = 0;
        $totalForeignTf = 0;
        $totalImplTf = 0;
        $grandTotal = 0;
        $totalPrograms = 0;

        foreach ($collegeGroups as $group) {
            $allotment = $group->firstWhere('is_allotment_row', true);
            $programs = $group->where('is_allotment_row', false);

            if ($allotment) {
                $totalGaa += (float)$allotment->traveling_local_gaa;
                $totalLocalTf += (float)$allotment->traveling_local_tf;
                $totalForeignTf += (float)$allotment->traveling_foreign_tf;
                $totalImplTf += (float)$allotment->implementation_tf;
                $grandTotal += (float)$allotment->total_allocation;
                $totalPrograms += !empty($allotment->sequence_no) ? (int)$allotment->sequence_no : ($programs->count() > 0 ? $programs->count() : 1);
            } else {
                $totalGaa += (float)$programs->sum('traveling_local_gaa');
                $totalLocalTf += (float)$programs->sum('traveling_local_tf');
                $totalForeignTf += (float)$programs->sum('traveling_foreign_tf');
                $totalImplTf += (float)$programs->sum('implementation_tf');
                $grandTotal += (float)$programs->sum('total_allocation');
                $totalPrograms += $programs->count();
            }
        }

        return [
            'total_gaa' => $totalGaa,
            'total_local_tf' => $totalLocalTf,
            'total_foreign_tf' => $totalForeignTf,
            'total_implementation_tf' => $totalImplTf,
            'grand_total' => $grandTotal,
            'total_programs' => $totalPrograms,
        ];
    }
}
