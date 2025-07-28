<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Progress Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2, h3 {
            margin-bottom: 10px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .field-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .field {
            width: 48%;
            margin-bottom: 10px;
        }

        .field label {
            font-weight: bold;
            display: block;
        }

        .comment {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f5f5f5;
            margin-top: 20px;
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