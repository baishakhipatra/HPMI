<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Progress Report</title>
</head>
<body>

    <h2>Student Progress Report - {{ $student->student_name }}</h2>
    <p><strong>Session:</strong> {{ $current_session }}</p>

    @foreach ($student_progress_category as $fieldName => $values)
        <div class="section">
            <div class="section-title">{{ ucwords($fieldName) }}</div>
            <div class="field-row">
                @foreach ($values as $item)
                
                    <div class="field">
                        <label>{{ ucwords($item->value) }}</label>
                        <div>
                            {{ $savedScores[ucwords($fieldName)][ucwords($item->value)] ?? 'N/A' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @php
        $comment = $getDetails->first()->add_comments ?? null;
    @endphp

    @if ($comment)
        <div class="section">
            <div class="section-title">Comment</div>
            <div class="comment">
                {{ ucfirst($comment) }}
            </div>
        </div>
    @endif

</body>
</html>