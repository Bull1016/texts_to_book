@extends('layouts.app')

@section('title', $report->title . ' - Texts to Book')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @livewire('report-show', ['report' => $report])
    </div>
</div>
@endsection
