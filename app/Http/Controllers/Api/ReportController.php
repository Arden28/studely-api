<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Search Endpoint for StudentSelector
     * Returns top 10 matches or recent students if query is empty
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $tenantId = $request->user()->tenant_id;

        $q = Student::where('tenant_id', $tenantId)
            ->with('user:id,name,email')
            ->select('id', 'user_id', 'reg_no');

        if ($query) {
            $q->where(function($sub) use ($query) {
                $sub->where('reg_no', 'like', "%{$query}%")
                    ->orWhereHas('user', function($u) use ($query) {
                        $u->where('name', 'like', "%{$query}%");
                    });
            });
        }

        return $q->limit(10)
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'label' => ($s->user->name ?? 'Unknown') . ' (' . $s->reg_no . ')',
                    'value' => $s->id
                ];
            });
    }

    /**
     * Main Dashboard Analytics
     */
    public function overview(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $range = $request->input('timeRange', '7d');

        $startDate = match ($range) {
            'today' => Carbon::today(),
            '30d' => Carbon::now()->subDays(30),
            'all' => Carbon::create(2000),
            default => Carbon::now()->subDays(7),
        };

        // --- KPIs ---
        $totalStudents = Student::where('tenant_id', $tenantId)->count();

        $activeNow = Attempt::where('tenant_id', $tenantId)
            ->where('started_at', '>=', Carbon::now()->subHours(2))
            ->whereNull('submitted_at')
            ->count();

        $avgPerformance = Attempt::where('tenant_id', $tenantId)
            ->where('submitted_at', '>=', $startDate)
            ->avg('score') ?? 0;

        $atRiskCount = DB::table('attempts')
            ->select('student_id', DB::raw('AVG(score) as avg_score'))
            ->where('tenant_id', $tenantId)
            ->groupBy('student_id')
            ->having('avg_score', '<', 50)
            ->count();

        // --- Trend ---
        $trendData = Attempt::where('tenant_id', $tenantId)
            ->where('submitted_at', '>=', $startDate)
            ->selectRaw('DATE(submitted_at) as date, AVG(score) as avg_score, COUNT(*) as attempts')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('D, M j'),
                    'avg_score' => round($item->avg_score, 1),
                    'attempts' => $item->attempts
                ];
            });

        // --- Weak Points (Global) ---
        $weakPoints = $this->getWeakPoints($tenantId, null);

        // --- Assessment Stats ---
        $assessmentStats = Assessment::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->withCount('attempts')
            ->withAvg('attempts', 'score')
            ->get()
            ->map(function ($assessment) use ($totalStudents) {
                $uniqueAttempters = DB::table('attempts')
                    ->where('assessment_id', $assessment->id)
                    ->distinct('student_id')
                    ->count('student_id');

                return [
                    'assessment_id' => $assessment->id,
                    'title' => $assessment->title,
                    'completion_rate' => $totalStudents > 0 ? round(($uniqueAttempters / $totalStudents) * 100) : 0,
                    'avg_score' => round($assessment->attempts_avg_score ?? 0),
                    'median_score' => round($assessment->attempts_avg_score ?? 0),
                    'p90_score' => 0,
                ];
            });

        // --- Student List ---
        $students = Student::where('tenant_id', $tenantId)
            ->with(['user:id,name,email'])
            ->withCount('attempts as total_attempts')
            ->withAvg('attempts as avg_score', 'score')
            ->withMax('attempts as last_active', 'submitted_at')
            ->limit(50)
            ->get()
            ->map(function ($s) {
                $avg = round($s->avg_score ?? 0);
                if ($s->total_attempts == 0) $status = 'Inactive';
                elseif ($avg < 50) $status = 'At Risk';
                elseif ($avg > 80) $status = 'Exceling';
                else $status = 'On Track';

                return [
                    'student_id' => $s->id,
                    'name' => $s->user?->name ?? 'Unknown',
                    'reg_no' => $s->reg_no,
                    'total_attempts' => $s->total_attempts,
                    'avg_score' => $avg,
                    'last_active' => $s->last_active,
                    'status' => $status,
                    'weakest_module' => null
                ];
            });

        return response()->json([
            'kpis' => [
                'total_students' => $totalStudents,
                'active_now' => $activeNow,
                'avg_performance' => round($avgPerformance),
                'at_risk_count' => $atRiskCount,
            ],
            'trend' => $trendData,
            'weak_points' => $weakPoints,
            'assessment_stats' => $assessmentStats,
            'student_performances' => $students
        ]);
    }

    /**
     * Individual Student Report
     */
    public function student(Request $request, $studentId)
    {
        $tenantId = $request->user()->tenant_id;
        $student = Student::where('tenant_id', $tenantId)->with('user')->findOrFail($studentId);

        // 1. Comparative History
        $history = Attempt::where('student_id', $studentId)
            ->join('assessments', 'attempts.assessment_id', '=', 'assessments.id')
            ->select('attempts.*', 'assessments.title as assessment_title', 'assessments.id as assessment_id')
            ->orderBy('attempts.submitted_at', 'asc')
            ->get()
            ->map(function ($attempt) {
                $cohortAvg = Attempt::where('assessment_id', $attempt->assessment_id)->avg('score');
                return [
                    'assessment' => $attempt->assessment_title,
                    'score' => $attempt->score,
                    'cohort_avg' => round($cohortAvg ?? 0),
                    'date' => $attempt->submitted_at?->format('Y-m-d'),
                    'duration' => $attempt->duration_sec ? gmdate("H:i:s", $attempt->duration_sec) : 'N/A'
                ];
            });

        // 2. Weak Points (Specific to Student)
        $weakPoints = $this->getWeakPoints($tenantId, $studentId);

        // 3. Stats & Percentile
        $myAvg = $history->avg('score') ?? 0;
        $totalAttempts = $history->count();

        $allStudentAvgs = DB::table('attempts')
            ->select(DB::raw('AVG(score) as avg_score'))
            ->where('tenant_id', $tenantId)
            ->groupBy('student_id')
            ->pluck('avg_score');

        $studentsBelowMe = $allStudentAvgs->filter(fn($s) => $s < $myAvg)->count();
        $totalStudents = $allStudentAvgs->count();
        $percentile = $totalStudents > 0 ? round(($studentsBelowMe / $totalStudents) * 100) : 0;

        if($totalStudents == 1 && $totalAttempts > 0) $percentile = 100;

        return response()->json([
            'student' => [
                'name' => $student->user->name,
                'email' => $student->user->email,
                'reg_no' => $student->reg_no,
                'joined_at' => $student->created_at->format('M Y'),
            ],
            'stats' => [
                'avg_score' => round($myAvg),
                'total_attempts' => $totalAttempts,
                'percentile' => $percentile,
                'status' => $myAvg < 50 ? 'At Risk' : ($myAvg > 80 ? 'Exceling' : 'On Track')
            ],
            'history' => $history,
            'weak_points' => $weakPoints
        ]);
    }

    /**
     * Helper to calculate weak points (Global or Per Student)
     */
    private function getWeakPoints($tenantId, $studentId = null)
    {
        $query = DB::table('responses')
            ->join('attempts', 'responses.attempt_id', '=', 'attempts.id')
            ->join('questions', 'responses.question_id', '=', 'questions.id')
            ->join('options', 'responses.option_id', '=', 'options.id')
            ->where('attempts.tenant_id', $tenantId)
            ->whereNotNull('questions.topic');

        if ($studentId) {
            $query->where('attempts.student_id', $studentId);
        }

        return $query->select(
                'questions.topic',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('AVG(options.is_correct) * 100 as avg_score')
            )
            ->groupBy('questions.topic')
            ->orderBy('avg_score', 'asc')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                $score = round($item->avg_score);
                $difficulty = $score < 50 ? 'High' : ($score < 75 ? 'Medium' : 'Low');
                return [
                    'topic' => $item->topic,
                    'avg_score' => $score,
                    'total_attempts' => $item->total_attempts,
                    'difficulty_index' => $difficulty
                ];
            });
    }
}
