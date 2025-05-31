 
@extends('layouts.nav')

@section('content')
<div class="p-8 max-w-[1400px] mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-[1.8rem] font-semibold text-[#2c3e50] mb-4 sm:mb-0">Intern Grades</h1>
        <div class="flex gap-4">
            <a href="{{ route('training.intern-grades.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-[#22bbea] text-white text-sm font-medium rounded-md hover:bg-[#1a9bc7] transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Intern Grade
            </a>
        </div>
    </div>

    <!-- School Filter -->
    <div class="mb-6">
        <form action="{{ route('training.intern-grades.index') }}" method="get" class="flex items-center gap-4">
            <label for="school_filter" class="text-sm font-medium text-gray-700">Filter by School:</label>
            <select name="school_filter" id="school_filter" 
                    class="w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#22bbea] focus:border-[#22bbea]"
                    onchange="this.form.submit();">
                <option value="">-- Select School --</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->school_id }}" {{ (request('school_filter') == $school->school_id) ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-md bg-[#dcfce7] border border-[#bbf7d0] text-[#166534]">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 rounded-md bg-[#fee2e2] border border-[#fecaca] text-[#dc2626]">
            {{ session('error') }}
        </div>
    @endif

    <!-- Grades Tables -->
    @if (count($groupedGrades) > 0)
        @foreach ($groupedGrades as $classId => $grades)
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Class: {{ $grades->first()->class_name }}</h2>
                <div class="w-full">
                    <table class="w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-[#22bbea] text-white">
                                <th class="w-[10%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Student ID</th>
                                <th class="w-[15%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Student Name</th>
                                <th class="w-[15%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Company</th>
                                <th class="w-[12%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">ICT Learning</th>
                                <th class="w-[12%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">21st Century Skills</th>
                                <th class="w-[12%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Expected Outputs</th>
                                <th class="w-[8%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Final Grade</th>
                                <th class="w-[8%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th class="w-[8%] px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($grades as $grade)
                                <tr class="hover:bg-[#f8f9fa]">
                                    <td class="px-3 py-4 text-sm text-gray-900 truncate">{{ $grade->student_id ?? 'N/A' }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-900 truncate">{{ $grade->intern_name }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-900 truncate">{{ $grade->company_name }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-900 text-center">{{ $grade->ict_learning_competency }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-900 text-center">{{ $grade->twenty_first_century_skills }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-900 text-center">{{ $grade->expected_outputs_deliverables }}</td>
                                    <td class="px-3 py-4 text-sm text-center">
                                        @php
                                            $gradeClass = match($grade->final_grade) {
                                                1 => 'bg-[#dcfce7] text-[#166534]',
                                                2 => 'bg-[#fef9c3] text-[#854d0e]',
                                                3 => 'bg-[#fee2e2] text-[#991b1b]',
                                                4 => 'bg-[#fee2e2] text-[#7f1d1d]',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $gradeClass }}">
                                            {{ $grade->final_grade }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($grade->status === 'Fully Achieved') bg-green-100 text-green-800
                                            @elseif($grade->status === 'Partially Achieved') bg-blue-100 text-blue-800
                                            @elseif($grade->status === 'Barely Achieved') bg-yellow-100 text-yellow-800
                                            @elseif($grade->status === 'No Achievement') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $grade->status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('training.intern-grades.edit', $grade->id) }}" 
                                               class="p-1.5 bg-[#e0f2fe] text-[#0369a1] rounded-md hover:bg-[#bae6fd] transition-colors duration-200"
                                               title="Edit Grade">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('training.intern-grades.destroy', $grade->id) }}" 
                                                  method="post" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('Are you sure you want to delete this grade?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 bg-[#fee2e2] text-[#dc2626] rounded-md hover:bg-[#fecaca] transition-colors duration-200"
                                                        title="Delete Grade">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No grades found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new grade entry.</p>
        </div>
    @endif
</div>
@endsection 
