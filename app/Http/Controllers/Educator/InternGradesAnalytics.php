<?php

namespace App\Http\Controllers\Educator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternGrade;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InternGradesAnalytics extends Controller
{
    public function index()
    {
        // Get all classes for the filter
        $classes = \App\Models\ClassModel::orderBy('class_name')->get();
        Log::info('Fetched classes:', ['count' => $classes->count(), 'classes' => $classes->toArray()]);

        // Get companies for each class
        $classCompanies = [];
        foreach ($classes as $class) {
            $classCompanies[$class->class_id] = InternGrade::select('company_name')
                ->where('class_id', $class->class_id)
                ->whereNotNull('company_name')
                ->distinct()
                ->orderBy('company_name')
                ->pluck('company_name');
            Log::info('Companies for class ' . $class->class_name . ':', ['companies' => $classCompanies[$class->class_id]->toArray()]);
        }

        // Get chart data for each class
        $classChartData = [];
        foreach ($classes as $class) {
            $classChartData[$class->class_id] = [
                'class_name' => $class->class_name,
                'chart_data' => $this->getDistributionChartData(null, $class->class_id)
            ];
            Log::info('Chart data for class ' . $class->class_name . ':', ['data' => $classChartData[$class->class_id]]);
        }

        return view('educator.analytics.intern-grades-progress', compact(
            'classCompanies',
            'classes',
            'classChartData'
        ));
    }

    public function getAnalyticsData(Request $request)
    {
        $company = $request->input('company');
        $classId = $request->input('class_id');

        // Get chart data for each class
        $classChartData = [];
        $classes = \App\Models\ClassModel::orderBy('class_name')->get();
        
        foreach ($classes as $class) {
            // If class_id is provided, only get data for that class
            if ($classId && $class->class_id != $classId) {
                continue;
            }

            $classChartData[$class->class_id] = [
                'class_name' => $class->class_name,
                'chart_data' => $this->getDistributionChartData($company, $class->class_id)
            ];
        }

        return response()->json([
            'classChartData' => $classChartData
        ]);
    }

    private function getDistributionChartData($company = null, $classId = null)
    {
        $query = InternGrade::query();
        
        if ($company) {
            $query->where('company_name', $company);
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        // Add submission number filter
        if (request()->has('submission_number')) {
            $submissionNumber = request('submission_number');
            $query->where('submission_number', $submissionNumber);
        }

        // Define the competencies exactly as they appear in the database
        $competencies = [
            'ict_learning_competency' => 'ICT Learning',
            'twenty_first_century_skills' => '21st Century Skills',
            'expected_outputs_deliverables' => 'Expected Outputs'
        ];

        // Initialize datasets for each grade (1-4)
        $datasets = [
            [
                'label' => 'Grade 1',
                'data' => [],
                'backgroundColor' => '#10B981', // Green
                'borderColor' => '#10B981',
                'borderWidth' => 1
            ],
            [
                'label' => 'Grade 2',
                'data' => [],
                'backgroundColor' => '#F59E0B', // Yellow
                'borderColor' => '#F59E0B',
                'borderWidth' => 1
            ],
            [
                'label' => 'Grade 3',
                'data' => [],
                'backgroundColor' => '#F97316', // Orange
                'borderColor' => '#F97316',
                'borderWidth' => 1
            ],
            [
                'label' => 'Grade 4',
                'data' => [],
                'backgroundColor' => '#EF4444', // Red
                'borderColor' => '#EF4444',
                'borderWidth' => 1
            ]
        ];

        // For each competency, count students in each grade
        foreach ($competencies as $competencyKey => $competencyLabel) {
            // Count students for each grade (1-4) for this competency
            for ($grade = 1; $grade <= 4; $grade++) {
                $count = (clone $query)
                    ->whereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(grades, '$.{$competencyKey}')) AS DECIMAL) = ?", [$grade])
                    ->count();
                
                $datasets[$grade - 1]['data'][] = $count;
            }
        }

        // Check if we have any data
        $hasData = false;
        foreach ($datasets as $dataset) {
            if (array_sum($dataset['data']) > 0) {
                $hasData = true;
                break;
            }
        }

        // For debugging
        \Log::info('Chart Data', [
            'company' => $company,
            'classId' => $classId,
            'submission_number' => request('submission_number'),
            'datasets' => $datasets,
            'hasData' => $hasData,
            'query' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        return [
            'labels' => array_values($competencies),
            'datasets' => $datasets,
            'hasData' => $hasData
        ];
    }

    private function getSummaryData($company = null)
    {
        $query = InternGrade::query();
        
        if ($company) {
            $query->where('company_name', $company);
        }

        return [
            'fully_achieved' => (clone $query)->where('status', 'Fully Achieved')->count(),
            'partially_achieved' => (clone $query)->where('status', 'Partially Achieved')->count(),
            'no_achievement' => (clone $query)->where('status', 'No Achievement')->count()
        ];
    }

    private function getTrendChartData($company = null, $classId = null)
    {
        $query = InternGrade::query();
        
        if ($company) {
            $query->where('company_name', $company);
        }

        // Add class filter
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $monthlyAverages = $query->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('AVG(final_grade) as average_grade')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'labels' => $monthlyAverages->pluck('month'),
            'datasets' => [
                [
                    'label' => 'Average Grade',
                    'data' => $monthlyAverages->pluck('average_grade'),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    private function getCompetencyChartData($company = null, $classId = null)
    {
        $query = InternGrade::query();
        
        if ($company) {
            $query->where('company_name', $company);
        }

        // Add class filter
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $competencyAverages = $query->select(
            DB::raw('AVG(JSON_EXTRACT(grades, "$.ict_learning_competency")) as ict_learning'),
            DB::raw('AVG(JSON_EXTRACT(grades, "$.twenty_first_century_skills")) as century_skills'),
            DB::raw('AVG(JSON_EXTRACT(grades, "$.expected_outputs_deliverables")) as outputs')
        )->first();

        return [
            'labels' => ['ICT Learning', '21st Century Skills', 'Expected Outputs'],
            'datasets' => [
                [
                    'label' => 'Average Competency Score',
                    'data' => [
                        $competencyAverages->ict_learning,
                        $competencyAverages->century_skills,
                        $competencyAverages->outputs
                    ],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => '#3B82F6',
                    'pointBackgroundColor' => '#3B82F6',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => '#3B82F6'
                ]
            ]
        ];
    }

    private function getSchoolComparisonData($company = null, $classId = null)
    {
        $query = InternGrade::query()
            ->join('pnph_users', 'intern_grades.intern_id', '=', 'pnph_users.user_id')
            ->join('class_student', 'pnph_users.user_id', '=', 'class_student.user_id')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->join('schools', 'classes.school_id', '=', 'schools.school_id')
            ->select('schools.name', DB::raw('AVG(intern_grades.final_grade) as average_grade'))
            ->groupBy('schools.school_id', 'schools.name');

        if ($company) {
            $query->where('intern_grades.company_name', $company);
        }

        // Add class filter
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $schoolAverages = $query->get();

        return [
            'labels' => $schoolAverages->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Average Grade',
                    'data' => $schoolAverages->pluck('average_grade'),
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#2563EB',
                    'borderWidth' => 1
                ]
            ]
        ];
    }
} 