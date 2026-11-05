<div class="space-y-3">
    @if($errors !== [])
        <div class="rounded-lg border border-danger-300 bg-danger-50 p-3 text-sm text-danger-700">
            <p class="font-semibold">Fix these issues before importing:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @else
        <p class="text-sm font-medium">{{ count($rows) }} student(s) will be added and enrolled in the active school year.</p>
    @endif

    @if($rows !== [])
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr><th class="p-2">Student ID</th><th class="p-2">Student</th><th class="p-2">Grade</th><th class="p-2">Gender</th></tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-2">{{ $row['id'] }}</td><td class="p-2">{{ $row['name'] }}</td><td class="p-2">{{ $row['grade_label'] }}</td><td class="p-2">{{ $row['gender'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
