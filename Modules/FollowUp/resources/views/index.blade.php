@extends('layouts.app')

@section('content')
<div id="app">
    <follow-up-list></follow-up-list>
</div>
@endsection

@push('scripts')
    @vite(['Modules/FollowUp/resources/assets/js/app.js'])
@endpush
