@extends('superadmin.layout')
@section('content')
<div class="max-w-5xl mx-auto p-8">
	<h1 class="text-3xl font-extrabold text-green-900 mb-8 flex items-center gap-3">
		<i class="bi bi-flag-fill text-red-600"></i> User Reports
	</h1>
	<div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
		<table class="min-w-full table-auto">
			<thead>
				<tr class="bg-gradient-to-r from-green-700 to-lime-600 text-white">
					<th class="px-6 py-3 text-left font-bold"><i class="bi bi-person-x-fill mr-2"></i>Reported User</th>
					<th class="px-6 py-3 text-left font-bold"><i class="bi bi-person-check-fill mr-2"></i>Reporter</th>
					<th class="px-6 py-3 text-left font-bold"><i class="bi bi-chat-left-text-fill mr-2"></i>Reason</th>
					<th class="px-6 py-3 text-left font-bold"><i class="bi bi-calendar-event-fill mr-2"></i>Date</th>
				</tr>
			</thead>
			<tbody>
				@forelse($reports as $report)
				<tr class="border-b hover:bg-lime-50 transition">
					<td class="px-6 py-4 font-semibold text-green-900">{{ $report->user->name ?? 'Unknown' }}</td>
					<td class="px-6 py-4 font-semibold text-lime-900">{{ $report->reporter->name ?? 'Unknown' }}</td>
					<td class="px-6 py-4">
						<span class="bg-red-50 text-red-700 px-3 py-2 rounded-lg shadow text-sm font-medium">{{ $report->reason }}</span>
					</td>
					<td class="px-6 py-4">
						<span class="inline-flex items-center gap-2 text-green-700 font-semibold">
							<i class="bi bi-calendar-event"></i> {{ $report->created_at->format('M d, Y h:i A') }}
						</span>
					</td>
				</tr>
				@empty
				<tr><td colspan="4" class="text-center text-gray-400 py-6 text-lg">No reports found.</td></tr>
				@endforelse
			</tbody>
		</table>
		<div class="mt-6">{{ $reports->links() }}</div>
	</div>
</div>
@endsection
