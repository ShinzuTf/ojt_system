@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">📄 Test Document Generation</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Generate a Document From Template</h2>
            
            <form id="testForm" class="space-y-4">
                @csrf
                
                <div>
                    <label for="student_id" class="block text-sm font-medium mb-2">Select Student:</label>
                    <select id="student_id" name="student_id" class="w-full px-4 py-2 border rounded" required onchange="previewStudentData()">
                        <option value="">-- Choose a Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->fname }} {{ $student->lname }}
                                @if($student->ojtInfo)
                                    ({{ $student->ojtInfo->student_number }})
                                @else
                                    ⚠️ No OJT Info
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="template" class="block text-sm font-medium mb-2">Select Template:</label>
                    <select id="template" name="template" class="w-full px-4 py-2 border rounded" required>
                        <option value="">-- Choose a Template --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template }}">{{ str_replace('.docx', '', $template) }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                    📥 Generate Document
                </button>
            </form>
        </div>

        <div id="previewSection" class="hidden bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">📋 Student Data That Will Be Used:</h2>
            <div id="previewContent" class="bg-gray-50 p-4 rounded overflow-auto max-h-96"></div>
        </div>

        <div id="resultSection" class="hidden bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">✅ Generation Result:</h2>
            <div id="resultContent"></div>
        </div>
    </div>
</div>

<script>
    function previewStudentData() {
        const studentId = document.getElementById('student_id').value;
        if (!studentId) return;

        fetch(`{{ route('test.document.preview') }}?student_id=${studentId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const html = `
                        <div class="space-y-2 text-sm">
                            <p><strong>Student:</strong> ${data.student.fname} ${data.student.lname}</p>
                            <p><strong>Email:</strong> ${data.student.email}</p>
                            <hr class="my-3">
                            <p class="font-bold">Available Placeholders:</p>
                            <ul class="space-y-1">
                        ` + Object.entries(data.data).map(([key, val]) => 
                            `<li><code class="bg-gray-200 px-2 py-1 rounded">\${${key}}</code> = <strong>${val}</strong></li>`
                        ).join('') + `
                            </ul>
                        </div>
                    `;
                    document.getElementById('previewContent').innerHTML = html;
                    document.getElementById('previewSection').classList.remove('hidden');
                }
            })
            .catch(e => console.error(e));
    }

    document.getElementById('testForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('{{ route('test.document.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                const html = `
                    <div class="space-y-4">
                        <div class="bg-green-50 border border-green-200 p-4 rounded">
                            <p class="text-green-700 font-bold">✅ Document generated successfully!</p>
                        </div>
                        <div class="space-y-2 text-sm">
                            <p><strong>Student:</strong> ${result.student_name}</p>
                            <p><strong>Template:</strong> ${result.template}</p>
                            <p><strong>File:</strong> ${result.file_path.split('/').pop()}</p>
                        </div>
                        <a href="${result.download_url}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                            ⬇️ Download Generated Document
                        </a>
                    </div>
                `;
                document.getElementById('resultContent').innerHTML = html;
                document.getElementById('resultSection').classList.remove('hidden');
            } else {
                document.getElementById('resultContent').innerHTML = `
                    <div class="bg-red-50 border border-red-200 p-4 rounded">
                        <p class="text-red-700"><strong>❌ Error:</strong> ${result.message}</p>
                    </div>
                `;
                document.getElementById('resultSection').classList.remove('hidden');
            }
        } catch (e) {
            document.getElementById('resultContent').innerHTML = `
                <div class="bg-red-50 border border-red-200 p-4 rounded">
                    <p class="text-red-700"><strong>❌ Error:</strong> ${e.message}</p>
                </div>
            `;
            document.getElementById('resultSection').classList.remove('hidden');
        }
    });
</script>
@endsection
