<?php

use function Livewire\Volt\{layout};

layout('components.layouts.blog');
?>

<div>
    <p>
        We have noticed that you haven't verified your email yet.
    </p>
    @if (session('status'))
        <p>
            {{ session('status') }}
        </p>
    @else
        <p>
            Please verify your email by clicking on the link we have sent you via email.
        </p>
    @endif
    <span>Didn't received a link ?</span>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">Resend</button>
    </form>
</div>