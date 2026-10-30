<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BudgetAllocation;

class BudgetAllocationSeeder extends Seeder
{
    public function run()
    {
        // Clear 2026 allocations to prevent duplicate seeding
        BudgetAllocation::where('fiscal_year', 2026)->delete();

        $standardMain = [
            'implementation_pct' => 1.33333,
            'traveling_pct' => 1.88679,
            'traveling_local_gaa' => 2000.00,
            'traveling_local_tf' => 1320.75,
            'traveling_foreign_tf' => 1320.75,
            'implementation_tf' => 5546.67,
            'total_allocation' => 10188.17,
        ];

        $mainCampusPrograms = [
            'COLLEGE OF ARTS AND SCIENCES' => [
                'Bachelor of Arts in English Language Studies',
                'Bachelor of Arts in Political Science',
                'Bachelor of Arts in Psychology',
                'Bachelor of Arts in Communication',
                'Bachelor of Science in Community Development',
            ],
            'COLLEGE OF CRIMINAL JUSTICE EDUCATION' => [
                'Bachelor of Science in Criminology',
                'Bachelor of Science in Industrial Security',
            ],
            'COLLEGE OF MANAGEMENT AND GOVERNANCE' => [
                'Bachelor of Science in Accountancy',
                'Bachelor of Science in Business Administration',
                'Bachelor of Science in Cooperatives Management',
                'Bachelor of Science in Entrepreneurship',
                'Bachelor of Science in Entrepreneurship Special Class',
                'Bachelor of Science in Hospitality Management',
                'Bachelor of Science in Management Accounting',
                'Bachelor of Science in Office Administration',
                'Bachelor of Science in Tourism Management',
            ],
            'COLLEGE OF TECHNOLOGY' => [
                'Bachelor of Industrial Technology',
                'TESDA Program Electrical Technology',
            ],
            'COLLEGE OF ENGINEERING & ARCHITECTURE' => [
                'Bachelor of Science in Architecture',
                'Bachelor of Science in Civil Engineering',
                'Bachelor of Science in Computer Engineering',
                'Bachelor of Science in Electronics Engineering',
                'Bachelor of Science in Electrical Engineering',
                'Bachelor of Science in Mechanical Engineering',
            ],
            'COLLEGE OF COMPUTING AND INFORMATION SCIENCES' => [
                'Bachelor of Library and Information Science',
                'Bachelor of Science in Computer Science',
                'Bachelor of Science in Information Technology',
            ],
            'COLLEGE OF TEACHER EDUCATION' => [
                'Bachelor of Early Childhood Education',
                'Bachelor of Elementary Education',
                'Bachelor of Physical Education',
                'Bachelor of Secondary Education - Major 1',
                'Bachelor of Secondary Education - Major 2',
                'Bachelor of Special Needs Education-Generalist',
                'Bachelor of Technical-Vocational Teacher Education',
                'Bachelor of Technical Vocational-Teacher',
                'Bachelor of Technical Vocational Teacher Education (Extension)',
            ],
            'COLLEGE OF MARITIME STUDIES' => [
                'Bachelor of Science in Marine Engineering',
                'Bachelor of Science in Marine Transportation',
            ],
            'GRADUATE SCHOOL' => [
                'Certificate in Teaching',
                'Diploma in Teaching',
            ],
            'GRADUATE STUDIES (CTE)' => [
                'Doctor of Philosophy',
                'Master of Arts in Education',
                'Master of Arts in Teaching',
                'Master in Education',
            ],
            'GRADUATE STUDIES (CBA)' => [
                'Doctor of Public Administration',
                'Master of Business Administration',
                'Master of Public Administration',
            ],
        ];

        // Seed Main Campus Colleges
        foreach ($mainCampusPrograms as $college => $programs) {
            $seq = 1;
            foreach ($programs as $prog) {
                BudgetAllocation::create(array_merge($standardMain, [
                    'fiscal_year' => 2026,
                    'campus' => 'Main Campus',
                    'college' => $college,
                    'program_name' => $prog,
                    'sequence_no' => $seq++,
                ]));
            }
        }

        // Laboratory High School (3 programs with dash, and 1 TOTAL Allotment row)
        BudgetAllocation::create([
            'fiscal_year' => 2026,
            'campus' => 'Main Campus',
            'college' => 'LABORATORY HIGH SCHOOL',
            'program_name' => 'LABORATORY HIGH SCHOOL- JUNIOR HIGH SCHOOL REGULAR',
            'sequence_no' => null,
            'is_allotment_row' => false,
            'implementation_pct' => 0,
            'traveling_pct' => 0,
            'traveling_local_gaa' => 0,
            'traveling_local_tf' => 0,
            'traveling_foreign_tf' => 0,
            'implementation_tf' => 0,
            'total_allocation' => 0,
        ]);

        BudgetAllocation::create([
            'fiscal_year' => 2026,
            'campus' => 'Main Campus',
            'college' => 'LABORATORY HIGH SCHOOL',
            'program_name' => 'LABORATORY HIGH SCHOOL- JUNIOR HIGH SCHOOL STE',
            'sequence_no' => null,
            'is_allotment_row' => false,
            'implementation_pct' => 0,
            'traveling_pct' => 0,
            'traveling_local_gaa' => 0,
            'traveling_local_tf' => 0,
            'traveling_foreign_tf' => 0,
            'implementation_tf' => 0,
            'total_allocation' => 0,
        ]);

        BudgetAllocation::create([
            'fiscal_year' => 2026,
            'campus' => 'Main Campus',
            'college' => 'LABORATORY HIGH SCHOOL',
            'program_name' => 'LABORATORY HIGH SCHOOL- SENIOR HIGH SCHOOL STEM',
            'sequence_no' => null,
            'is_allotment_row' => false,
            'implementation_pct' => 0,
            'traveling_pct' => 0,
            'traveling_local_gaa' => 0,
            'traveling_local_tf' => 0,
            'traveling_foreign_tf' => 0,
            'implementation_tf' => 0,
            'total_allocation' => 0,
        ]);

        // TOTAL Allotted Budget Row for Laboratory High School
        BudgetAllocation::create([
            'fiscal_year' => 2026,
            'campus' => 'Main Campus',
            'college' => 'LABORATORY HIGH SCHOOL',
            'program_name' => 'TOTAL (Allotted Budget)',
            'sequence_no' => 1,
            'is_allotment_row' => true,
            'implementation_pct' => 1.33333,
            'traveling_pct' => 1.88679,
            'traveling_local_gaa' => 2000.00,
            'traveling_local_tf' => 1320.75,
            'traveling_foreign_tf' => 1320.75,
            'implementation_tf' => 5546.67,
            'total_allocation' => 10188.18,
        ]);

        // Libertad Campus (2 programs)
        $libertad = [
            'BS in Information Technology',
            'BS in Hospitality Management',
        ];
        $seq = 1;
        foreach ($libertad as $prog) {
            BudgetAllocation::create(array_merge($standardMain, [
                'fiscal_year' => 2026,
                'campus' => 'Libertad Campus',
                'college' => 'Libertad Programs',
                'program_name' => $prog,
                'sequence_no' => $seq++,
                'total_allocation' => 10188.18,
            ]));
        }

        // Caluya Campus (3 programs)
        $caluya = [
            'BS in Entrepreneurship',
            'BInd Tech Major in Electrical Technology',
            'BS in Hospitality Management',
        ];
        $seq = 1;
        foreach ($caluya as $prog) {
            BudgetAllocation::create(array_merge($standardMain, [
                'fiscal_year' => 2026,
                'campus' => 'Caluya Campus',
                'college' => 'Caluya Programs',
                'program_name' => $prog,
                'sequence_no' => $seq++,
                'total_allocation' => 10188.17,
            ]));
        }

        // Hamtic Campus (Lump sum GAA 8 programs: 54,000.00)
        BudgetAllocation::create([
            'fiscal_year' => 2026,
            'campus' => 'Hamtic Campus',
            'college' => null,
            'program_name' => 'Subtotal',
            'sequence_no' => 8,
            'is_allotment_row' => true,
            'implementation_pct' => 0,
            'traveling_pct' => 0,
            'traveling_local_gaa' => 54000.00,
            'traveling_local_tf' => 0,
            'traveling_foreign_tf' => 0,
            'implementation_tf' => 0,
            'total_allocation' => 54000.00,
            'source_of_fund' => 'Source of Fund: GAA',
            'remarks' => 'Total for 8 programs in Hamtic Campus funded under GAA',
        ]);

        // Tibiao Campus (Lump sum GAA 14 programs: 30,000.00)
        BudgetAllocation::create([
            'fiscal_year' => 2026,
            'campus' => 'Tibiao Campus',
            'college' => null,
            'program_name' => 'Subtotal (TLMC)',
            'sequence_no' => 14,
            'is_allotment_row' => true,
            'implementation_pct' => 0,
            'traveling_pct' => 0,
            'traveling_local_gaa' => 30000.00,
            'traveling_local_tf' => 0,
            'traveling_foreign_tf' => 0,
            'implementation_tf' => 0,
            'total_allocation' => 30000.00,
            'source_of_fund' => 'Source of Fund: GAA',
            'remarks' => 'Total for 14 programs (TLMC) in Tibiao Campus funded under GAA',
        ]);
    }
}
