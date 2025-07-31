<!DOCTYPE html>
<html>
<head>
    <title>Progress Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 30px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; margin-bottom: 30px; font-size: 14px; }

        .section { margin-bottom: 25px; }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .field-row {
            display: flex;
            flex-wrap: wrap;
        }

        .field {
            width: 45%;
            margin: 5px 2.5%;
        }

        .field label {
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .comment {
            margin-top: 15px;
            padding: 10px;
            border: 1px dashed #000;
            font-style: italic;
        }
    </style>
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
                        @php
                            $scoreValue = $savedScores[ucwords($fieldName)][ucwords($item->value)] ?? null;
                            $label = $scoreValue ? getProgressScoreOptions()[$scoreValue] ?? $scoreValue : 'N/A';
                        @endphp
                        <div>{{ $label }}</div>
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
